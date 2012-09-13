(function($){
    $.fn.comboSelect = function(url, options) {

        var defaults = {
            queryDelay: 400,
            minChars: 2,
            width: 330,
            displayValue: 'name',
            watermark: 'Παρακαλώ επιλέξτε...',
            resultTemplate: '<div class="flexboxresultcol">{name}</div>'
        };

        var options = $.extend(defaults, options);

        return this.each(function() {
            var obj = $(this);
            var rootid = obj.attr('id').substring(0, obj.attr('id').lastIndexOf('-'));
            var rootsubform = rootid.substring(0, rootid.lastIndexOf('-'));
            var elementid = rootid.substring(rootsubform.length + 1);
            if(typeof options.onSelect == 'undefined') {
                options.onSelect = function() {
                    $('#'+rootid).val($('#'+obj.attr('id')+'_hidden').val());
                }
            }
            if(typeof options.hiddenValue == 'undefined') {
                options.hiddenValue = elementid;
            }
            if(typeof options.resultsProperty == 'undefined') {
                alert('comboSelect: The option "resultsProperty" is mandatory');
                return;
            }
            if(typeof options.initialValue == 'undefined') {
                alert('comboSelect: The option "initialValue" is mandatory');
                return;
            }
            obj.flexbox(url, options);
            if($(this).parent().find('label').hasClass('optional')) {
                var $clearelement = $('<img src="'+baseUrl+'/images/clear.png" alt="Clear" class="clearFlexbox" title="Καθαρισμός" />');
                $clearelement.click(function() {
                    $(this).parent().children('input:eq(0)').val('null');
                    $(this).parent().children('input:eq(1)').val('');
                    $(this).parent().children('input:eq(2)').val('');
                })
                $(this).append($clearelement);
            }
            /*$('#clearAitisi').click(function() {
                $(this).parent().children('div').children('dd').children('input[type=hidden]:first').val('null');
                $(prefix+'aitisiid-element').setValue('');
            });*/
        });
    };
})(jQuery);