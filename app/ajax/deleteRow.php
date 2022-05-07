<?php
include("../config.php");

if ($_POST["mode"] == "delete") {

    $query = $con->prepare("DELETE FROM sites");
    $query->execute();
    $query = $con->prepare("DELETE FROM images");

    $query->execute();
    echo "success!";
} else {
    echo "No Link Passed to page";
}