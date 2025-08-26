<?php 
session_start();
require_once '../modeles/Auth.php';

$auth = new Auth();
if($auth->check()){
    $auth->logout();
}
    header("Location: login.php");

?>