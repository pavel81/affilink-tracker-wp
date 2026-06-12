jQuery(document).ready(function($) {
    $('.refresh-ssl').on('click', function(e) {
        e.preventDefault();
        var el = $(this);
        var url = el.data('url');
        el.text('⏳');
        $.post(affilink_ajax.ajax_url, {
            action: 'panda_check_ssl',
            nonce: affilink_ajax.nonce,
            url: url
        }, function(response) {
            if (response.success) {
                el.text('🔄');
                el.prev('.ssl-status').text(response.data.status === 'ok' ? '✅' : '❗');
            } else {
                el.text('❗');
            }
        });
    });
});
