
jQuery(document).ready(function($){
    var frame;
    $('#affilink_icon_button').on('click', function(e){
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Vyberte ikonu',
            button: { text: 'Použít tuto ikonu' },
            multiple: false
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('#action_icon').val(attachment.url);
        });
        frame.open();
    });
});
