$(document).ready(function() {
    function addunilink(obj, link) {
        obj.css('cursor','pointer');
        obj.click(function() {
            window.location = link;
        });
    }
    
    $(".orderlink").each(function(index) {
        addunilink($(this).parent().parent(), $(this).attr('href'));
    });
});