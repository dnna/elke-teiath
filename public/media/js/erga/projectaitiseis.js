$(document).ready(function() {
    for(var i = 1; i <= 20; i++) {
        var $origContents = $('#default-aitiseis-'+i+'-aitisiid-element').html();
        change(i, $origContents);
        setupAitisi('#default-aitiseis-'+i+'-', $('#default-aitiseis-'+i+'-shorttype').val());
    }
    
    function change(i, $origContents) {
        $('#default-aitiseis-'+i+'-shorttype').change(function() {
            $('#default-aitiseis-'+i+'-aitisiid-element').html($origContents);
            setupAitisi('#default-aitiseis-'+i+'-', $('#default-aitiseis-'+i+'-shorttype').val());
        });
    }
    
    var items = new Array();
    var item = {
        addButtonName: 'default-aitiseis-addAitisi',
        firstPart: 'default-aitiseis',
        fields: ['type', 'aitisiid-element_input', 'aitisiid', 'aitisiname'],
        fieldToCheck: 'aitisiid',
        emptyValue: 'null',
        itemCount: 20
    }
    items = pushToArray(items, item);
    
    setupItems(jQuery.extend(true, {}, items));
});

function setupAitisi(prefix, type) {
    var url;
    if(typeof type != 'undefined') {
        url = baseUrl+'/api/aitiseis/'+type+'.json';
    } else {
        url = baseUrl+'/api/aitiseis.json';
    }
    $(prefix+'aitisiid-element').flexbox(url, {
        queryDelay: 400,
        minChars: 2,
        width: 330,
        displayValue: 'name',
        hiddenValue: 'aitisiid',
        watermark: 'Παρακαλώ επιλέξτε...',
        resultsProperty: 'aitiseis',
        resultTemplate: '<div class="flexboxresultcol">{name}</div>',
        initialValue: $(prefix+'aitisiname').val(),
        onSelect: function() {
            $(prefix+'aitisiid').val($(prefix+'aitisiid-element_hidden').val());
        }
        /*extraParams: {
            'approved' : '1'
        }*/
    });
}