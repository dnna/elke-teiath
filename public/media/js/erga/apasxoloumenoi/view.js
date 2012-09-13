$(document).ready(function(){
    $("#apasxoloumenoicollapsible").treeTable({
        indent: 20, // Margin & Padding
        treeColumn: 1,
        clickableNodeNames: true,
        persist: true,
        persistCookiePrefix: "apasxoloumenoitreetable"+location.pathname.replace(/\//g, '_')
    });

    $(".treetablelink").click(function() {
        $(this).parent().css({'cursor':'default'});
        $(this).parent().unbind("click");
    });

    var items = new Array();
    var item = {
        addButtonName: 'personnelcategories-addPersonnelCategory',
        firstPart: 'personnelcategories',
        fields: ['name'],
        fieldToCheck: 'name',
        itemCount: 10
    }
    items = pushToArray(items, item);
    setupItems($.extend(true, {}, items));
});