<?php
require_once("config.php");
require_once("classes/Constants.php");
require_once("classes/User.php");

$user = new User($con);

if (isset($_POST["submitButton"])) {
  $username = sanitizeFormValue($_POST["username"]);
  $password = sanitizeFormPassword($_POST["password"]);
  // var_dump($username, $password);
  // return false;
  $success = $user->register($username, $password);
  if ($success) {

    $_SESSION["username"] = $username;
    header("Location: admin.php");
  }
}
function sanitizeFormValue($inputText)
{
  $inputText = strip_tags($inputText);
  $inputText = trim($inputText);
  $inputText = strtolower($inputText);
  return $inputText;
}
function sanitizeFormPassword($inputText)
{
  $inputText = strip_tags($inputText);
  return $inputText;
}
function getInputValue($name)
{
  if (isset($_POST[$name])) {
    echo htmlspecialchars($_POST[$name]);
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous" />
  <link rel="stylesheet" href="css/style.css" />
  <title>登録</title>
</head>

<body>
  <!-- START HERE -->
  <nav class="navbar navbar-expand-sm navbar-dark bg-dark p-0">
    <div class="container">
      <a href="index.php" class="navbar-brand">MySite</a>
    </div>
  </nav>

  <!-- HEADER -->
  <header id="main-header" class="py-2 bg-primary text-white">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h1><i class="fas fa-user"></i> MySite</h1>
        </div>
      </div>
    </div>
  </header>

  <!-- ACTIONS -->
  <section id="actions" class="py-4 mb-4 bg-light">
    <div class="container">
      <div class="row">

      </div>
    </div>


  </section>

  <!-- LOGIN -->
  <section id="login">
    <div class="container">
      <div class="row">
        <div class="col-md-6 mx-auto">
          <div class="card">
            <div class="card-header">
              <h4>Account Register</h4>
            </div>
            <div class="card-body">
              <form action="" method="POST">
                <?php echo $user->getError(Constants::$usernameCharacters); ?>
                <?php echo $user->getError(Constants::$usernameTaken); ?>
                <div class="form-group">
                  <label for="username">UserName</label>
                  <input id="username" name="username" type="text" class="form-control" value="<?php getInputValue("username") ?>" required>
                </div>
                <div class="form-group">

                  <label for="password">Password</label>
                  <input id="password" name="password" type="password" class="form-control" required>
                </div>
                <input name="submitButton" type="submit" value="Register" class="btn btn-primary btn-block">
              </form>
              <p>ログインページは<a href="login.php">コチラ</a></p>
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
            Copyright &copy; <span id="year"></span> My site
          </div>
        </div>
      </div>
    </div>
  </footer>



  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
  </script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous">
  </script>


  <script>
    // Get the current year for the copyright
    $('#year').text(new Date().getFullYear());

    CKEDITOR.replace('editor1');
  </script>
</body>

</html>