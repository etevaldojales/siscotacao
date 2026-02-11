$(document).ready(function() {
    $('#profile-form').submit(function(e) {
        e.preventDefault();

        var formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            role: $('#role').length ? $('#role').val() : undefined,
            password: $('#password').val(),
            password_confirmation: $('#password_confirmation').val(),
            _token: $('input[name="_token"]').val()
        };

        $.ajax({
            url: '/admin/profile',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#message').html('<div class="alert alert-success">' + response.message + '</div>');
                $('#password').val('');
                $('#password_confirmation').val('');
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessages = '<ul>';
                $.each(errors, function(key, value) {
                    errorMessages += '<li>' + value[0] + '</li>';
                });
                errorMessages += '</ul>';
                $('#message').html('<div class="alert alert-danger">' + errorMessages + '</div>');
            }
        });
    });
});
