<?php
    require_once ('./backend.php');
    if (!isLogin())
    {
        header('location: ./login.php');
        die();
    }
?>

<?php
    $page = "Nạp tiền";
    require_once ('./_layout/_header.php');
?>

<div class="path">Nạp tiền</div>
<div class="withdraw-money">
    <div class="basic">
        <p>Tài khoản</p>
        <div class="user"><?= $info['user'] ?></div>
        <div class="surplus"><?= number_format($info['money']) ?></div>
    </div>
    <div class="inp">
        <input type="number" id="inp-card-number" placeholder="Số tài khoản">
        <label for="inp-card-number"><i class="fa fa-address-book-o"></i></label>
    </div>
    <div class="inp">
        <input type="date" id="inp-expiration-date" placeholder="Ngày hết hạn">
        <label for="inp-expiration-date"><i class="fa fa-calendar-minus-o"></i></label>
    </div>
    <div class="inp">
        <input type="number" id="inp-CVV" placeholder="CVV">
        <label for="inp-CVV"><i class="fa fa-credit-card-alt"></i></label>
    </div>
    <div class="inp">
        <input type="number" id="inp-amount-of-money" placeholder="Số tiền">
        <label for="inp-amount-of-money"><i class="fa fa-dollar"></i></label>
    </div>
    <div class="btn">
        <button id="btn-continue-depositing">Tiếp tục</button>
    </div>
</div>

<?php
    require_once ('./_layout/_footer.php');
?>