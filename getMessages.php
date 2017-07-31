<?php
    require_once('database.php');

    $messages = new Database();

    $json = $messages->getMessages();

    echo $json;

?>