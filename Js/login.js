$(document).ready(function () {
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
            url: "http://localhost/digital_wallet/backend.php?API=Login",
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

    toastr.options.closeButton = true;
});