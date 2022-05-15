<?php
    session_start();
    require_once ('./config.php');
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
        if(!function_exists($func)){
            require_once ('./404.php');
            return;
        }
        call_user_func($func);
    }

    function Register()
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
        $font_identity_card_path = './Image/upload/' . $font_identity_card_path[0] . '_' . md5($font_identity_card_path[0]) . '.' . end($font_identity_card_path);
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
        $back_identity_card_path = './Image/upload/' . $back_identity_card_path[0] . '_' . md5($back_identity_card_path[0]) . '.' . end($back_identity_card_path);
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

    function Login()
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
?>