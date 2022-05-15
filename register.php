<?php
    require_once ('./backend.php');
    if (isLogin())
    {
        header('Location: ./');
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký | <?= Title ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="./Js/register.js"></script>

    <link rel="stylesheet" href="./Css/register.css">
</head>
<body>
    <img src="./Image/background-login.png" alt="" id="bg">
    <div class="register-form">
        <div class="title">Chào mừng đến <?= Title ?>!</div>
        <div class="item">
            <input type="text" id="full_name" placeholder="Họ và tên">
            <i class="fa fa-user-o"></i>
        </div>
        <div class="item">
            <input type="phone" id="phone" placeholder="Số điện thoại">
            <i class="fa fa-phone"></i>
        </div>
        <div class="item">
            <input type="email" id="email" placeholder="Email">
            <i class="fa fa-at"></i>
        </div>
        <div class="item">
            <input type="date" id="date_of_birth">
            <i class="fa fa-calendar-o"></i>
        </div>
        <div class="item">
            <input type="text" id="address" placeholder="Địa chỉ">
            <i class="fa fa-compass"></i>
        </div>
        <div class="item">
            <input type="file" id="font_identity_card">
            <label for="font_identity_card" id="show-font_identity_card"><span>Ảnh mặt trước chứng minh nhân dân</span></label>
        </div>
        <div class="item">
            <input type="file" id="back_identity_card">
            <label for="back_identity_card" id="show-back_identity_card"><span>Ảnh mặt sau chứng minh nhân dân</span></d>
        </div>
        <div class="btn"><button id="btn-register">Đăng Ký</button></div>
        <p>
            <a href="#">Quên mật khẩu?</a>
            <label class="dot"></label>
            <a href="./login.php">Đăng nhập ngay</a>
        </p>
    </div>
</body>
</html>