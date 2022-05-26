$(document).ready(function () {

    // --------------------- START CONFIG ---------------------
    
    toastr.options.closeButton = true;

    // --------------------- END CONFIG ---------------------

    // --------------------- START JS IN LOGIN PAGE ---------------------

    $('#btn-eye').click(function () { 
        let hide = $('#pass').attr('type');
        if (hide == "password")
            $('#pass').attr('type' , 'text');
        else $('#pass').attr('type' , 'password');
    });

    $('#btn-login').click(function () { 
        let user = $('#user').val();
        let pass = $('#pass').val();

        let data = new FormData();
        data.append('user', user);
        data.append('pass', pass);

        $.ajax({
            type: "POST",
            url: "./backend.php?API=Login",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    window.location = './';
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    // --------------------- END JS IN LOGIN PAGE ---------------------

    // --------------------- START JS IN REGISTER PAGE ---------------------

    $('#font_identity_card').change(function (e) { 
        var img = e.target.files[0];
        if(!img.type.match("image.*")) {
            $('#show-font_identity_card').html('<span>Ảnh mặt trước chứng minh nhân dân</span>');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#show-font_identity_card').html('<img src="'+e.target.result+'"/>');
          }
        reader.readAsDataURL(img);
    });

    $('#back_identity_card').change(function (e) { 
        var img = e.target.files[0];
        if(!img.type.match("image.*")) {
            $('#show-back_identity_card').html('<span>Ảnh mặt trước chứng minh nhân dân</span>');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#show-back_identity_card').html('<img src="'+e.target.result+'"/>');
          }
        reader.readAsDataURL(img);
    });

    $('#btn-register').click(function () { 
        let full_name = $('#full_name').val();
        let phone = $('#phone').val();
        let email = $('#email').val();
        let date_of_birth = $('#date_of_birth').val();
        let address = $('#address').val();
        let font_identity_card = $('#font_identity_card')[0].files[0];
        let back_identity_card = $('#back_identity_card')[0].files[0];

        let data = new FormData();
        data.append('full_name', full_name);
        data.append('phone', phone);
        data.append('email', email);
        data.append('date_of_birth', date_of_birth);
        data.append('address', address);
        data.append('font_identity_card', font_identity_card);
        data.append('back_identity_card', back_identity_card);

        $.ajax({
            type: "POST",
            url: "./backend.php?API=Register",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    window.location = "./login.php";
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    // --------------------- END JS IN REGISTER PAGE ---------------------

    // --------------------- START JS IN INFORMATION PAGE ---------------------

    $('#cmndTruoc').change(function (e) { 
        var img = e.target.files[0];
        if(!img.type.match("image.*")) {
            return;
        }
        $('.cmnd>label>.noti>span').css('display', 'none');
        $('.btn-update-cmnd').attr('data-check-change-cmnd', 'true');
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#show-cmndTruoc').attr( 'src', e.target.result);
          }
        reader.readAsDataURL(img);
    });

    $('#cmndSau').change(function (e) { 
        var img = e.target.files[0];
        if(!img.type.match("image.*")) {
            return;
        }
        $('.cmnd>label>.noti>span').css('display', 'none');
        $('.btn-update-cmnd').attr('data-check-change-cmnd', 'true');
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#show-cmndSau').attr( 'src', e.target.result);
          }
        reader.readAsDataURL(img);
    });

    $('.btn-update-cmnd').click(function () { 
        $change = $('.btn-update-cmnd').attr('data-check-change-cmnd');
        if ($change == "false" || $change == false)
            return;
        let font_identity_card = $('#cmndTruoc')[0].files[0];
        let back_identity_card = $('#cmndSau')[0].files[0];

        let data = new FormData();
        data.append('font_identity_card', font_identity_card);
        data.append('back_identity_card', back_identity_card);

        $.ajax({
            type: "POST",
            url: "./backend.php?API=UpdateCMND",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    window.location = "./";
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    // --------------------- END JS IN INFORMATION PAGE ---------------------

    // --------------------- START JS IN WITHDRAW MONEY PAGE ---------------------


    $('.withdraw-money>.list>.item').click(function () { 
        let id = $(this).attr('id');
        if (id == 'btn-money-1')
            $('#inp-amount-of-money').val('100000');
        if (id == 'btn-money-2')
            $('#inp-amount-of-money').val('200000');
        if (id == 'btn-money-3')
            $('#inp-amount-of-money').val('500000');
        if (id == 'btn-money-4')
            $('#inp-amount-of-money').val('1000000');
        if (id == 'btn-money-5')
            $('#inp-amount-of-money').val('2000000');
        if (id == 'btn-money-6')
            $('#inp-amount-of-money').val('5000000');

        let money = parseInt($('#inp-amount-of-money').val());
        $('#inp-total-withdraw-money').val(money + parseInt(money * 0.05));
    });

    $('#inp-card-number').keyup(function () { 
        let money = parseInt($(this).val()) || 0;
        if (money < 0)
        {
            $(this).val(Math.abs(money));
        }
    });

    $('#inp-CVV').keyup(function () { 
        let money = parseInt($(this).val()) || 0;
        if (money < 0)
        {
            $(this).val(Math.abs(money));
        }
    });

    $('#inp-amount-of-money').keyup(function () { 
        let money = parseInt($(this).val()) || 0;
        if (money < 0)
        {
            $(this).val(Math.abs(money));
            money = Math.abs(money);
        }
        $('#inp-total-withdraw-money').val(money + parseInt(money * 0.05));
    });

    $('#btn-continue-withdraw-money').click(function () { 

        let card_number = $('#inp-card-number').val();
        let expiration_date = $('#inp-expiration-date').val();
        let CVV = $('#inp-CVV').val();
        let amount_of_money = $('#inp-amount-of-money').val();
        let note_withdraw_money = $('#inp-note-withdraw-money').val();

        let data = new FormData();
        data.append('card_number', card_number);
        data.append('expiration_date', expiration_date);
        data.append('CVV', CVV);
        data.append('amount_of_money', amount_of_money);
        data.append('note_withdraw_money', note_withdraw_money);

        $.ajax({
            type: "POST",
            url: "./backend.php?API=WithdrawMoney",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    $('#inp-card-number').val('');
                    $('#inp-expiration-date').val('');
                    $('#inp-CVV').val('');
                    $('#inp-amount-of-money').val('');
                    $('#inp-note-withdraw-money').val('');
                    $('#inp-total-withdraw-money').val('0');
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    // --------------------- END JS IN WITHDRAW MONEY PAGE ---------------------

    // --------------------- START JS IN ADMIN- CMND PAGE ---------------------

    $('.cmnd>.list>.item>.top>.btn>#cmnd-agree').click(function () { 
        let id = $(this).parent().parent().children(0).html();

        let data = new FormData();
        data.append('id', id);

        $.ajax({
            type: "POST",
            url: "../backend.php?API=ConfirmCMND",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    $('#user_' + id).remove();
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    $('.cmnd>.list>.item>.top>.btn>#cmnd-cancel').click(function () { 
        let id = $(this).parent().parent().children(0).html();

        let data = new FormData();
        data.append('id', id);

        $.ajax({
            type: "POST",
            url: "../backend.php?API=CancelCMND",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    $('#user_' + id).remove();
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    // --------------------- END JS IN ADMIN- CMND PAGE ---------------------

    // --------------------- START JS IN ADMIN- WITHDRAWAL MANAGEMENT PAGE ---------------------

    $('.cmnd>.list>.item>.top>.btn>#money-agree').click(function () { 
        let id = $(this).parent().parent().parent().attr('id');
        id = parseInt(id.slice(3, id.length));

        let data = new FormData();
        data.append('id', id);

        $.ajax({
            type: "POST",
            url: "../backend.php?API=ConfirmMoney",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    $('#id_' + id).remove();
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    $('.cmnd>.list>.item>.top>.btn>#money-cancel').click(function () { 
        let id = $(this).parent().parent().parent().attr('id');
        id = parseInt(id.slice(3, id.length));

        let data = new FormData();
        data.append('id', id);

        $.ajax({
            type: "POST",
            url: "../backend.php?API=CancelMoney",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    $('#id_' + id).remove();
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    // --------------------- END JS IN ADMIN- WITHDRAWAL MANAGEMENT PAGE ---------------------

    // --------------------- START JS IN ADMIN- UNLOCK PAGE ---------------------
    
    $('.unlock>.list>.item>.top>.btn>#unlock-agree').click(function () { 
        let id = $(this).parent().parent().parent().attr('id');
        id = parseInt(id.slice(3, id.length));

        let data = new FormData();
        data.append('id', id);

        $.ajax({
            type: "POST",
            url: "../backend.php?API=Unlock",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
                    $('#id_' + id).remove();
                }
                else toastr.error(json['message'], 'Thông báo');
            },
            error: function () {
                toastr.error('Xảy ra lỗi trong quá trình truyền tin', 'Thông báo');
            }
        });
    });

    // --------------------- END JS IN ADMIN- UNLOCK PAGE ---------------------

});