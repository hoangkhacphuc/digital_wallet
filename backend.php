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
        $num_abnormal = count($account_abnormal);
        if ($num_abnormal == 3)
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

            $message = 'Tài khoản hoặc mật khẩu không chính xác';
            if ($num_abnormal == 2) 
                $message = 'Tài khoản của bạn đã bị khóa tạm thời. Vui lòng chờ 1 phút và thực hiện lại';
            if ($num_abnormal == 5)
                $message = 'Tài khoản của bạn đã bị khóa vĩnh viễn. Vui lòng liên hệ Admin để mở khóa tài khoản';
            echo json_encode(array(
                'status' => false,
                'message' => $message,
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

    function API_Depositing()
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
        if (!isset($_POST['card_number']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp mã thẻ',
                'data' => ''
            ));
            return;
        }
        
        if (!isset($_POST['date']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp ngày',
                'data' => ''
            ));
            return;
        }
        if (!isset($_POST['cvv']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp mã CVV',
                'data' => ''
            ));
            return;
        }
        if (!isset($_POST['amount']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng cung cấp số tiền',
                'data' => ''
            ));
            return;
        }

        $card_number = $_POST['card_number'];
        $date = $_POST['date'];
        $cvv = $_POST['cvv'];
        $amount = $_POST['amount'];
        
        // check date format
        if (!isDate($date))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Ngày không hợp lệ',
                'data' => ''
            ));
            return;
        }

        // check card number
        if (!is_numeric($card_number) || strlen($card_number) != 6)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Mã thẻ không hợp lệ',
                'data' => ''
            ));
            return;
        }

        // check cvv
        if (!is_numeric($cvv) || strlen($cvv) != 3)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Mã CVV không hợp lệ',
                'data' => ''
            ));
            return;
        }

        // check amount
        if (!is_numeric($amount))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Số tiền không hợp lệ',
                'data' => ''
            ));
            return;
        }

        // Check amount min 50000
        if ($amount < 50000)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Số tiền tối thiểu là 50.000',
                'data' => ''
            ));
            return;
        }

        if ($card_number == '111111')
        {
            if ($date != '2022-10-10')
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Ngày hết hạn không chính xác',
                    'data' => ''
                ));
                return;
            }
            if ($cvv != '411')
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Mã CVV không chính xác',
                    'data' => ''
                ));
                return;
            }
            $user_id = $_SESSION['user'];

            db_insert('depositing', array(
                'customer_id' => $user_id,
                'amount_of_money' => $amount,
                'expiration_date' => $date,
                'card_number' => $card_number,
                'cvv' => $cvv,
            )); 

            $info = getInformation();
            $update_money = (int)$info['money'] + (int)$amount;
            db_update('customer', array('money' => $update_money), "`id` = '".$user_id."'");

            echo json_encode(array(
                'status' => true,
                'message' => 'Nạp tiền thành công',
                'data' => ''
            ));
        }
        else if ($card_number == '222222')
        {
            if ($date != '2022-11-11')
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Ngày hết hạn không chính xác',
                    'data' => ''
                ));
                return;
            }
            if ($cvv != '443')
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Mã CVV không chính xác',
                    'data' => ''
                ));
                return;
            }
            // check amount max 1000000
            if ($amount > 1000000)
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Giới hạn nạp tiền là 1000000',
                    'data' => ''
                ));
                return;
            }

            $user_id = $_SESSION['user'];

            db_insert('depositing', array(
                'customer_id' => $user_id,
                'amount_of_money' => $amount,
                'expiration_date' => $date,
                'card_number' => $card_number,
                'cvv' => $cvv,
            )); 

            $info = getInformation();
            $update_money = (int)$info['money'] + (int)$amount;
            db_update('customer', array('money' => $update_money), "`id` = '".$user_id."'");

            echo json_encode(array(
                'status' => true,
                'message' => 'Nạp tiền thành công',
                'data' => ''
            ));
        }
        else if ($card_number == '333333')
        {
            if ($date != '2022-12-12')
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Ngày hết hạn không chính xác',
                    'data' => ''
                ));
                return;
            }
            if ($cvv != '577')
            {
                echo json_encode(array(
                    'status' => false,
                    'message' => 'Mã CVV không chính xác',
                    'data' => ''
                ));
                return;
            }
            echo json_encode(array(
                'status' => false,
                'message' => 'Thẻ hết tiền',
                'data' => ''
            ));
        }
        else 
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Thẻ này không được hỗ trợ',
                'data' => ''
            ));
        }
    }

    function API_BuyRechargeCard()
    {
        if (!isset($_POST['operator']) || !isset($_POST['denominations']) || !isset($_POST['amount']))
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin',
                'data' => ''
            ));
            return;
        }
        // Assign value
        $operator = $_POST['operator'];
        $denominations = $_POST['denominations'];
        $amount = $_POST['amount'];

        // Check value operator
        if ($operator != 0 && $operator != 1 && $operator != 2)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Nhà mạng không hợp lệ',
                'data' => ''
            ));
            return;
        }

        // Check value denominations
        if ($denominations != 10000 && $denominations != 20000 && $denominations != 50000 && $denominations != 100000)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Mệnh giá không hợp lệ',
                'data' => ''
            ));
            return;
        }

        // Check value amount
        if ($amount < 1 || $amount > 5)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Số lượng không hợp lệ',
                'data' => ''
            ));
            return;
        }

        // Check money
        $info = getInformation();
        if ($info['money'] < $amount * $denominations)
        {
            echo json_encode(array(
                'status' => false,
                'message' => 'Không đủ tiền',
                'data' => ''
            ));
            return;
        }


        $list_card = array();
        $user_id = $_SESSION['user'];

        for ($i = 0; $i < $amount; $i++)
        {
            $card_number = '11111';
            if ($operator == 1)
            {
                $card_number = '22222';
            }
            else if ($operator == 2)
            {
                $card_number = '33333';
            }
            $card_number .= random_number(5);

            db_insert('recharge_card', array(
                'customer_id' => $user_id,
                'code' => $card_number,
                'denominations' => $denominations,
                'operator' => $operator,
            ));
            array_push($list_card, $card_number);
        }

        $update_money = (int)$info['money'] - (int)$amount * $denominations;
        db_update('customer', array('money' => $update_money), "`id` = '".$user_id."'");

        echo json_encode(array(
            'status' => true,
            'message' => 'Mua thẻ cào thành công',
            'data' => $list_card
        ));
    }

    function getTotalDeposit()
    {
        $user_id = $_SESSION['user'];
        $total_deposit = db_select_query("SELECT SUM(amount_of_money) as total FROM `depositing` WHERE customer_id = '$user_id' GROUP BY customer_id;");
        if (count($total_deposit) == 0)
        {
            return 0;
        }
        return $total_deposit[0]['total'];
    }

    function getTotalRechargeCard()
    {
        $user_id = $_SESSION['user'];
        $total_recharge_card = db_select_query("SELECT SUM(denominations) as total FROM `recharge_card` WHERE customer_id = '$user_id' GROUP BY customer_id;");
        if (count($total_recharge_card) == 0)
        {
            return 0;
        }
        return $total_recharge_card[0]['total'];
    }

    function random_number($length)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++)
        {
            $result .= mt_rand(0, 9);
        }
        return $result;
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
        $data = array();
        foreach ($info as $item) {
            array_push($data, array(
                'date_created' => $item['date_created'],
                'amount_of_money' => $item['amount_of_money'],
                'confirm' => $item['confirm'],
                'note' => $item['note'],
                'type' => 0
            ));
        }
        $info = db_select_query("SELECT * FROM depositing WHERE customer_id = '$user' ORDER BY date_created DESC LIMIT 50;");
        foreach ($info as $item) {
            array_push($data, array(
                'date_created' => $item['date_created'],
                'amount_of_money' => $item['amount_of_money'],
                'confirm' => 1,
                'note' => '',
                'type' => 1
            ));
        }
        $info = db_select_query("SELECT * FROM recharge_card WHERE customer_id = '$user' ORDER BY date_created DESC LIMIT 50;");
        foreach ($info as $item) {
            array_push($data, array(
                'date_created' => $item['date_created'],
                'amount_of_money' => $item['denominations'],
                'confirm' => 1,
                'note' => 'Nhà mạng: '.($item['operator'] == 0 ? 'Viettel' : ($item['operator'] == 1 ? 'Mobifone' : 'Vinaphone')).'<br>Mã thẻ: '.$item['code'],
                'type' => 2
            ));
        }

        // sort by date_created desc
        usort($data, function($a, $b) {
            return strtotime($b['date_created']) - strtotime($a['date_created']);
        });

        return $data;
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