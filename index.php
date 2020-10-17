<!DOCTYPE html>
<html lang="ja">

<head>
    <title>Welcome to Doodle</title>
    <meta name="description" content="Search the web for sites and images.">
    <meta name="keywords" content="Search engine, doodle, websites">
    <meta name="description" content="shotaro matsuya">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark p-0">
        <div class="container">
            <a href="index.php" class="navbar-brand">MySite</a>
            <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">

                    <li class="nav-item">
                        <a href="admin.php" class="nav-link">
                            <i class="fas fa-user-times"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="wrapper indexPage">
        <div class="mainSection">
            <div class="logoContainer">

                <img src="assets/images/festisite_google.png" title="Logo of our site" alt="Site logo">
            </div>
            <div class="searchContainer">
                <form action="search.php" method="GET">
                    <input class="searchBox" type="text" name="term" autocomplete="off">
                    <input class="searchButton" type="submit" value="Search">
                </form>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous">
    </script>
</body>

</html>