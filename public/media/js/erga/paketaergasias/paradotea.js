$(document).ready(function() {
    var items = new Array();
    var item;

    // Συντάκτες
    item = {
            addButtonName: 'default-authors-addAuthor',
            firstPart: 'default-authors',
            fields: ['name'],
            fieldToCheck: 'name',
            itemCount: 30
    }
    items = pushToArray(items, item);
    setupItems(jQuery.extend(true, {}, items));

    var deliverableFields = item.fields;
    deliverableFields.splice(0, 2);

    function click1(aa) {
        $("#toggleDeliverableDetails_"+aa).click(function() { toggleDetails(aa, 'deliverables-', deliverableFields, ['authors']); });
        toggleDetails(aa, 'deliverables-', deliverableFields, ['authors']);
    }

    for(var i = 1; i <= item.itemCount; i++) {
        click1(i);
    }
});