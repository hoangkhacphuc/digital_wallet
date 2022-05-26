<?php
    session_start();
    if (file_exists('./config.php'))
        require_once ('./config.php');
    if (file_exists('./DB.php'))
        require_once ('./DB.php');

    // Kiểm tra đã login chưa
    function isLogin()
    {
        if (isset($_SESSION['user']))
            return true;
        return false;
    }

    if (isset($_GET['API']))
    {
        $func = $_GET['API'];
        if(!function_exists('API_' . $func)){
            require_once ('./404.php');
            return;
        }
        call_user_func('API_' . $func);
    }

    function API_Register()
    {
        if (isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng xuất và thực hiện lại',
                'data' => ''
            ));
            return;
        }

        if (!isset($_POST['full_name']) || 
            !isset($_POST['phone']) || 
            !isset($_POST['email']) || 
            !isset($_POST['date_of_birth']) || 
            !isset($_POST['address']) ||
            !isset($_FILES['font_identity_card']) ||
            !isset($_FILES['back_identity_card']) )
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
                'data' => ''
            ));
            return;
        }

        if (!isPhoneNumber($_POST['phone']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Số điện thoại không đúng định dạng',
                'data' => ''
            ));
            return;
        }

        if (!isEmail($_POST['email']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Email không đúng định dạng',
                'data' => ''
            ));
            return;
        }

        if (!isDate($_POST['date_of_birth']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Ngày sinh không đúng định dạng',
                'data' => ''
            ));
            return;
        }

        $font_identity_card_path = explode('.', $_FILES["font_identity_card"]["name"]);
        $font_identity_card_path = './Image/upload/' . $font_identity_card_path[0] . '_' . randomString(10) . '.' . end($font_identity_card_path);
        if (!move_uploaded_file($_FILES["font_identity_card"]["tmp_name"], $font_identity_card_path ))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không thể upload file ảnh mặt trước CMND',
                'data' => ''
            ));
            return;
        }

        $back_identity_card_path = explode('.', $_FILES["back_identity_card"]["name"]);
        $back_identity_card_path = './Image/upload/' . $back_identity_card_path[0] . '_' . randomString(10) . '.' . end($back_identity_card_path);
        if (!move_uploaded_file($_FILES["back_identity_card"]["tmp_name"], $back_identity_card_path ))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không thể upload file ảnh mặt trước CMND',
                'data' => ''
            ));
            return;
        }

        $email_phone_exist = db_select('customer', "`phone_number` = '". $_POST['phone'] . "' OR `email` = '" . $_POST['email']. "'");
        if (count($email_phone_exist) > 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Email hoặc số điện thoại đã tồn tại',
                'data' => ''
            ));
            return;
        }

        $pass = randomString(15);

        $user = db_insert('customer', array(
            'full_name' => $_POST['full_name'],
            'phone_number' => $_POST['phone'],
            'email' => $_POST['email'],
            'date_of_birth' => $_POST['date_of_birth'],
            'address' => $_POST['address'],
            'pass' => md5($pass),
        ));

        $user = $user[0]['id'];

        db_update('customer', array('user' => $user),"`id` = '" . $user ."'");

        db_insert('identity_card', array(
            'customer_id' => $user,
            'font_identity_card' => $font_identity_card_path,
            'back_identity_card' => $back_identity_card_path
        ));

        include ('./sent-mail.php');

        sendMailTo($_POST['full_name'], $_POST['email'], 'Xin chào ' . $_POST['full_name'] . '!<br/>
        Cảm ơn bạn đã đăng ký tham gia ví điện tử <b><i>'. Title .'</i></b>. Tài khoản của bạn là <b style ="color: red"><i>'.$user.'</i></b>, mật khẩu là <b style ="color: red"><i>'.$pass.'</i></b>', 'ĐĂNG KÝ TÀI KHOẢN THÀNH CÔNG | ' . Title);

        echo json_encode(array(
            'status' => true,
            'message' => 'Đăng ký tài khoản thành công. Vui lòng kiểm tra Email để lấy thông tin tài khoản và mật khẩu',
            'data' => ''
        ));
        return;
    }

    function API_Login()
    {
        if (isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng xuất và thực hiện lại',
                'data' => ''
            ));
            return;
        }

        if (!isset($_POST['user']) || 
            !isset($_POST['pass']) )
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
                'data' => ''
            ));
            return;
        }
        $account_exist = db_select('customer', "`user` = '". $_POST['user'] . "'");

        if (count($account_exist) == 0)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tài khoản không tồn tại',
                'data' => ''
            ));
            return;
        }

        $account_abnormal = db_select('abnormal', "`customer_id` = '". $_POST['user'] . "' ORDER BY `date_created` DESC");
        if (count($account_abnormal) >= 3 && count($account_abnormal) < 6)
        {
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $time_current = date('Y-m-d H:i:s');
            $time_final = oneMinuteIncrease($account_abnormal[0]['date_created']);

            if ($time_current <= $time_final)
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa tạm thời. Vui lòng chờ 1 phút và thực hiện lại',
                    'data' => ''
                ));
                return;
            }
        }
        if (count($account_abnormal) == 6) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tài khoản của bạn đã bị khóa vĩnh viễn. Vui lòng liên hệ Admin để mở khóa tài khoản',
                'data' => ''
            ));
            return;
        }

        if ($account_exist[0]['pass'] != md5($_POST['pass']))
        {
            if ((int)$account_exist[0]['user_type'] != 1)
                db_insert('abnormal', array(
                    'customer_id' => $account_exist[0]['id'],
                ));
            echo json_encode(array(
                'status' => false,
                'message' => 'Tài khoản hoặc mật khẩu không chính xác',
                'data' => ''
            ));
            return;
        }

        $_SESSION['user'] = $_POST['user'];

        db_delete('abnormal', "`customer_id` = '". $account_exist[0]['id'] . "'");
        
        echo json_encode(array(
            'status' => true,
            'message' => 'Đăng nhập tài khoản thành công',
            'data' => ''
        ));
        return;
    }

    function API_UpdateCMND()
    {
        if (!isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập và thực hiện lại',
                'data' => ''
            ));
            return;
        }
        if (!isset($_FILES['font_identity_card']) && !isset($_FILES['back_identity_card']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
                'data' => ''
            ));
            return;
        }
        $data = array(
            'front' => '',
            'back'=> ''
        );
        $user = $_SESSION['user'];
        if (isset($_FILES['font_identity_card'])){
            $font_identity_card_path = explode('.', $_FILES["font_identity_card"]["name"]);
            $font_identity_card_path = './Image/upload/' . $font_identity_card_path[0] . '_' . randomString(10) . '.' . end($font_identity_card_path);
            if (!move_uploaded_file($_FILES["font_identity_card"]["tmp_name"], $font_identity_card_path ))
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Không thể upload file ảnh mặt trước CMND',
                    'data' => ''
                ));
                return;
            }
            db_update('identity_card', array(
                'font_identity_card' => $font_identity_card_path,
                'confirm' => 0
            ), "`customer_id` = $user");
            $data['front'] = $font_identity_card_path;
        }

        if (isset($_FILES['back_identity_card'])){
            $back_identity_card_path = explode('.', $_FILES["back_identity_card"]["name"]);
            $back_identity_card_path = './Image/upload/' . $back_identity_card_path[0] . '_' . randomString(10) . '.' . end($back_identity_card_path);
            if (!move_uploaded_file($_FILES["back_identity_card"]["tmp_name"], $back_identity_card_path ))
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Không thể upload file ảnh mặt trước CMND',
                    'data' => ''
                ));
                return;
            }
            db_update('identity_card', array(
                'back_identity_card' => $back_identity_card_path,
                'confirm' => 0
            ), "`customer_id` = $user");
            $data['back'] = $back_identity_card_path;
        }

        echo json_encode(array(
            'status' => true,
            'message' => 'Cập nhật thành công',
            'data' => $data
        ));
        return;
        
    }

    function API_WithdrawMoney()
    {
        if (!isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập và thực hiện lại',
                'data' => ''
            ));
            return;
        }
        if (!isset($_POST['card_number']) ||
            !isset($_POST['expiration_date']) || 
            !isset($_POST['CVV']) ||
            !isset($_POST['amount_of_money']) ||
            !isset($_POST['note_withdraw_money']) )
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
                'data' => ''
            ));
        }
        if ($_POST['card_number'] != '111111' || $_POST['expiration_date'] != '2022-10-10' || $_POST['CVV'] != '411' || (int)$_POST['amount_of_money'] < 50000) 
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thông tin thẻ hoặc số tiền không hợp lệ',
                'data' => ''
            ));
            return;
        }

        if (strlen($_POST['note_withdraw_money']) >= 255) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Nội dung chuyển tiền quá dài',
                'data' => ''
            ));
            return;
        }

        if ((int)$_POST['amount_of_money'] % 50000 != 0) 
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Chỉ nhận số tiền là bội số của 50000 VNĐ',
                'data' => ''
            ));
            return;
        }
        $info = getInformation();
        if ((int)$info['confirm'] != 2)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Chứng minh nhân dân chưa được phê duyệt',
                'data' => ''
            ));
            return;
        }

        $user = $_SESSION['user'];
        $current_year = date('Y');
        $current_month = date('m');
        $current_day = date('d');

        $times = db_select_query("SELECT * FROM withdraw_money WHERE customer_id = '$user' AND YEAR(date_created) = '$current_year' AND MONTH(date_created) = '$current_month' AND DAY(date_created) = '$current_day';");
        if (count($times) >= 2) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tối đa 2 giao dịch mỗi ngày',
                'data' => ''
            ));
            return;
        }
        $total_not_pproved_yet = db_select_query("SELECT sum(amount_of_money) as total FROM withdraw_money WHERE customer_id = '$user' AND confirm = 0 GROUP BY customer_id");
        if (count($total_not_pproved_yet) > 0)
        {
            $total_not_pproved_yet = $total_not_pproved_yet[0]['total'];
        }
        else $total_not_pproved_yet = 0;
        $total = (int)$_POST['amount_of_money'] + round((int)$_POST['amount_of_money'] * 0.05);

        if ($info['money'] - (int)$total_not_pproved_yet < $total)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Tài khoản của bạn không đủ để thực hiện giao dịch',
                'data' => ''
            ));
            return;
        }

        if ((int)$_POST['amount_of_money'] < 5000000)
        {
            db_insert('withdraw_money', array(
                'customer_id' => $user,
                'amount_of_money' => $total,
                'card_number' => $_POST['card_number'],
                'expiration_date' => $_POST['expiration_date'],
                'cvv' => $_POST['CVV'],
                'note' => $_POST['note_withdraw_money'],
                'confirm' => '1',
            ));
            db_update('customer', array(
                'money' => (int)((int)$info['money'] - (int)$total),
            ), '`id` = ' . $user);
            echo json_encode(array(
                'status' => true,
                'message' => 'Giao dịch thành công',
                'data' => ''
            ));
            return;
        }
        db_insert('withdraw_money', array(
                'customer_id' => $user,
                'amount_of_money' => $total,
                'card_number' => $_POST['card_number'],
                'expiration_date' => $_POST['expiration_date'],
                'cvv' => $_POST['CVV'],
                'note' => $_POST['note_withdraw_money'],
            ));
        echo json_encode(array(
            'status' => true,
            'message' => 'Đã thêm giao dịch vào danh sách chờ duyệt',
            'data' => ''
        ));  
    }

    function API_ConfirmCMND()
    {
        if (!isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập và thực hiện lại',
                'data' => ''
            ));
            return;
        }
        if (!isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có quyền truy cập',
                'data' => ''
            ));
            return;
        }
        if (!isset($_POST['id']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp id',
                'data' => ''
            ));
            return;
        }
        db_update('identity_card', array('confirm' => '2'), "`customer_id` = '".$_POST['id']."'");
        echo json_encode(array(
            'status' => true,
            'message' => 'Xác nhận thành công',
            'data' => ''
        ));
    }

    function API_CancelCMND()
    {
        if (!isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập và thực hiện lại',
                'data' => ''
            ));
            return;
        }
        if (!isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có quyền truy cập',
                'data' => ''
            ));
            return;
        }
        if (!isset($_POST['id']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp id',
                'data' => ''
            ));
            return;
        }
        db_update('identity_card', array('confirm' => '1'), "`customer_id` = '".$_POST['id']."'");
        echo json_encode(array(
            'status' => true,
            'message' => 'Từ chối thành công',
            'data' => ''
        ));
    }

    function API_ConfirmMoney()
    {
        if (!isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập và thực hiện lại',
                'data' => ''
            ));
            return;
        }
        if (!isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có quyền truy cập',
                'data' => ''
            ));
            return;
        }
        if (!isset($_POST['id']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp id',
                'data' => ''
            ));
            return;
        }
        db_update('withdraw_money', array('confirm' => '1'), "`id` = '".$_POST['id']."'");
        $wm = db_select('withdraw_money', "`id` = '".$_POST['id']."'")[0];
        $user = db_select('customer', "`id` = '".$wm['customer_id']."'")[0]['money'];
        db_update('customer', array('money' => ((int)$user - (int)$wm['amount_of_money'])), "`id` = '".$wm['customer_id']."'");
        
        echo json_encode(array(
            'status' => true,
            'message' => 'Xác nhận thành công',
            'data' => ''
        ));
    }

    function API_CancelMoney()
    {
        if (!isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập và thực hiện lại',
                'data' => ''
            ));
            return;
        }
        if (!isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có quyền truy cập',
                'data' => ''
            ));
            return;
        }
        if (!isset($_POST['id']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp id',
                'data' => ''
            ));
            return;
        }
        db_update('withdraw_money', array('confirm' => '2'), "`id` = '".$_POST['id']."'");
        echo json_encode(array(
            'status' => true,
            'message' => 'Từ chối thành công',
            'data' => ''
        ));
    }

    function API_Unlock()
    {
        if (!isLogin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng đăng nhập và thực hiện lại',
                'data' => ''
            ));
            return;
        }
        if (!isAdmin())
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không có quyền truy cập',
                'data' => ''
            ));
            return;
        }
        if (!isset($_POST['id']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp id',
                'data' => ''
            ));
            return;
        }

        db_delete('abnormal', "`customer_id` = '". $_POST['id'] . "'");
        
        echo json_encode(array(
            'status' => true,
            'message' => 'Mở khóa thành công',
            'data' => ''
        ));
    }

    function getInformation() {
        if (!isLogin())
            return;
        $user = $_SESSION['user'];
        $info = db_select_query("SELECT *
                                FROM customer as c, identity_card as ic
                                WHERE c.id = ic.customer_id AND c.user = '$user'");
        return $info[0];
    }

    function isAdmin()
    {
        if (!isLogin())
            return;
        $user = $_SESSION['user'];
        $info = db_select_query("SELECT *
                                FROM customer
                                WHERE user = '$user' AND user_type = 1");
        if (count($info) > 0)
            return true;
        return false;
    }

    function getTotalAmountWithdrawn() {
        if (!isLogin())
            return;
        $user = $_SESSION['user'];
        $info = db_select_query("SELECT SUM(amount_of_money) as total 
                                FROM withdraw_money
                                WHERE customer_id = '$user' AND confirm = 1")[0]['total'];
        return $info;
    }

    function getListCMND()
    {
        if (!isLogin())
            return;
        if (!isAdmin())
            return;
        $list = db_select_query("SELECT * FROM identity_card WHERE confirm = 0 LIMIT 30;");
        return $list;
    }

    function getListWithdrawMoney()
    {
        if (!isLogin())
            return;
        if (!isAdmin())
            return;
        $list = db_select_query("SELECT * FROM withdraw_money WHERE confirm = 0 LIMIT 30;");
        return $list;
    }

    function getHistory() {
        if (!isLogin())
            return;
        $user = $_SESSION['user'];
        $info = db_select_query("SELECT * FROM withdraw_money WHERE customer_id = '$user' ORDER BY date_created DESC LIMIT 50;");
        return $info;
    }

    function getListLocked() {
        if (!isLogin())
            return array();
        if (!isAdmin())
            return array();
        $account_locked = db_select_query("SELECT c.id, c.user, c.full_name, c.email, c.phone_number FROM abnormal as a, customer as c, (SELECT COUNT(id) as NUM, customer_id FROM abnormal GROUP BY customer_id) as m WHERE m.NUM > 5 AND a.customer_id = m.customer_id AND a.customer_id = c.id GROUP BY a.customer_id;");
        return $account_locked;
    }

    function isEmail($email) {
        $email = trim($email);
        $email = stripslashes($email);
        $email = htmlspecialchars($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return false;
        if (explode('@', $email)[1] != "gmail.com")
            return false;
        return true;
    }

    function isPhoneNumber(string $s, int $minDigits = 9, int $maxDigits = 14): bool {
        // if (strlen($s) > 9 && )
        $result = preg_match('/^[0-9]{'.$minDigits.','.$maxDigits.'}\z/', $s);
        // echo substr($s, 0, 3) == "+84" ? '1' : "0";
        if ($result && ($s[0] == 0 || substr($s, 0, 3) == "+84"))
            return true;
        return false;
    }

    function isDate($date,  $format = 'Y-m-d') {
        $dt = DateTime::createFromFormat($format, $date);
        return $dt && $dt->format($format) === $date;
    }

    function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function oneMinuteIncrease($date)
    {
        $date = array(
            'Y' => date('Y', strtotime($date)),
            'm' => date('m', strtotime($date)),
            'd' => date('d', strtotime($date)),
            'H' => date('H', strtotime($date)),
            'i' => date('i', strtotime($date)),
            's' => date('s', strtotime($date)),
        );
        $date_1minute = mktime($date['H'], ($date['i'] + 1), $date['s'], $date['m'], $date['d'], $date['Y']);
        return date('Y-m-d H:i:s', $date_1minute);
    }

    function convertStringToDateFormat($date)
    {
        return date('d', strtotime($date)). '/' . date('m', strtotime($date)) . '/' . date('Y', strtotime($date));
    }

    function convertStringToDateTimeFormat($date)
    {
        return date('d', strtotime($date)). '/' . date('m', strtotime($date)) . '/' . date('Y', strtotime($date)) . ' - ' . date('H', strtotime($date)) . ':' . date('i', strtotime($date)) . ':' . date('s', strtotime($date));
    }
?>