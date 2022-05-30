<?php
    if (isAdmin())
    {
        header('location: ./admin');
        return;
    }
    $info = getInformation(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page) ? $page : '' ?> | <?= Title ?></title>

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Toastr JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <script src="./main.js"></script>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <header>
        <div class="left">
            <a href="./depositing.php"><i class="fa fa-sign-in"></i><span>Nạp tiền</span></a>
            <a href="./withdraw_money.php"><i class="fa fa-money"></i><span>Rút tiền</span></a>
            <a href="./recharge_card.php"><i class="fa fa-credit-card"></i><span>Thẻ cào</span></a>
        </div>
        <div class="right">
            <span><i class="fa fa-dollar"></i><span id="my-money"><?= number_format($info['money']) ?></span></span>
            <a href="./">Quản lý tài khoản <i class="fa fa-angle-right"></i></a>
        </div>
    </header>