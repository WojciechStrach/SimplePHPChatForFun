<!DOCTYPE html>
<html lang="en">
<head>
  <title>chat</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
    <!-- Material Design fonts -->
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">

  <!-- Bootstrap -->
  <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://www.atlasestateagents.co.uk/javascript/tether.min.js"></script>
  <script src="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/js/bootstrap.min.js"></script>

  <!-- Bootstrap Material Design -->
  <link rel="stylesheet" type="text/css" href="dist/css/bootstrap-material-design.css">
  <link rel="stylesheet" type="text/css" href="dist/css/ripples.min.css">
  <script src="https://cdn.jsdelivr.net/g/bootstrap.material-design@4.0.2(bootstrap-material-design.iife.min.js+bootstrap-material-design.iife.js+bootstrap-material-design.umd.js)"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/g/bootstrap.material-design@4.0.2(bootstrap-material-design.css+bootstrap-material-design.min.css)">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <script type='text/javascript'>  
    var app = angular.module('myChat', []);

    app.controller('myCtrl', function($scope, $http) {


      setInterval(function(){

      
        $http({method: 'GET', url: 'getMessages.php'}).success(function(messages) {
          $scope.messagesJson = messages;
        });

        $http({method: 'GET', url: 'getActiveUsers.php'}).success(function(au) {
          $scope.activeUsers = au;
        });

      }, 100);

      $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
      

      $scope.ajaxSend = function ajaxSend(){
    
        $http({method: 'POST', 
               url: 'sendAjax.php',
               data:{
                  'message' : $scope.formData.senddd
               }
               }).success(function(status){
                 $scope.formData.senddd = null;
                 
        });
      };

    });

    

    $(document).ready(function() {

      $(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        document.getElementById("sendd").click(); 
        }
      });

    });

    

      
  </script>
  <?php
    ob_start();
    session_start();
    require_once('database.php');

    if( !isset($_SESSION['user']) ) {
      header("Location: index.php");
      exit; 
    }

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
      $logout = new Database();
      $logout->logout();
    }
    $_SESSION['LAST_ACTIVITY'] = time();

    if( isset($_POST['logout']) ) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $logout = new Database();
            $status = $logout->logout();

            if($status != ""){
                echo "<script type='text/javascript'>
                      $(document).ready(function(){
                      $('#error').modal('show');
                      });
                      </script>";        
            }
  
        }  
    }

    // if( isset($_POST['send']) ) {
    //     if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //         $message = $_POST['message'];

    //         $send = new Database();
    //         $status = $send->addMessage($message);

    //         if($status != ""){
    //             echo "<script type='text/javascript'>
    //                   $(document).ready(function(){
    //                   $('#error').modal('show');
    //                   });
    //                   </script>";        
    //         }
  
    //     }  
    // }
  ?>
   
  <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
  <section class="text-xs-center" id="head-panel">
    <div class="row">
      <div id="logout-div" class="col-sm-3">
        <button name="logout" type="submit" class="btn btn-raised btn-primary">Wyloguj</button>
      </div>
      <div class="col-sm-3">
      </div>
      <div class="col-sm-3">
      </div>
      <div class="col-sm-3">
      </div>
    </div>
  </section>
    <div ng-app="myChat" ng-controller="myCtrl">
      <section class="" id="chat">
        <div class="row">
          <div id="messages-window" class="col-sm-9">
            <div ng-repeat="message in messagesJson"  id="messagess">
              <span id="author">{{message.author}}:</span><span id="message"> {{message.message}}</span>
            </div>  
          </div>    
          <div  id="active-users" class="text-xs-center col-sm-3">
            <h3>Aktywni użytkownicy</h3>
            <div ng-repeat="users in activeUsers" id="users">{{users.nick}}</div>
          </div>
        </div>
      </section>
      <section class="text-xs-center">
        <div class="row" id="message-form">
            <div class="col-sm-3">
              <div id="ses-nick" class="color">
                <?php echo $_SESSION['user'] ?>
              </div>  
            </div>
            <div class="col-sm-6">
                <div class="form-group label-floating">
                    <label class="control-label" for="mess">Napisz wiadomość</label>
                    <input ng-model="formData.senddd" name="message" class="form-control" id="messs" type="text">
                </div>
            </div>
            <div id="send" class="col-sm-3">
                <button ng-click="ajaxSend()" id="sendd" name="send" type="button" class="btn btn-raised btn-primary">Wyślij</button>
            </div>
        </div>
      </section>
    </div>  
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
  </form>                        
</body>
<footer>
</footer>
</html>
<?php ob_end_flush(); ?>