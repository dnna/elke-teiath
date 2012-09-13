$(document).ready(function() {
    $('#dllink').click(function(e) {
        e.preventDefault();
        var dltemplateselect = window.open($(this).attr('href'), 'dltemplateselectwin', 'height=500, width=650,status=0,toolbar=0,location=0,menubar=0');
        $(window).unload(function() {
            dltemplateselect.close();
        });
    });
});