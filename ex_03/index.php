<?php 
session_start();
require_once '../modeles/Auth.php';

$auth = new Auth();
if($auth->check()){
    $info = $auth->userInfo();
    echo "Hello ".$info["username"]."<br>";
    echo "<a href='logout.php'>Logout</a>";
}else{
    header("Location: login.php");
}
?>