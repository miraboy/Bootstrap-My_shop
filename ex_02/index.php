<?php 
session_start();
require_once './modeles/Auth.php';

$auth = new Auth();
if($auth->check()){
    $info = $auth->userInfo();
    echo "Hello ".$info["username"];
}else{
    header("Location: login.php");
}
?>