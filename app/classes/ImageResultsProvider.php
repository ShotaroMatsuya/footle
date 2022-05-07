<?php
class ImageResultsProvider
{
    private $con;
    public function __construct($con)
    {
        $this->con = $con;
    }
    public function getNumResults($term)
    {
        $query = $this->con->prepare("SELECT COUNT(*) as total 
                                        FROM images 
                                        WHERE (title LIKE :term 
                                        OR alt LIKE :term)
                                        AND broken=0");
        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);    /* 連想配列として取得(keyはtotal)　*/
        return $row["total"];
    }
    public function getResultsHtml($page, $pageSize, $term, $order = 'clicks')
    {
        $fromLimit = ($page - 1) * $pageSize;
        //page 1 : (1 - 1) * 20 :0
        //page 2 : (2 - 1) * 20 :20
        //page 3 : (3 - 1) * 20 :40

        if($order === 'random'){
            $order = 'RAND()';
        }

        $query = $this->con->prepare("SELECT *
                                        FROM images 
                                        WHERE (title LIKE :term 
                                        OR alt LIKE :term)
                                        AND broken=0
                                        ORDER BY $order  DESC
                                        LIMIT :fromLimit, :pageSize");
        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT); /*デフォルトだとstr */
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT); /*デフォルトだとstr */
        $query->execute();

        $resultsHtml = "<div class='imageResults'>";


        $count = 0;
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {    /*fetch(1行ずつ取得), fetchAll(全データを配列に変換)*/
            $count++;
            $id = $row["id"];
            $imageUrl = $row["imageUrl"];
            $siteUrl = $row["siteUrl"];
            $title = $row["title"];
            $alt = $row["alt"];
            if ($title) {
                $displayText = $title;
            } else if ($alt) {
                $displayText = $alt;
            } else {
                $displayText = $imageUrl;
            }

            // phpからjsに変数をエスケープして渡す場合は、「\"」でかこむ
            $resultsHtml .= "<div class='gridItem image$count'>
								<a href='$imageUrl' data-fancybox='images_buttons' data-caption='$displayText'
									data-siteurl='$siteUrl'>
            
            <script>
                                        $(document).ready(function() {
                                            loadImage(\"$imageUrl\", \"image$count\");  
                                        });
                                    </script>
                                    <span class='details'>$displayText</span>
                                </a>
                            </div>";
        }

        $resultsHtml .= "</div>";
        return $resultsHtml;
    }
}