<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../model/util/autoload.php';
session_start();
switch ($_POST["action"]) {
    case "login":
        $loginDB = new Login();
//        if (empty($_SESSION['login'])) {
        if ($loginDB->checkCred($_POST["email"], $_POST["password"]) === true) {
            $_SESSION['login'] = true;
            header('Location: work.php');
            exit;
        } else {
            echo "invalid pw";
        }
//        } else {
//            echo 'lalalal';
//        }
        break;
    case "logout":
        session_destroy();
        header('Location: work.php');
        exit;
        break;
    default:
        break;
}
