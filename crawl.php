<?php
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function linkExists($url) //urlのduplicateを防ぐ
{
	global $con;

	$query = $con->prepare("SELECT * FROM sites WHERE url = :url");

	$query->bindParam(":url", $url);
	$query->execute();
	return $query->rowCount() != 0;
}

function insertLink($url, $title, $description, $keywords) //sitesテーブルにinsert
{
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

function insertImage($url, $src, $alt, $title) //imagesテーブルにinsert
{
	global $con;

	$query = $con->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
							VALUES(:siteUrl, :imageUrl, :alt, :title)");

	$query->bindParam(":siteUrl", $url);
	$query->bindParam(":imageUrl", $src);
	$query->bindParam(":alt", $alt);
	$query->bindParam(":title", $title);

	return $query->execute();
}
function createLink($src, $url) //anchorタグのhrefをグローバルリンクに変換する
{

	$scheme = parse_url($url)["scheme"]; // http
	$host = parse_url($url)["host"]; // www.google.com

	if (substr($src, 0, 2) == "//") {
		$src =  $scheme . ":" . $src;
	} else if (substr($src, 0, 1) == "/") {
		$src = $scheme . "://" . $host . $src;
	} else if (substr($src, 0, 2) == "./") {
		$src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
	} else if (substr($src, 0, 3) == "../") {
		$src = $scheme . "://" . $host . "/" . $src;
	} else if (substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
		$src = $scheme . "://" . $host . "/" . $src;
	}

	return $src;
}

function getDetails($url)
{ /* get title,meta,img element  */
	global $alreadyFoundImages;

	$parser = new DomDocumentParser($url);


	/** meta tag */
	$titleArray = $parser->getTitleTags(); //NodeList

	if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
		return;
	}

	$title = $titleArray->item(0)->nodeValue;    //nodeListの値だけ取得
	$title = str_replace("\n", "", $title);   //改行を空文字に


	if ($title == "") {
		return;
	}

	$description = "";
	$keywords = "";

	$metasArray = $parser->getMetatags(); //NodeList

	foreach ($metasArray as $meta) {

		if ($meta->getAttribute("name") == "description") {
			$description = $meta->getAttribute("content");
		}

		if ($meta->getAttribute("name") == "keywords") {
			$keywords = $meta->getAttribute("content");
		}
	}

	$description = str_replace("\n", "", $description);
	$keywords = str_replace("\n", "", $keywords);

	/** sitesテーブルにinsert */
	if (linkExists($url)) {
		echo "$url already exists<br>";
	} else if (insertLink($url, $title, $description, $keywords)) {
		echo "SUCCESS : $url<br>";
	} else {
		echo "ERROR: Failed to insert $url<br>";
	}

	/**img tag */
	$imageArray = $parser->getImages(); //NodeList
	foreach ($imageArray as $image) {
		$src = $image->getAttribute("src");
		$alt = $image->getAttribute("alt");
		$title = $image->getAttribute("title");

		if (!$title && !$alt) {
			continue;
		}
		$src = createLink($src, $url); //absolute pathを取得

		//Insert the image
		if (!in_array($src, $alreadyFoundImages)) {
			$alreadyFoundImages[] = $src;
			insertImage($url, $src, $alt, $title);
		}
	}
}

function followLinks($url)
{ /* get anchor element */

	global $alreadyCrawled;
	global $crawling;

	$parser = new DomDocumentParser($url);

	$linkList = $parser->getLinks();  /* NodeListはforeachで回せる */

	foreach ($linkList as $link) { /* 1階層目のanchor tagの取得 */
		$href = $link->getAttribute("href");

		if (strpos($href, "#") !== false) {  /* #が含まれていれば.. */
			continue;
		} else if (substr($href, 0, 11) == "javascript:") {  /*jsで生成されたlinkかどうか*/
			continue;
		}


		$href = createLink($href, $url); /*absolute path*/


		if (!in_array($href, $alreadyCrawled)) {
			$alreadyCrawled[] = $href;
			$crawling[] = $href;
			// Insert $href
			getDetails($href); //tableにinsert
		}
	}

	array_shift($crawling); /* 取得し終えたurl */

	foreach ($crawling as $site) {/* 2階層目以降のanchor tagの取得start */
		followLinks($site);
	}
}

$startUrl = "https://www.shutterstock.com/search/rooney";
followLinks($startUrl);
