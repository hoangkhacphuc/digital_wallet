<?php
    require_once ('./backend.php');
    if (isLogin())
    {
        header('location: ./');
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập | <?= Title ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="./main.js"></script>

    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <img src="./Image/background-login.png" alt="" id="bg-login">
    <div class="login-form">
        <div class="title">Chào mừng đến <?= Title ?>!</div>
        <div class="item">
            <input type="text" id="user" placeholder="Tên đăng nhập">
            <i class="fa fa-user-o"></i>
        </div>
        <div class="item">
            <input type="password" id="pass" placeholder="Mật khẩu">
            <i class="fa fa-eye" id="btn-eye"></i>
        </div>
        <p>
            <a href="#">Quên mật khẩu?</a>
            <label class="dot"></label>
            <a href="./register.php">Đăng ký ngay</a>
        </p>
        <div class="btn"><button id="btn-login">Đăng Nhập</button></div>
    </div>
</body>
</html>