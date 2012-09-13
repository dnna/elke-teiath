(function($){
    $.fn.ajaxFormDialog = function(url, options) {
        var defaults = {
            cssclass: 'choose',
            title: 'Επιλογή',
            width: 330,
            postLoad: function() {},
            submit: function() {}
        };

        var options = $.extend(defaults, options);

        var $dialogContentOrig = $(document.createElement('div'));
        $dialogContentOrig.addClass(options.cssclass);
        $dialogContentOrig.html('Παρακαλώ περιμένετε...');

        return this.each(function() {
            var $clickedItem = $(this);
            $(this).click(function(event){
                var $dialogContent = $dialogContentOrig.clone();
                $dialogContent.dialog({
                    modal: true,
                    title: options.title,
                    resizable: false,
                    width: 'auto',
                    open: function() {
                        $dialogContent.load(url, function() {
                            options.postLoad($clickedItem);
                            $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("enable");
                        });
                        $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("disable");
                    },
                    buttons: {
                        Αποθήκευση : function() {
                            options.submit($clickedItem, function() { // Callback
                                $dialogContent.dialog("close");
                                $dialogContent.remove();
                            });
                            $dialogContent.html('Γίνεται αποθήκευση...');
                            $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("disable");
                        },
                        Ακύρωση : function() {
                            $dialogContent.dialog("close");
                            $dialogContent.remove();
                        }
                    }
                });
            });
        });
    }
})(jQuery);