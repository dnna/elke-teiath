$(document).ready(function(){
    $("#ypoergacollapsible").treeTable({
        indent: 20, // Margin & Padding
        treeColumn: 1,
        clickableNodeNames: true,
        persist: true,
        persistCookiePrefix: "ypoergatreetable"+location.pathname.replace(/\//g, '_')
    });

    $(".treetablelink").click(function() {
        $(this).parent().css({'cursor':'default'});
        $(this).parent().unbind("click");
    });
});