<?php
include("config.php");

    if(isset($_GET["term"])){
        $term = $_GET["term"];

    }else{
        exit("you must entr a search term");
    }
    $type = isset($_GET["type"]) ? $_GET["type"] : "Sites";

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Welcome to Noodle</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <div class="headerContent">
                <div class="logoContainer">
                    <a href="index.php">
                        <img src="assets/images/festisite_google.png">
                    </a>
                </div>
                <div class="searchContainer">
                    <form action="search.php" method="GET">
                        <div class="searchBarContainer">
                            <input class="searchBox" type="text" name="term">
                            <button class="searchButton"><img src="assets/images/icons/search.png"></button>
                        </div>
                    </form>
                </div>

            </div>
            <div class="tabsContainer">
                <ul class="tabList">
                    <li class="<? echo $type == 'sites' ? 'active' : '' ?>">
                        <a href='<?php echo "search.php?term=$term&type=sites" ; ?>'>Sites</a>
                    </li>
                    <li class="<? echo $type == 'images' ? 'active' : '' ?>">
                        <a href='<?php echo "search.php?term=$term&type=images" ; ?>'>Images</a>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</body>

</html>
