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

<div class="path">Rút tiền</div>
<div class="withdraw-money">
    <div class="basic">
        <p>Từ tài khoản</p>
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
    <div class="list">
        <div class="item" id="btn-money-1">100,000 VNĐ</div>
        <div class="item" id="btn-money-2">200,000 VNĐ</div>
        <div class="item" id="btn-money-3">500,000 VNĐ</div>
        <div class="item" id="btn-money-4">1,000,000 VNĐ</div>
        <div class="item" id="btn-money-5">2,000,000 VNĐ</div>
        <div class="item" id="btn-money-6">5,000,000 VNĐ</div>
    </div>
    <div class="note">
        <input type="text" id="inp-note-withdraw-money" value="<?= $info['full_name']?> chuyen tien" placeholder="Nội dung chuyển tiền">
    </div>
    <div class="total">
        <input type="text" id="inp-total-withdraw-money" value="0" placeholder="Tổng tiền" disabled>
    </div>
    <div class="btn">
        <button id="btn-continue-withdraw-money">Tiếp tục</button>
    </div>
</div>

<?php
    require_once ('./_layout/_footer.php');
?>