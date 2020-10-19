<?php
require_once("config.php");
require_once("classes/SiteResultsProvider.php");
require_once("classes/ImageResultsProvider.php");



if (isset($_GET["term"])) {
    $term = $_GET["term"];
} else {
    exit("You must enter a search term");
}
$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Welcome to Doodle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
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
                    <?php if ($type == "sites" || $type == "images") { ?>
                        <form action="search.php" method="GET">
                            <div class="searchBarContainer">
                                <input type="hidden" name="type" value="<?php echo $type; ?>">
                                <input class="searchBox" type="text" name="term" value="<?php echo $term; ?>" autocomplete="off">
                                <button class="searchButton"><img src="assets/images/icons/search.png"></button>
                            </div>
                        </form>
                    <?php } elseif ($type == "movies") { ?>
                        <form action="search.php" method="GET" id="search-form" name="search-form">
                            <input type="hidden" name="type" value="<?php echo $type; ?>">
                            <div class="searchBarContainer">
                                <input class="searchBox search-field" type="search" id="query" name="term" value="<?php echo $term; ?>">

                                <button class="searchButton" type="submit" id="search-btn" name="search-btn"><img src="assets/images/icons/search.png"></button>
                            </div>
                        </form>

                    <?php } elseif ($type == "wiki") { ?>

                        <form action="search.php" method="GET">
                            <input type="hidden" name="type" value="<?php echo $type; ?>">
                            <div class="searchBarContainer">
                                <input class="searchBox" id="search" name="term" type="search" value="<?php echo $term; ?>" />
                                <button class="searchButton" type="submit" id="searchBtn"><img src="assets/images/icons/search.png"></button>
                            </div>

                        </form>

                    <?php } ?>
                </div>

            </div>
            <div class="tabsContainer">
                <ul class="tabList">
                    <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
                        <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a>
                    </li>
                    <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
                        <a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a>
                    </li>
                    <li class="<?php echo $type == 'movies' ? 'active' : '' ?>">
                        <a href='<?php echo "search.php?term=$term&type=movies"; ?>'>Movies</a>
                    </li>
                    <li class="<?php echo $type == 'wiki' ? 'active' : '' ?>">
                        <a href='<?php echo "search.php?term=$term&type=wiki"; ?>'>Wikipedia</a>
                    </li>
                </ul>

            </div>
        </div>




        <div class="mainResultsSection">
            <?php
            if ($type == "sites") {
                $resultsProvider = new SiteResultsProviders($con);
                $pageSize = 20;
                $numResults = $resultsProvider->getNumResults($term);

                echo "<p class='resultsCount'>$numResults results found</p>";

                echo $resultsProvider->getResultsHtml($page, $pageSize, $term);
            } elseif ($type == "images") {
                $resultsProvider = new ImageResultsProviders($con);
                $pageSize = 30;
                $numResults = $resultsProvider->getNumResults($term);

                echo "<p class='resultsCount'>$numResults results found</p>";

                echo $resultsProvider->getResultsHtml($page, $pageSize, $term);
            } elseif ($type == "movies") {
                echo '<div class="siteResults"></div>';
            } elseif ($type == "wiki") {
                echo '
                <p class="resultsCount"></p>
                <div class="siteResults">
                <div class="resultContainer"></div>
                </div>';
            }

            ?>

        </div>
        <div class="paginationContainer">
            <?php if ($type == "sites" || $type == "images") { ?>
                <div class="pageButtons">
                    <div class="pageNumberContainer">
                        <img src="assets/images/pageStart.png">
                    </div>
                    <?php
                    $pagesToShow = 10; /* oの表示数のmax */
                    $numPages = ceil($numResults / $pageSize); /*小数点切り上げ 総ページ数*/
                    $pagesLeft = min($pagesToShow, $numPages); /*min関数は最小値の方を返す ループで回すoの数*/

                    // ループ開始地点の設定
                    $currentPage = $page - floor($pagesToShow / 2); /*小数点切り下げ 　ループ開始地点*/
                    if ($currentPage < 1) {  /*ページ数が5より少ないときのループ開始地点は1から */
                        $currentPage = 1;
                    }
                    if ($currentPage + $pagesLeft > $numPages + 1) {  /* ページmax付近でのループ開始地点 */
                        $currentPage = $numPages + 1 - $pagesLeft;
                    }

                    while ($pagesLeft != 0 && $currentPage <= $numPages) { /*currentPageを増やし、pagesLeftをへらす */
                        if ($currentPage == $page) {
                            echo "<div class='pageNumberContainer'>
                                <img src='assets/images/pageSelected.png'>
                                <span class='pageNumber'>$currentPage</span>
    
                            </div>";
                        } else {
                            echo "<div class='pageNumberContainer'>
                                <a href='search.php?term=$term&type=$type&page=$currentPage'>
                                    <img src='assets/images/page.png'>
                                    <span class='pageNumber'>$currentPage</span>
                                </a>
                            </div>";
                        }
                        $currentPage++;
                        $pagesLeft--;
                    } ?>
                    <div class="pageNumberContainer">
                        <img src="assets/images/pageEnd.png">
                    </div>


                </div>

            <?php  } elseif ($type == "movies") { ?>
                <div class="pageButtons">


                </div>
                <script type="text/javascript" src="assets/js/tube.js"></script>
                <script>
                    search();
                </script>


            <?php } elseif ($type == "wiki") {

            ?>
                <div class="pageButtons" style="display:none">
                    <button>
                        <div id="previous" class="pageNumberContainer">
                            &lt;Prev
                            <img src="assets/images/pageStart.png" alt="前へ">
                        </div>
                    </button>
                    <div class='pageNumberContainer'>
                        <img src='assets/images/pageSelected.png'>
                    </div>
                    <div class='pageNumberContainer'>
                        <img src='assets/images/page.png'>
                    </div>

                    <button>
                        <div id="next" class="pageNumberContainer">
                            <img src="assets/images/pageEnd.png" alt="次へ">
                            Next&gt;
                        </div>
                    </button>

                    <script type="text/javascript" src="assets/js/wiki.js"></script>
                    <script>
                        searchBtnClickHandler();
                    </script>


                </div>

            <?php  } ?>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js">
    </script>

    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
    <script type="text/javascript" src="assets/js/tube.js"></script>

</body>

</html>