<?php
    session_start();
    require_once '../modeles/Database.php';
    require_once '../modeles/User.php';
    require_once '../modeles/Auth.php';
    $form =false;
    if($_SERVER['REQUEST_METHOD']=="POST"){
        $db = new Database();
        $user = new User($db);
        
        $user->email = $_POST['email'];
        $user->password= $_POST['password'];
        $auth= new Auth($user);
        $signin=$auth->login();

        if($signin){
            header("Location: index.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body class="container">
    
    <div class="h-100" style="height: 100vh;">
        <form action="" method="post" class="mx-auto w-50 my-5">
            <h2>Login</h2>
            <div class="mb-3 mt-3">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="email" class="form-control" required>
            </div>
            <div class="mb-3 mt-3">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="password" class="form-control" required>
            </div>
            <div class="d-flex">
                <input type="submit" class="form-control btn btn-success mx-1" value="Submit">
                <input type="reset" class="form-control btn btn-outline-success mx-1" value="Cancel">
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>