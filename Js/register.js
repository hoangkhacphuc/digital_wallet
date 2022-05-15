$(document).ready(function () {
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
            url: "http://localhost/digital_wallet/backend.php?API=Register",
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                let json = JSON.parse(response);
                if (json['status'])
                {
                    toastr.success(json['message'], 'Thông báo');
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