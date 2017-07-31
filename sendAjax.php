<?php
    require_once('C:\xampp\php\chat\database.php');
    session_start();
    ob_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST"){

        $message = json_decode(file_get_contents("php://input"));

        $send = new Database();

        $status = $send->addMessage($message->message);

        echo $status;

    }   

?>
<?php ob_end_flush(); ?>