$(document).ready(function(){
    $("#paketaergasiascollapsible").treeTable({
        indent: 20, // Margin & Padding
        treeColumn: 1,
        clickableNodeNames: true,
        persist: true,
        persistCookiePrefix: "paketaergasiastreetable"+location.pathname.replace(/\//g, '_')
    });

    $(".treetablelink").click(function() {
        $(this).parent().css({'cursor':'default'});
        $(this).parent().unbind("click");
    });
});