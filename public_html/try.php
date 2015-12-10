<?php

include_once '../model/util/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
echo $_SESSION['login'];
