<?php
include("config.php");
include("classes/DomDocumentParser.php");
$stdout = fopen('php://stdout', 'w');
$stderr = fopen('php://stderr', 'w');

if (!isset($_SESSION["username"])) {
	header("Location: login.php");
}

if (!isset($_POST["crawlSite"]) || $_POST["crawlSite"] == "") {
	echo "<h1>No Link Passed to page</h1>";
	fwrite($stderr, "No Link Passed to page\n");
	exit;
}

$validateLinksForSites =	'/\/news\/|\/Articles\/|\/News\/|\/football\/[0-9]{3,}/';
$validateLinksForImages = '/\.jpe?g/';

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

function insertLink($url, $title, $description, $keywords, $thumbnailImageLink) //sitesテーブルにinsert
{
	global $con;
	global $validateLinksForSites;
	global $stdout;

	if (preg_match($validateLinksForSites, $url) !== 1) {
		fwrite($stdout, "ERROR(sites): keyword isn\'t included in $url\n");
		return false;
	}

	$query = $con->prepare("INSERT INTO sites(url, title, description, keywords, thumbnailImageLink)
							VALUES(:url, :title, :description, :keywords, :thumbnailImageLink)");

	$query->bindParam(":url", $url);
	$query->bindParam(":title", $title);
	$query->bindParam(":description", $description);
	$query->bindParam(":keywords", $keywords);
	$query->bindParam(":thumbnailImageLink", $thumbnailImageLink);

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
	global $validateLinksForImages;
	global $stdout;

	if (preg_match($validateLinksForImages, $src) !== 1) {
		fwrite($stdout, "ERROR(images): $src doesn\'t end with the extension \n");
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
	global $stdout;
	$parser = new DomDocumentParser($url);

	// Get The Sites info
	$titleArray = $parser->getTitleTags();
	if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
		return;
	}
	$siteTitle = $titleArray->item(0)->nodeValue;  //nodeListの値だけ取得
	$siteTitle = str_replace("\n", "", $siteTitle);   //改行を空文字に
	if ($siteTitle == "") {
		return;
	}

	$description = "";
	$keywords = "";
	$thumbnailImageLink = "";
	$metasArray = $parser->getMetatags();
	foreach ($metasArray as $meta) {

		if ($meta->getAttribute("name") == "description") {
			$description = $meta->getAttribute("content");
		}

		if ($meta->getAttribute("name") == "keywords") {
			$keywords = $meta->getAttribute("content");
		}

		if ($meta->getAttribute("property") == "og:image") {
			$thumbnailImageLink = $meta->getAttribute("content");
		}
	}
	if ($thumbnailImageLink == "") {
		$linksArray = $parser->getLinkTags();
		foreach ($linksArray as $link) {
			if ($link->getAttribute("rel") == "preload" && $link->getAttribute("as") == "image") {
				$thumbnailImageLink = $link->getAttribute("href");
				break;
			}
		}
	}
	$description = str_replace("\n", "", $description);
	$keywords = str_replace("\n", "", $keywords);

	// Insert the sites
	if (linkExists($url)) {
		fwrite($stdout, "$url already exists\n");
	} else if (insertLink($url, $siteTitle, $description, $keywords, $thumbnailImageLink)) {
		fwrite($stdout, "SUCCESS(sites) : $url\n");
	} else {
		fwrite($stdout, "ERROR(sites): Failed to insert $url\n");
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
		if ($info[0] < 500 && $info[1] < 500) {
			fwrite($stdout, "ERROR(images): Image attributes don\'t match the criteria INFO($info[3])\n");
			continue;
		}
		$mimeType = $info["mime"];
		if ($mimeType !== "image/jpeg") {
			fwrite($stdout, "ERROR(images): Image extension is not jpeg INFO($mimeType)\n");
			continue;
		}
		if (!in_array($src, $alreadyFoundImages)) {
			$alreadyFoundImages[] = $src;
			if (insertImage($url, $src, $alt, $title)) {
				fwrite($stdout, "SUCCESS(images) : $src\n");
			} else {
				fwrite($stdout, "ERROR(images): Failed to insert $url\n");
			}
		} else {
			fwrite($stdout, "ERROR(images): Already exists images $src\n");
		}
	}
}

function followLinks($url)
{ /* get anchor element */

	global $alreadyCrawled;
	global $crawling;
	global $stdout;
	global $validateLinksForSites;

	$parser = new DomDocumentParser($url);
	$linkList = $parser->getLinks();  /* NodeListはforeachで回せる */

	foreach ($linkList as $link) { /* 1階層目のanchor tagの取得 */
		$href = $link->getAttribute("href");
		if (strpos($href, "#") !== false) {  /* #が含まれていれば.. */
			continue;
		} else if (substr($href, 0, 11) == "javascript:") {  /*jsで生成されたlinkかどうか*/
			continue;
		} else {
			// Remove ? parameters from url
			$href = strtok($href, "?");
		}

		$href = createLink($href, $url); /*absolute path*/
		if (
			!in_array($href, $alreadyCrawled)
			&& preg_match($validateLinksForSites, $href) === 1
		) {
			$alreadyCrawled[] = $href;
			$crawling[] = $href;
			// Insert $href(sites & images)
			getDetails($href);
		}
	}

	array_shift($crawling); /* 取得し終えたurl */

	foreach ($crawling as $site) {/* 2階層目以降のanchor tagの取得start */
		followLinks($site);
	}
}
$startUrl = $_POST["crawlSite"];
// $startUrl = "https://onefootball.com/en/home";

followLinks($startUrl);
fwrite($stdout, "グローリング終了\n");
exit(0);