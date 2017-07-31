<?php
    define('SERVERNAME', 'yourservername');
    define('USERNAME', 'user');
    define('PASSWORD', 'password');
    define('DATABASE', 'databasename');

    class Database{
        

        private function connect(){

            $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

            if($conn->connect_errno){                
                return false;
            }

            return $conn;
             
        }

        private function cleanInput($value){
            $val = trim($value);
            $val = strip_tags($val);
            $val = htmlspecialchars($val);

            return $val;

        }

        public function register($nick, $pass){

           $data = new Database();

           $nickname = $data->cleanInput($nick);
           $password = $data->cleanInput($pass);

           $error = "";

           if (empty($nickname)) {
            $error = "Wprowadź pseudonim.";
            return $error;
           } else if (strlen($nickname) < 3) {
            $error = "Pseudonim musi zawierać przynajmniej 3 znaki.";
            return $error;
           } else if (!preg_match("/^[a-ząćęłńóśźżA-ZĄĆĘŁŃÓŚŹŻ]+$/",$nickname)) {
            $error = "Pseudonim może zawierać tylko litery alfabetu.";
            return $error;
           }else{
               $connection = $data->connect();
               if($connection == false){
                   $error = "Nie można połączyć się z bazą danych";
                   return $error;
               }
               $query = "SELECT nick FROM users WHERE nick ='$nickname'";
               $result = $connection->query($query);
               $count = $result->num_rows;
               if($count!=0){
                  $error = "Pseudonim jest już w użyciu.";
                  return $error;
               }
           }

           if(empty($password)) {
            $error = "Wprowadź hasło";
            return $error;    
           } else if(strlen($password) < 6){
               $error = "Hasło musi zawierać przynajmniej 6 znaków";
               return $error;
           }

           $password = hash('sha256', $password);

           $query = "INSERT INTO users(nick,password) VALUES('$nickname','$password')";
           $result = $connection->query($query);

           if(!$result){
               $error = "Coś poszło nie tak";
               return $error;
           }else{
               $error = "Udało się zarejestrować pseudonim, możesz się teraz zalogować";
               return $error;
           }

           return $error;


        }

        public function login($nick, $pass){

            $data = new Database();

            $nickname = $data->cleanInput($nick);
            $password = $data->cleanInput($pass);

            $error = "";

            if(empty($nickname)){
             $error = "Wprowadź pseudonim";
             return $error;
            }

            if(empty($password)){
                $error = "Wprowadź hasło";
                return $error;
            }

            $password = hash('sha256', $password);

            $connection = $data->connect();
            if($connection == false){
                $error = "Nie można połączyć się z bazą danych";
                return $error;
            }
            $query = "SELECT nick, password FROM users WHERE nick='$nickname'";
            $result = $connection->query($query);
            $row = $result->fetch_array();
            $count = $result->num_rows;

            if($count == 1 && $row['password'] == $password){

                $query = "INSERT INTO activeusers(nick) VALUES('$nickname')";
                $result = $connection->query($query);

                if(!$result){
                    $error = "Coś poszło nie tak";
                    return $error;
                }


                $_SESSION['user'] = $row['nick'];
                header("Location: chat.php");
            }else{
                $error = "Niepoprawne dane logowania, spróbuj ponownie";
                return $error;
            }

            return $error;

        }

        public function guestLogin($nick){

            $data = new Database();

            $nickname = $data->cleanInput($nick);

            $error = "";

            if (empty($nickname)) {
              $error = "Wprowadź pseudonim.";
              return $error;
            } else if (strlen($nickname) < 3) {
              $error = "Pseudonim musi zawierać przynajmniej 3 znaki.";
              return $error;
            } else if (!preg_match("/^[a-ząćęłńóśźżA-ZĄĆĘŁŃÓŚŹŻ]+$/",$nickname)) {
              $error = "Pseudonim może zawierać tylko litery alfabetu.";
              return $error;
            }else{
                $connection = $data->connect();
                if($connection == false){
                   $error = "Nie można połączyć się z bazą danych";
                   return $error;
               }
               $query = "SELECT nick FROM users WHERE nick ='$nickname'";
               $result = $connection->query($query);
               $row = $result->fetch_array();
               $count = $result->num_rows;
               if($count!=0){
                  $error = "Pseudonim jest już zarejestrowany, nie możesz go użyć.";
                  return $error;
               }else{
                    $query = "SELECT nick FROM activeusers WHERE nick ='$nickname'";
                    $result = $connection->query($query);
                    $row = $result->fetch_array();
                    $count = $result->num_rows;
                    
                    if($count!=0){
                        $error = "Ktoś właśnie używa tego pseudonimu, nie możesz go użyć.";
                        return $error;
                    }

                    $query = "INSERT INTO activeusers(nick) VALUES('$nickname')";
                    $result = $connection->query($query);

                    if(!$result){
                        $error = "Coś poszło nie tak";
                        return $error;
                    }

                    $_SESSION['user'] = $nickname;
                    header("Location: chat.php");
               }

               
            }  
            return $error;


         }


         public function logout(){

             $error = "";

             $data = new Database();

             $connection = $data->connect();
             if($connection == false){
                   $error = "Nie można połączyć się z bazą danych";
                   return $error;
             }
             $nick = $_SESSION['user'];
             $query = "DELETE FROM activeusers WHERE nick='$nick'";

             $result = $connection->query($query);

             if(!$result){
                $error = "Coś poszło nie tak";
                return $error;
             }

             unset($_SESSION['user']);
             session_unset();
             session_destroy();
             header("Location: index.php");
             exit;
         }

         public function addMessage($mess){

             $error = "";

             $data = new Database();

             $message = $data->cleanInput($mess);

             if (empty($message)) {
              $error = "Wiadomość nie może być pusta.";
              return $error;
             }

             $author = $_SESSION['user'];

             $connection = $data->connect();
             if($connection == false){
                   $error = "Nie można połączyć się z bazą danych";
                   return $error;
             }
             $query = "INSERT INTO generalchatmessages(author,message) VALUES('$author','$message')"; 

             $result = $connection->query($query);

             if(!$result){
                $error = "Coś poszło nie tak";
                return $error;
             }

             return $error;
         }

         public function getMessages(){

             $getMessages = new Database();
             $connection = $getMessages->connect();
             if($connection == false){
                   return false;
             }

             $query = "SELECT * FROM generalchatmessages";
             $result = $connection->query($query);

             if(!$result){
                return false;
             }else {
                $resArray = array();
                
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $resArray[] = $row;
                }

                $jsonArray = array();
                $jsonArray = json_encode($resArray); 
             }

             return $jsonArray;


         }

         public function getActiveUsers(){

             $getActiveUsers = new Database();
             $connection = $getActiveUsers->connect();
             if($connection == false){
                   return false;
             }

             $query = "SELECT * FROM activeusers";
             $result = $connection->query($query);

             if(!$result){
                return false;
             }else {
                $resArray = array();
                
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $resArray[] = $row;
                }

                $jsonArray = array();
                $jsonArray = json_encode($resArray); 
             }

             return $jsonArray;

         }
    }
?>