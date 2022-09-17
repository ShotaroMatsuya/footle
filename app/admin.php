<?php
require_once("config.php");


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
}
$username = $_SESSION["username"];

?>


<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
    integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/style.css" />
  <title>Admin Page</title>
</head>

<body>
  <!-- START HERE -->
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark p-0">
    <div class="container">
      <a href="index.php" class="navbar-brand">Mysite</a>
      <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">

        <ul class="navbar-nav ml-auto">

          <li class="nav-item">
            <a href="logout.php" class="nav-link">
              <i class="fas fa-user-times"></i> Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- HEADER -->
  <header id="main-header" class="py-2 bg-primary text-white">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h1><i class="fas fa-cog"></i> Settings</h1>
        </div>
      </div>
    </div>
  </header>

  <!-- ACTIONS -->
  <section id="actions" class="py-4 mb-4 bg-light">
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <a href="index.php" class="btn btn-light btn-block"><i class="fas fa-arrow-left"> Back To
              Top</i></a>
        </div>

      </div>
    </div>


  </section>

  <!-- SETTINGS -->
  <section id="settings">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-header">
              <h4>Crawling Settings</h4>

            </div>
            <div class="card-body">
              Before Crawling, delete older information from the database table
              <button class="btn btn-danger btn-lg d-block" id="deleteBtn">Delete!</button>


              <hr>

              <form id="myForm1" action="crawl.php" method="POST">
                <fieldset class="form-group">
                  <legend>Choose one to crawl</legend>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="crawlSite" value="https://www.goal.com/en/"
                        checked> Goal.com
                    </label>
                  </div>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="radio" name="crawlSite" class="form-check-input"
                        value="https://onefootball.com/en/home"> one-football
                    </label>
                  </div>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="radio" name="crawlSite" class="form-check-input"
                        value="https://www.bbc.com/sport/football"> BBC for football
                    </label>
                  </div>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="radio" name="crawlSite" class="form-check-input"
                        value="https://www.skysports.com/football/news"> Sky sports
                    </label>
                  </div>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="radio" name="crawlSite" class="form-check-input"
                        value="https://talksport.com/football/"> Talk sport - football -
                    </label>
                  </div>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="radio" name="crawlSite" class="form-check-input" value="https://footballnews.net/">
                      FootBallNews.net
                    </label>
                  </div>
                  <div class="input-group my-3">
                    <button type="submit" class="btn btn-primary btn-lg" id="crawlBtn1" disabled>let' crawling!</button>
                  </div>

                </fieldset>
              </form>


              <form id="myForm2" action="crawl.php" method="POST">
                <fieldset class="form-group">
                  <legend>Crawling specific URL</legend>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">URL</div>
                    </div>
                    <input type="text" class="form-control" name="crawlSite" placeholder="www.example.com/">
                  </div>
                  <div class="input-group my-3">
                    <button id="crawlBtn2" type="submit" class="btn btn-primary btn-lg" disabled>let' crawling!</button>
                  </div>
                </fieldset>
              </form>
              <button class="btn btn-success btn-lg" onclick="resetCookie()">Stop Crawling!</button>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer id="main-footer" class="bg-dark text-white mt-5 p-5">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="lead text-center">
            Copyright &copy; <span id="year"></span> MySite
          </div>
        </div>
      </div>
    </div>
  </footer>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
    integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
  </script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
    integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
  </script>
  <script src="assets/js/crawing.js"></script>


  <script>
  // Get the current year for the copyright
  $('#year').text(new Date().getFullYear());
  </script>
</body>

</html>