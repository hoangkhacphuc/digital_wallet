<?php
    require_once ('./backend.php');
    if (!isLogin())
    {
        header('location: ./login.php');
        die();
    }
?>

<?php
    $page = "Thẻ cào";
    require_once ('./_layout/_header.php');
?>

<div class="path">Thẻ cào</div>
<div class="page-recharge-card">
    <div class="show-recharge-card">
        <table>
            <thead>
                <tr>
                    <td>STT</td>
                    <td>Mệnh giá</td>
                    <td>Nhà mạng</td>
                    <td>Mã thẻ</td>
                </tr>
            </thead>
            <tbody id="show-card-number">
                <tr>
                    <td>1</td>
                    <td>10.000</td>
                    <td>Vinaphone</td>
                    <td>123456789</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>10.000</td>
                    <td>Vinaphone</td>
                    <td>123456789</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="left">
        <div class="operator">
            <div class="title">Nhà mạng</div>
            <div class="list">
                <div class="item" data-id="0">Viettel</div>
                <div class="item" data-id="1">Mobifone</div>
                <div class="item" data-id="2">Vinaphone</div>
            </div>
        </div>
        <div class="denominations">
            <div class="title">Mệnh giá</div>
            <div class="list">
                <div class="item" data-id="10000">10.000</div>
                <div class="item" data-id="20000">20.000</div>
                <div class="item" data-id="50000">50.000</div>
                <div class="item" data-id="100000">100.000</div>
            </div>
        </div>
        <div class="amount">
            <span>Số lượng</span>
            <div class="choose">
                <i class="fa fa-minus" id="amount-minus"></i>
                <span id="amount-value">1</span>
                <i class="fa fa-plus" id="amount-plus"></i>
            </div>
        </div>
    </div>
    <div class="right">
        <div class="title">Chi tiết giao dịch</div>
        <table>
            <tbody>
                <tr>
                    <td><span>Nhà mạng</span></td>
                    <td><i id="show-operator"></i></td>
                </tr>
                <tr>
                    <td><span>Mệnh giá</span></td>
                    <td><i class="money" id="show-denominations">0</i></td>
                </tr>
                <tr>
                    <td><span>Số lượng</span></td>
                    <td><i id="show-amount">1</i></td>
                </tr>
                <tr>
                    <td><span>Phí giao dịch</span></td>
                    <td><i class="money">0</i></td>
                </tr>
                <tr>
                    <td><span>Tổng tiền</span></td>
                    <td><i class="money" id="show-total">0</i></td>
                </tr>
            </tbody>
        </table>
        <div class="btn-pay" id="btn-pay-recharge-card">Thanh toán</div>
    </div>
</div>

<?php
    require_once ('./_layout/_footer.php');
?>