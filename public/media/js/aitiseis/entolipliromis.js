var $origdeliverables = new Array();

$(document).ready(function() {
    $('#default-subproject-subprojectid-element').comboSelect(baseUrl+'/api/erga/ypoerga.json', {
        resultsProperty: 'subprojects',
        initialValue: $('#default-subproject-subprojecttitle').val(),
        onSelect: function() {
            $('#default-subproject-subprojectid').val($('#default-subproject-subprojectid-element_hidden').val());
            updateRecipientChoices($('#default-subproject-subprojectid').val(), 'apasxoloumenoi');
        }
    });

    // Κώδικας Παραδοτέων
    for(var i = 1; i <= 20; i++) {
        $origdeliverables[i] = $('#fieldset-deliverables-'+i).html();
    }
    function setupDeliverables() {
        for(var i = 1; i <= 20; i++) {
            $('#fieldset-deliverables-'+i).html($origdeliverables[i]);
            setupDeliverable(i);
        }
    }
    $('#default-recipientauthor-recordid').change(function() {
        setupDeliverables();
    });
    $('#default-recipientcontractor-recordid').change(function() {
        setupDeliverables();
    });
    setupDeliverables();

    var items = new Array();
    var item = {
        addButtonName: 'deliverables-addDeliverable',
        firstPart: 'deliverables',
        fields: ['recordid', 'comments'],
        fieldToCheck: 'recordid',
        //startItem: 2,
        itemCount: 20
    };
    items = pushToArray(items, item);

    setupItems($.extend(true, {}, items));
    // Τέλος Κώδικα Παραδοτέων

    $('#default-recipientauthor-recordid-div').hide();
    $('#default-recipientcontractor-recordid-div').hide();
    //updateRecipientChoices(200); // DEBUG
    updateRecipientChoices($('#default-subproject-subprojectid').val(), 'apasxoloumenoi');

    $('#default-type').change(function() {
        updateSubTypeVisibility($('#default-type').val());
    });
    updateSubTypeVisibility($('#default-type').val());

    $('#default-paymentmethod').change(function() {
        updateRecBankAccountVisibility($('#default-paymentmethod').val());
    });
    updateRecBankAccountVisibility($('#default-paymentmethod').val());

    $('#submit').attr('disabled', 'disabled');
});

function updateRecipientChoices(subprojectid, type) {
    if(subprojectid == "" || subprojectid == "null") {
        return;
    }
    $('#default-recipientcontractor-recordid-div').show();
    $('#default-recipientcontractor-recordid').html('<option value="">Παρακαλώ Περιμένετε...</option>');
    $('#submit').attr('disabled', 'disabled');
    var oldvale = $('#default-recipientauthor-recordid').val();
    var oldvalc = $('#default-recipientcontractor-recordid').val();
    $.getJSON(baseUrl+'/api/erga/ypoerga/'+type+'.json?subprojectid='+subprojectid, function(data) {
        var options = '';
        if(typeof data.contractors != 'undefined') {
            // Contractor
            $('#default-recipientauthor-recordid-div').hide();
            $('#default-recipientauthor-recordid-div').html('<option value="null">-</option>');
            $('#default-recipientcontractor-recordid-div').show();
            for (var key in data.contractors) {
                if (data.contractors.hasOwnProperty(key)) {
                    options += '<option value="' + data.contractors[key].recordid + '">' + data.contractors[key].name + '</option>';
                }
            }
            if(data.contractors.length <= 0) {
                options = '<option value="null">Δεν έχουν οριστεί ανάδοχοι</option>';
            } else {
                $('#submit').removeAttr('disabled');
            }
            $('#default-recipientcontractor-recordid').html(options);
            $('#default-recipientauthor-recordid').val(oldvalc);
        } else {
            // Employee
            $('#default-recipientcontractor-recordid-div').hide();
            $('#default-recipientcontractor-recordid-div').html('<option value="null">-</option>');
            $('#default-recipientauthor-recordid-div').show();
            for (var key in data.employees) {
                if (data.employees.hasOwnProperty(key)) {
                    options += '<option value="' + data.employees[key].recordid + '">' + data.employees[key].name + '</option>';
                }
            }
            if(data.employees.length <= 0) {
                options = '<option value="null">Δεν έχουν οριστεί απασχολούμενοι</option>';
            } else {
                $('#submit').removeAttr('disabled');
            }
            $('#default-recipientauthor-recordid').html(options);
            $('#default-recipientauthor-recordid').val(oldvale);
        }
    });
}

function updateSubTypeVisibility(type) {
    if(type == "1") {
        $('#default-vouchertype-div').show();
        $('#default-subtype-div').hide();
    } else {
        $('#default-vouchertype-div').hide();
        $('#default-subtype-div').show();
    }
}

function updateRecBankAccountVisibility(paymentmethod) {
    if(paymentmethod == "2") {
        $('#default-recbankaccount-div').show();
    } else {
        $('#default-recbankaccount-div').hide();
    }
}

function setupDeliverable(i) {
    var url = '';
    if($('#default-recipientauthor-recordid-div').is(':visible')) {
        url = baseUrl+'/api/erga/ypoerga/paradotea.json?subprojectid='+$('#default-subproject-subprojectid').val()+'&authorid='+$('#default-recipientauthor-recordid').val();
    } else if($('#default-recipientcontractor-recordid-div').is(':visible')) {
        url = baseUrl+'/api/erga/ypoerga/paradotea.json?subprojectid='+$('#default-subproject-subprojectid').val()+'contractorid='+$('#default-recipientcontractor-recordid').val();
    } else {
        $.error('No author or contractor selected.');
    }
    $('#deliverables-'+i+'-deliverable-recordid-element').comboSelect(url, {
        resultsProperty: 'deliverables',
        initialValue: $('#deliverables-'+i+'-deliverable-title').val(),
        onSelect: function() {
            $('#deliverables-'+i+'-deliverable-recordid').val($('#deliverables-'+i+'-deliverable-recordid-element_hidden').val());
        }
    });
}