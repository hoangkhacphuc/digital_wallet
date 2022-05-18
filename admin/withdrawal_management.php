<?php
    require_once ('../config.php');
    require_once ('../DB.php');
    require_once ('../backend.php');
    if (!isLogin())
    {
        header('location: ../login.php');
        return;
        die();
    }
    if (!isAdmin())
    {
        header('location: ./');
        die();
        return;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QL Rút Tiền | <?= Title ?></title>

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Toastr JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <script src="../main.js"></script>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <header>
        <div class="left">
            <a href="./"><span style="padding: 10px">Quản lý CMND</span></a>
            <a href="./withdrawal_management.php"><span style="padding: 10px">Quản lý rút tiền</span></a>
        </div>
        <div class="right">
            <span>ADMIN</span>
            <a href="../logout.php">Đăng xuất <i class="fa fa-angle-right"></i></a>
        </div>
    </header>
    <div class="path">Quản lý rút tiền</div>
    <div class="cmnd">
        <div class="list">
            <?php
                $list = getListWithdrawMoney();
                if (count($list) == 0) {
                    ?>
                    <p class="not-found-cmnd">Không tìm thấy kết quả</p>
                    <?php
                }
                else {
                    foreach ($list as $item):
            ?>
                <div class="item" id="id_<?= $item['id'] ?>">
                    <div class="top">
                        <div class="user_id"><?= $item['customer_id'] ?></div>
                        <div class="btn">
                            <div class="agree" id="money-agree">Xác nhận</div>
                            <div class="cancel" id="money-cancel">Từ chối</div>
                        </div>
                    </div>
                    <div class="bottom">
                        <table>
                            <tbody>
                                <tr>
                                    <td>Mã thẻ</td>
                                    <td><?= $item['card_number'] ?></td>
                                </tr>
                                <tr>
                                    <td>CVV</td>
                                    <td><?= $item['cvv'] ?></td>
                                </tr>
                                <tr>
                                    <td>HSD</td>
                                    <td><?= convertStringToDateFormat($item['expiration_date']); ?></td>
                                </tr>
                                <tr>
                                    <td>Số tiền</td>
                                    <td><?= number_format($item['amount_of_money']); ?></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            <?php 
                endforeach;
                }
            ?>
            
        </div>
    </div>
</body>
</html>

