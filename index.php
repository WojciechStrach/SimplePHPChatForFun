<!DOCTYPE html>
<html lang="en">
<head>
  <title>chat</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <!-- Material Design fonts -->
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">

  <!-- Bootstrap -->
  <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.2/css/bootstrap.min.css">

  <!-- Bootstrap Material Design -->
  <link rel="stylesheet" type="text/css" href="dist/css/bootstrap-material-design.css">
  <link rel="stylesheet" type="text/css" href="dist/css/ripples.min.css">
  <script src="https://cdn.jsdelivr.net/g/bootstrap.material-design@4.0.2(bootstrap-material-design.iife.min.js+bootstrap-material-design.iife.js+bootstrap-material-design.umd.js)"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/g/bootstrap.material-design@4.0.2(bootstrap-material-design.css+bootstrap-material-design.min.css)">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
    ob_start();
    session_start();
    require_once('database.php');

    if ( isset($_SESSION['user'])!="" ) {
        header("Location: chat.php");
        exit;
    }

    if( isset($_POST['guest-button']) ) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $nick = $_POST['guest-nick'];

            $guest = new Database();
            $status = $guest->guestLogin($nick);

            if($status != ""){
                echo "<script type='text/javascript'>
                      $(document).ready(function(){
                      $('#error').modal('show');
                      });
                      </script>";        
            }
  
        }  
    }

    if( isset($_POST['user-button']) ){
        if ($_SERVER["REQUEST_METHOD"] == "POST"){

            $nick = $_POST['login-nick'];
            $pass = $_POST['login-pass'];

            $user = new Database();
            $status = $user->login($nick,$pass);

            if($status != ""){
                echo "<script type='text/javascript'>
                      $(document).ready(function(){
                      $('#error').modal('show');
                      });
                      </script>";
            }
        }
    }

    if( isset($_POST['register-button']) ){
        if ($_SERVER["REQUEST_METHOD"] == "POST"){

            $nick = $_POST['register-nick'];
            $pass = $_POST['register-pass'];

            $register = new Database();
            $status = $register->register($nick,$pass);

            if($status != ""){
                echo "<script type='text/javascript'>
                      $(document).ready(function(){
                      $('#error').modal('show');
                      });
                      </script>";
            }
        }
    }
    

    ?>

  <section class="header text-xs-center">
    Witaj na czacie
  </section>    
  <section class="text-xs-center container">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
        <div class="row">  
            <div class="col-sm-4">
                <div class="row-headline">
                    Wejdź jako gość
                    <br>
                </div> 
                <br>
                <br> 
                <div class="form-group label-floating">
                    <label class="control-label" for="guest-nick">Wprowadź pseudonim</label>
                    <input name="guest-nick" class="form-control" id="guest-nick" type="text">
                </div>
                <br>
                <br>
                <button name="guest-button" type="submit" class="btn btn-raised btn-primary">Wejdź</button>
            </div>
            <div class="col-sm-4">
                <div class="row-headline">
                    Zaloguj się
                    <br>
                </div> 
                <br>
                <br>
                <div class="form-group label-floating">
                    <label class="control-label" for="login-nick">Wprowadź pseudonim</label>
                    <input name="login-nick" class="form-control" id="login-nick" type="text">
                </div>
                <div class="form-group label-floating">
                    <label class="control-label" for="login-pass">Wprowadź hasło</label>
                    <input name="login-pass" class="form-control" id="login-pass" type="password">
                </div> 
                <br>
                <br>
                <button name="user-button" type="submit" class="btn btn-raised btn-primary">Wejdź</button>
            </div>
            <div class="col-sm-4">
                <div class="row-headline">
                    Zarejestruj się
                    <br>
                </div> 
                <br>
                <br>
                <div class="form-group label-floating">
                    <label class="control-label" for="register-nick">Wprowadź pseudonim</label>
                    <input name="register-nick" class="form-control" id="register-nick" type="text">
                </div>
                <div class="form-group label-floating">
                    <label class="control-label" for="register-pass">Wprowadź hasło</label>
                    <input name="register-pass" class="form-control" id="register-pass" type="password">
                </div> 
                <br>
                <br>
                <button name="register-button" type="submit" class="btn btn-raised btn-primary">Zarejestruj pseudonim</button>
            </div>
        </div>
    </form>      
  </section>
                       <div id="error" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <p><?php echo $status ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                            </div>
                            </div>
                         </div>
                        </div>
</body>
</html>
<?php ob_end_flush(); ?>