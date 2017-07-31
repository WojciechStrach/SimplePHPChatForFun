<?php
    require_once('database.php');

    $activeUsers = new Database();

    $json = $activeUsers->getActiveUsers();

    echo $json;

?>