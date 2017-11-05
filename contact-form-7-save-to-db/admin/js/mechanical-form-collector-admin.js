jQuery(function ($) {
    enableDatepickers();
    resetFormButton();
    highlightSource();
    enabledCondition();

    $('.get-form-settings').click(function() {
        showFormSettings($(this));
    });

    function showFormSettings($button) {
        //var $row = $button.closest('tr');
        //var service = $row.find('.service-list').val();
        var data = {
            form: {
                id: $button.data('form-id'),
                system: $button.data('form-system')
            },
            service: $button.closest('tr').find('.service-list').val()
        };

        jQuery.post(
            ajax_object.ajax_url,
            {
                'action': 'show_form_settings',
                'data': data
            },
            function (response) {
                //console.log(response);
                if (response.status === 'success') {
                    //console.log(response.form);
                    $('body').append(response.form);
                } else if (response.status === 'error') {
                    alert(response.message);
                }
            },
            'json'
        );

        $('body').on('click','.mfc-form-settings-close', function() {
            $('.mfc-form-settings-wrap').remove();
        });
    }

    $('body').on('submit','#mfc-form-settings', function (e) {
        e.preventDefault();

        var self = $(this);
        //console.log(self.serializeArray());

        jQuery.post(
            ajax_object.ajax_url,
            {
                'action': 'save_form_settings',
                'data': self.serialize()
            },
            function (response) {
                //console.log(response);
                //if (response.status === 'success') {
                //    //console.log(response.form);
                //    $('body').append(response.form);
                //} else if (response.status === 'error') {
                //    alert(response.message);
                //}
            },
            'json'
        );
    });

    function enabledCondition() {
        var $form = $('.mcf-service-list');
        var conditionSwitcher = $form.find('.mcf-switcher');

        conditionSwitcher.each(function () {
            handleConditionSwitcher($(this))
        });

        conditionSwitcher.change(function() {
            handleConditionSwitcher($(this))
        });
    }

    function handleConditionSwitcher(switcher) {
        var $conditionWrap = switcher.closest('tr').find('.mcf-condition-wrap');

        if (switcher.prop('checked') === false) {
            $conditionWrap.removeClass('show-condition');
            $conditionWrap.addClass('show-permanent');
        } else {
            $conditionWrap.removeClass('show-permanent');
            $conditionWrap.addClass('show-condition');
        }
    }

    function resetFormButton() {
        var $filterForm = $('#form-filter');
        $filterForm.find('#reset').click(function () {
            $filterForm.find("input[type ='text'], select").val("");
            $filterForm.find("input:checkbox, input:radio").prop('checked', false);
        });
    }

    function highlightSource() {
        $('.code-highlight-source').each(function () {
            var self = $(this);
            self.hide();
            var blockHighlightID = self.data('highlight-block-id');

            var editor = ace.edit(blockHighlightID);
            editor.setTheme("ace/theme/monokai");
            editor.getSession().setMode("ace/mode/javascript");
            editor.getSession().setValue($.trim(self.val()));

            editor.getSession().on('change', function () {
                self.val(editor.getSession().getValue());
            });
        });
    }

    function enableDatepickers() {
        var dateFormat = "yy-mm-dd",
            from = $( "#from" )
                .datepicker({
                    dateFormat: dateFormat,
                    defaultDate: "-1w",
                    changeMonth: true
                })
                .on( "change", function() {
                    to.datepicker( "option", "minDate", getDate( this ) );
                }),
            to = $( "#to" ).datepicker({
                dateFormat: dateFormat,
                defaultDate: "+1w",
                changeMonth: true
            })
                .on( "change", function() {
                    from.datepicker( "option", "maxDate", getDate( this ) );
                });

        function getDate( element ) {
            var date;
            try {
                date = $.datepicker.parseDate( dateFormat, element.value );
            } catch( error ) {
                date = null;
            }

            return date;
        }
    }
});