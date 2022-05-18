<?php
    require_once ('./backend.php');
    if (!isLogin())
    {
        header('location: ./login.php');
        die();
    }
?>

<?php
    $page = "Lịch sử giao dịch";
    require_once ('./_layout/_header.php');
?>
<div class="path">Lịch sử giao dịch</div>

<div class="history">
    <div class="left">
        <div class="item">
            <div class="title">Nạp tiền</div>
            <div class="money">0</div>
        </div>
        <div class="item">
            <div class="title">Chuyển tiền</div>
            <div class="money">0</div>
        </div>
        <div class="item">
            <div class="title">Nhận tiền</div>
            <div class="money">0</div>
        </div>
        <div class="item">
            <div class="title">Rút tiền</div>
            <div class="money"><?= number_format(getTotalAmountWithdrawn()); ?></div>
        </div>
    </div>
    <div class="right">
        <div class="title">Giao dịch gần đây</div>
        <div class="list">
            <?php
                $list = getHistory();
                if (count($list) == 0) {
                    ?>
                    <p class="not-found-history">Không tìm thấy kết quả</p>
                    <?php
                }
                else {
                    foreach ($list as $item) :
            ?>
                <div class="item">
                    <div class="col">
                        <div class="date"><?= convertStringToDateTimeFormat($item['date_created']) ?></div>
                        <div class="note"><?= $item['note'] ?></div>
                    </div>
                    <div class="col">
                        <div class="sub"><?= number_format($item['amount_of_money']) ?></div>
                        <div class="action">Rút tiền</div>
                    </div>
                    <div class="col">
                        <div class="status <?= $item['confirm'] == 0 ? '' : ($item['confirm'] == 1 ? 'ok' : 'no') ?>"><?= $item['confirm'] == 0 ? 'Chờ duyệt' : ($item['confirm'] == 1 ? 'Thành công' : 'Bị hủy') ?></div>
                    </div>
                </div>
            <?php 
                endforeach;
            }
            ?>
        </div>
    </div>
</div>

<?php
    require_once ('./_layout/_footer.php');
?>