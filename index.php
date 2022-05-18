<?php
    require_once ('./backend.php');
    if (!isLogin())
    {
        header('location: ./login.php');
        die();
    }
?>

<?php
    $page = "Quản lý thông tin";
    require_once ('./_layout/_header.php');
?>

<div class="path">Quản lý tài khoản</div>

<div class="information">
    <div class="basic">
        <div class="full_name"><?= $info['full_name'] ?></div>
        <div class="user"><?= $info['user'] ?></div>
        <div class="surplus"><?= number_format($info['money']) ?></div>
        <div class="link_history">
            <a href="./logout.php">Đăng xuất</a>
            <a href="./history.php">Lịch sử giao dịch&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a>
        </div>
    </div>
    <div class="detail">
        <div class="left">
            <div class="item">
                <div class="title">Tên đăng nhập</div>
                <span><?= $info['user'] ?></span>
            </div>
            <div class="item">
                <div class="title">Họ và tên</div>
                <span class="full-name"><?= $info['full_name'] ?></span>
            </div>
            <div class="item">
                <div class="title">Số điện thoại</div>
                <span><?= $info['phone_number'] ?></span>
            </div>
            <div class="item">
                <div class="title">Email</div>
                <span><?= $info['email'] ?></span>
            </div>
            <div class="item">
                <div class="title">Ngày sinh</div>
                <span><?= convertStringToDateFormat($info['date_of_birth']) ?></span>
            </div>
            <div class="item">
                <div class="title">Địa chỉ</div>
                <span>Hà Nội</span>
            </div>
        </div>
        <div class="right">
            <div class="item">
                <div class="title">Chứng minh nhân dân mặt trước</div>
                <div class="cmnd">
                    <input type="file" id="cmndTruoc">
                    <label for="cmndTruoc">
                        <div class="noti">
                            <span class="<?= $info['confirm'] == 2 ? 'verified' : 'not-verified'; ?>"><?= $info['confirm'] == 0 ? 'Chưa xác minh' : ($info['confirm'] == 1 ? 'Bị từ chối' : 'Đã xác nhận'); ?></span>
                        </div>
                        <img src="<?= $info['font_identity_card'] ?>" alt="" id="show-cmndTruoc">
                    </label>
                </div>
            </div>
            <div class="item">
                <div class="title">Chứng minh nhân dân mặt sau</div>
                <div class="cmnd">
                    <input type="file" id="cmndSau">
                    <label for="cmndSau">
                        <div class="noti">
                            <span class="<?= $info['confirm'] == 2 ? 'verified' : 'not-verified'; ?>"><?= $info['confirm'] == 0 ? 'Chưa xác minh' : ($info['confirm'] == 1 ? 'Bị từ chối' : 'Đã xác nhận'); ?></span>
                        </div>
                        <img src="<?= $info['back_identity_card'] ?>" alt=""  id="show-cmndSau">
                    </label>
                </div>
            </div>
            <div class="item">
                <div class="btn-update-cmnd" data-check-change-cmnd='false'>Cập nhật</div>
            </div>
        </div>
    </div>
</div>

<?php
    require_once ('./_layout/_footer.php');
?>