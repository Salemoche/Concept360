jQuery(document).ready(function ($) {        jQuery( '.upload_image_button' ).on( 'click', function() {            tb_show('test', 'media-upload.php?type=image&TB_iframe=1');            var field_id = jQuery(this).data('field-id');            window.send_to_editor = function( html )  {                imgurl = jQuery( 'img', html ).attr( 'src' );                jQuery( '#' + field_id ).val(imgurl);                tb_remove();            }            return false;        });});