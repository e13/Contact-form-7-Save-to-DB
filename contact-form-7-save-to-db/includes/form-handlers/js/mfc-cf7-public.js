jQuery(function ($) {

    var wpcf7 = $(".wpcf7");
    countShowForm(wpcf7);

    function countShowForm(wpcf7) {
        var cf7_forms = [];
        wpcf7.each(function() {
            var  cf7FormIdInput= $(this).find('input[name=_wpcf7]');
            if (cf7FormIdInput.length === 0) {
                return true;
            }
            var cf7FormId = cf7FormIdInput.val();
            if (cf7_forms.indexOf(cf7FormId)  === -1) {
                cf7_forms.push(cf7FormId);
            }
        });

        if (cf7_forms.length > 0 && typeof formCollectorJSData !== 'undefined') {
            $.post(
                formCollectorJSData.ajaxUrl,
                {
                    action: 'count_show_form',
                    forms: cf7_forms
                }
            ).done(function(data) {
                    console.log( "second success" + data );
            });
        }
    }

});
