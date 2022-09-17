<?php
set_time_limit(10);
ini_set('max_execution_time', 10);
include("config.php");
include("classes/DomDocumentParser.php");
$stdout= fopen( 'php://stdout', 'w' );
$stderr = fopen( 'php://stderr', 'w' );

if($argc > 2 || $argc < 2){
  echo "引数を一つだけ指定してください";
  exit(0);
}
$pattern = '/https?:\/{2}[\w\/:%#\$&\?\(\)~\.=\+\-]+/';
if(!preg_match($pattern, $argv[1])){
  echo "有効なURLを入力してください";
  exit(0);
} else {
  fwrite($stdout, "$argv[1]をクローリング開始\n");
}

$validateLinksForSites =	'/\/news\/|\/Articles\/|\/News\/|www\.bbc\.com\/sport\/football\/[0-9]{3,}/';
$validateLinksForImages = '/\.jpe?g/';

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function linkExists($url)
{
	global $con;
	$query = $con->prepare("SELECT * FROM sites WHERE url = :url");
	$query->bindParam(":url", $url);
	$query->execute();
	return $query->rowCount() != 0;
}

function insertLink($url, $title, $description, $keywords)
{
	global $con;
	global $validateLinksForSites;
	global $stdout;

	if(preg_match($validateLinksForSites, $url) !== 1){
		fwrite( $stdout, "ERROR(sites): keyword isn\'t included in $url\n" );
		return false;
	} 

	$query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
							VALUES(:url, :title, :description, :keywords)");

	$query->bindParam(":url", $url);
	$query->bindParam(":title", $title);
	$query->bindParam(":description", $description);
	$query->bindParam(":keywords", $keywords);

	return $query->execute();
}

function insertImage($url, $src, $alt, $title)
{
	global $con;
	global $validateLinksForImages;
	global $stdout;
	if(preg_match($validateLinksForImages, $src) !== 1){
		fwrite( $stdout, "ERROR(images): $src doesn\'t end with the extension \n" );
		return false;
	}

	$query = $con->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
							VALUES(:siteUrl, :imageUrl, :alt, :title)");
	$query->bindParam(":siteUrl", $url);
	$query->bindParam(":imageUrl", $src);
	$query->bindParam(":alt", $alt);
	$query->bindParam(":title", $title);
	return $query->execute();
}
function createLink($src, $url)
{
	$scheme = parse_url($url)["scheme"];
	$host = parse_url($url)["host"];

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
{
	global $alreadyFoundImages;
	global $stdout;
	$parser = new DomDocumentParser($url);

	$titleArray = $parser->getTitleTags();
	if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
		return;
	}
	$siteTitle = $titleArray->item(0)->nodeValue;
	$siteTitle = str_replace("\n", "", $siteTitle);
	if ($siteTitle == "") {
		return;
	}
	
	$description = "";
	$keywords = "";
	$metasArray = $parser->getMetatags();
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

	if (linkExists($url)) {
		fwrite( $stdout, "$url already exists\n" );
	}else if (insertLink($url, $siteTitle, $description, $keywords)) {
		fwrite( $stdout,"SUCCESS(sites) : $url\n" );
	} else {
		fwrite( $stdout,"ERROR(sites): Failed to insert $url\n" );
	}

	// Get the Image
	$imageArray = $parser->getImages();
	foreach ($imageArray as $image) {

		$src = $image->getAttribute("src");
		$alt = $image->getAttribute("alt");
		$title = $image->getAttribute("title");

		if (!$title && !$alt) {
			$title = $siteTitle;
		}
		$src = createLink($src, $url);
		$info = getimagesize($src);
		if($info[0] < 500 && $info[1] < 500){
			fwrite($stdout, "ERROR(images): Image attributes don\'t match the criteria INFO($info[3])\n");
			continue;
		}
		$mimeType = $info["mime"];
		if($mimeType !== "image/jpeg" ){
			fwrite($stdout, "ERROR(images): Image extension is not jpeg INFO($mimeType)\n");
			continue;
		}
		if (!in_array($src, $alreadyFoundImages)) {
			$alreadyFoundImages[] = $src;
			if(insertImage($url, $src, $alt, $title)){
				fwrite( $stdout,"SUCCESS(images) : $src\n" );
			}else {
				fwrite($stdout, "ERROR(images): Failed to insert $url\n");
			}
		}else {
			fwrite($stdout, "ERROR(images): Already exists images $src\n");
		}
	}
}

function followLinks($url)
{
	global $alreadyCrawled;
	global $crawling;
	global $stdout;
	global $validateLinksForSites;
	
	$parser = new DomDocumentParser($url);
	$linkList = $parser->getLinks();
	foreach ($linkList as $link) {
		$href = $link->getAttribute("href");
		if (strpos($href, "#") !== false) {
			continue;
		} else if (substr($href, 0, 11) == "javascript:") {
			continue;
		} else {
			$href = strtok($href, "?");
		}
		
		$href = createLink($href, $url);
		if (!in_array($href, $alreadyCrawled) 
		&& preg_match($validateLinksForSites, $href) === 1
		) {
			$alreadyCrawled[] = $href;
			$crawling[] = $href;
			getDetails($href);
		}
	}
	array_shift($crawling);
	foreach ($crawling as $site) {
		followLinks($site);
	}
}
$startUrl = $argv[1];
followLinks($startUrl);
fwrite($stdout,"グローリング終了\n");
exit(0);