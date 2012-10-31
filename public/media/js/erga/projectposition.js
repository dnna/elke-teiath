$(document).ready(function() {
    var items = new Array();
    var item = {
        addButtonName: 'position-partners-addPartner',
        removeSpecialFunc: removePartner,
        firstPart: 'position-partners',
        fields: ['partnerlistitem', 'amount', 'iscoordinator'],
        fieldToCheck: 'amount',
        itemCount: 20
    }
    items = pushToArray(items, item);
    
    setupItems(jQuery.extend(true, {}, items));
    
    var unique = $('input.unique');
    unique.click(function() {
        unique.filter(':checked').not(this).removeAttr('checked');
        updateCoordinator();
    });
    updateCoordinator();

    $("#position-default-teirole").change(function() {changeTEIRole($("#position-default-teirole").val())});
    changeTEIRole($("#position-default-teirole").val());
    
    updateCheckboxVisibility();

    // Autocomplete for contractor
    $('#position-anadoxos-id-element').comboSelect(baseUrl+'/api/agencies.json', {
        resultsProperty: 'agencies',
        initialValue: $('#position-anadoxos-name').val()
    });

    // Autocomplete for partners
    var click1 = function(aa) {
        $('#position-partners-'+aa+'-partnerlistitem-id-element').comboSelect(baseUrl+'/api/agencies.json', {
            resultsProperty: 'agencies',
            initialValue: $('#position-partners-'+aa+'-partnerlistitem-name').val()
        });
    }
    for(var i = 1; i <= 20; i++) {
        click1(i);
    }
});


function changeTEIRole(newrole) {
    if(newrole == 0 || newrole == 1 || newrole == 2) {
        $("#fieldset-partners").show();
        $("#fieldset-anadoxos").hide();
    } else if(newrole == 3) {
        $("#fieldset-partners").hide();
        $("#fieldset-anadoxos").show();
    }
    updateCheckboxVisibility();
}

function updateCoordinator() {
    var id;
    $('input.unique').each(function(index) {
        id = this.id.split("-");
        id = id[2];
        if(!$('#position-partners-'+id+'-iscoordinator').is(":checked")) {
            $('#fieldset-position-partners-'+id+'-coordinatorfields').hide();
        } else if($('#position-partners-'+id+'-iscoordinator').is(":checked")) {
            $('#fieldset-position-partners-'+id+'-coordinatorfields').show();
        }
        return true;
    });
}

function updateCheckboxVisibility() {
    var onevisible = false;
    $('.partner').each(function(index) {
        if($('#'+this.id).is(':visible')) {
            onevisible = true;
            return false;
        }
        return true;
    });
    if(onevisible == true) {
        $('.unique').show();
    } else {
        $('.unique').hide();
        $('#position-default-teiiscoordinator').removeAttr('checked');
    }
}

var original_removeItem = removeItem;
var removeItem = (function(item) {
    var result = original_removeItem(item);
    updateCheckboxVisibility();
    return result;
});

var original_addItem = addItem;
var addItem = (function(item) {
    var result = original_addItem(item);
    updateCheckboxVisibility();
    return result;
});

function removePartner(element) {
    $('#'+element).parent().parent().parent().find('.partnerautocompleteid').val('null');
    $('#'+element).parent().parent().parent().find('.partnerautocompletename').val('');
    $('#'+element).parent().parent().parent().find('.partnerautocompleteid').parent().find('.ffb-input').val('');
}