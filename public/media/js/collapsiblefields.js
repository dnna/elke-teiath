/*
 * Allows a Zend_Form to have collapsible fields that will only display when
 * they are not empty, or when the user explicitly decides to display them.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */

function setupItems(items) {
    var curItem;
    var newItem;
    var startItem;
    for(var item in items) {
        // Κάνουμε expand το item με βάση το itemcount
        curItem = items[item];
        if(typeof curItem.startItem  != "undefined") {
            startItem = curItem.startItem;
        } else {
            startItem = 1;
        }
        createAddEvent(jQuery.extend(true, {}, curItem));
        for(var i = startItem; i <= curItem.itemCount; i++) {
            newItem = jQuery.extend(true, {}, curItem);
            newItem.id = i;
            newItem.firstPart = curItem.firstPart+"-"+i;
            createRemoveEvent(jQuery.extend(true, {}, newItem));
            setupItem(jQuery.extend(true, {}, newItem));
        }
        for(i = 1; i < startItem; i++) {
            $("#"+curItem.firstPart+"-"+i+"-isvisible").val('1');
        }
    }
}

function setupItem(item) {
    if(typeof item.subforms  != "undefined") {
        setupSubforms(jQuery.extend(true, {}, item));
    }
    var itemname = item.firstPart;
    var itemproperties = item.fields;
    if(itemproperties[0] != "") {
        itemproperties.splice(0,0,"");
    }
    if($("#"+itemname+"-isvisible").val() == '0') {
        for(var j = 0; j < itemproperties.length; j++) {
            $("#fieldset-"+itemname).hide();
            $("#"+itemname+"-isvisible").val('0');
        }
    }
}

function setupSubforms(item) {
    var curSubform;
    var newSubform;
    for(var subform in item.subforms) {
        curSubform = item.subforms[subform];
        newSubform = jQuery.extend(true, {}, curSubform);
        newSubform.addButtonName = item.firstPart+'-'+curSubform.addButtonName;
        newSubform.firstPart = item.firstPart+'-'+curSubform.firstPart;
        setupItems([newSubform]);
    }
}

function addItem(curItem) {
    var itemname = curItem.firstPart;
    var itemproperties = curItem.fields;
    if(itemproperties[0] != "") {
        itemproperties.splice(0,0,"");
    }
    var startItem;
    if(typeof curItem.startItem  != "undefined") {
        startItem = curItem.startItem;
    } else {
        startItem = 1;
    }
    var itemcount = curItem.itemCount;
    for(var i = startItem; i <= itemcount; i++) {
        if($("#"+itemname+"-"+i+"-isvisible").val() == '0') {
            for(var j = 0; j < itemproperties.length; j++) {
                $("#fieldset-"+itemname+"-"+i).fadeIn();
                $("#"+itemname+"-"+i+"-isvisible").val('1');
            }
            break;
        }
    }
}

function removeItem(curItem) {
    var itemname = curItem.firstPart;
    var itemproperties = curItem.fields;
    if(itemproperties[0] != "") {
        itemproperties.splice(0,0,"");
    }
    var emptyValue;
    if(typeof curItem.emptyValue  != "undefined") {
        emptyValue = curItem.emptyValue;
    } else {
        emptyValue = "";
    }
    var itemtocheck;
    if(typeof curItem.fieldToCheck  != "undefined") {
        itemtocheck = curItem.fieldToCheck;
    }
    if($("#"+itemname+"-isvisible").val() == '1') {
        if(typeof itemtocheck == "undefined" || $("#"+itemname+"-"+itemtocheck).val() == emptyValue || confirm('Κάποια πεδία δεν είναι κενά. Θέλετε σίγουρα να συνεχίσετε;')) {
            for(var j = 0; j < itemproperties.length; j++) {
                $("#fieldset-"+itemname).hide();
                $("#"+itemname+"-isvisible").val('0');
                $("#"+itemname+'-'+itemproperties[j]).val(emptyValue);
                if(itemname+itemproperties[j] == itemname+itemtocheck) {
                    if(typeof curItem.removeSpecialFunc != "undefined") {
                        curItem.removeSpecialFunc(itemname+"-"+itemproperties[j]);
                    } else {
                        $("#"+itemname+"-"+itemproperties[j]).val(emptyValue);
                    }
                } else {
                    $("#"+itemname+"-"+itemproperties[j]).val("");
                }
            }
            $("#"+itemname+'-recordid').val("");
        } else {
            return false;
        }
    }
    return true;
}

function createAddEvent(curItem) {
    $("#"+curItem.addButtonName).click(function() {addItem(jQuery.extend(true, {}, curItem));});
}

function createRemoveEvent(newItem) {
    $("#remove-"+newItem.firstPart).click(function() {removeItem(jQuery.extend(true, {}, newItem));});
}

function pushToArray(arrayobj, item) {
    arrayobj.push(item);
    return arrayobj;
}