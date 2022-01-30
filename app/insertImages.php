<?php
include("config.php");

$images = glob('./assets/images/image/*');


function insertImage($url, $src, $alt, $title)
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

foreach ($images as $imageUrl) {
    insertImage("", $imageUrl, "", "");
}
