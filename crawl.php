<?php
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();
function insertImage($url, $src, $alt, $title) {
	global $con;

	$query = $con->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
							VALUES(:siteUrl, :imageUrl, :alt, :title)");

	$query->bindParam(":siteUrl", $url);
	$query->bindParam(":imageUrl", $src);
	$query->bindParam(":alt", $alt);
	$query->bindParam(":title", $title);

	$query->execute();
}

function linkExists($url) {
	global $con;

	$query = $con->prepare("SELECT * FROM sites WHERE url = :url");

    $query->bindParam(":url", $url);
    $query->execute();
	return $query->rowCount() != 0;
}

function insertLink($url, $title, $description, $keywords) {
	global $con;

	$query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
							VALUES(:url, :title, :description, :keywords)");

	$query->bindParam(":url", $url);
	$query->bindParam(":title", $title);
	$query->bindParam(":description", $description);
	$query->bindParam(":keywords", $keywords);

	return $query->execute();
}

/*　パターン
1,  //www.google.com -> [scheme] + // + [host]
2,  /about/aboutUs.php -> [scheme] + // + [host]+ /about/aboutUs.php
3,  ./about/aboutUs.php -> [scheme] + // + [host] + dirname(....["path]) + /about/aboutUs.php
4,  ../about/aboutUs.php ->[scheme] + // + [host] + / + about/aboutUs.php
5,  about/aboutUs.php ->[scheme] + // + [host] + / + about/aboutUs.php
*/

function createLink($src, $url) {

	$scheme = parse_url($url)["scheme"]; // http
	$host = parse_url($url)["host"]; // www.reecekenney.com
	
	if(substr($src, 0, 2) == "//") {
		$src =  $scheme . ":" . $src;
	}
	else if(substr($src, 0, 1) == "/") {
		$src = $scheme . "://" . $host . $src;
	}
	else if(substr($src, 0, 2) == "./") {
		$src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
	}
	else if(substr($src, 0, 3) == "../") {
		$src = $scheme . "://" . $host . "/" . $src;
	}
	else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
		$src = $scheme . "://" . $host . "/" . $src;
	}

	return $src;
}

function getDetails($url) {
    global $alreadyFoundImages;

	$parser = new DomDocumentParser($url);

	$titleArray = $parser->getTitleTags(); //NodeList

	if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
		return;
	}

	$title = $titleArray->item(0)->nodeValue;    //nodeListの値だけ取得
	$title = str_replace("\n", "", $title);   //改行を空文字に


	if($title == "") {
		return;
	}

	$description = "";
	$keywords = "";

	$metasArray = $parser->getMetatags(); //NodeList

	foreach($metasArray as $meta) {

		if($meta->getAttribute("name") == "description") {
			$description = $meta->getAttribute("content");
		}

		if($meta->getAttribute("name") == "keywords") {
			$keywords = $meta->getAttribute("content");
		}
	}

	$description = str_replace("\n", "", $description);
	$keywords = str_replace("\n", "", $keywords);

    if(linkExists($url)){
        echo "$url already exists<br>";
    } else if (	insertLink($url, $title, $description, $keywords)){
        echo "SUCCESS : $url<br>";
    }else{
        echo "ERROR: Failed to insert $url<br>";
    }
    $imageArray = $parser->getImages(); //NodeList
    foreach($imageArray as $image){
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if(!$title && !$alt){
            continue;
        }
        $src = createLink($src,$url); //absolute pathを取得
        if(!in_array($src, $alreadyFoundImages)){
            $alreadyFoundImages[] = $src;
            //Insert the image
            insertImage($url,$src,$alt,$title);
        }

    }

}

function followLinks($url) {

	global $alreadyCrawled;
	global $crawling;

	$parser = new DomDocumentParser($url);

	$linkList = $parser->getLinks();  /* NodeListはforeachで回せる */

	foreach($linkList as $link) {
		$href = $link->getAttribute("href");

		if(strpos($href, "#") !== false) {  /* #が含まれているかどうか */
			continue;
		}
		else if(substr($href, 0, 11) == "javascript:") {  /*jsで生成されたlinkかどうか*/
			continue;
		}


		$href = createLink($href, $url);


		if(!in_array($href, $alreadyCrawled)) {
			$alreadyCrawled[] = $href;
			$crawling[] = $href;
			// Insert $href
			getDetails($href);
		}
	}

	array_shift($crawling);

	foreach($crawling as $site) {
		followLinks($site);
	}

}

$startUrl = "http://www.bbc.com";
followLinks($startUrl);
?>