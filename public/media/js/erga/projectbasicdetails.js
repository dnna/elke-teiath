$(document).ready(function() {
    var items = new Array();
    var item = {
        addButtonName: 'basicdetails-modifications-addModification',
        firstPart: 'basicdetails-modifications',
        fields: ['refnum'],
        fieldToCheck: 'refnum',
        itemCount: 20
    }
    items = pushToArray(items, item);
    
    item = {
        addButtonName: 'basicdetails-committee-addCommitteeMember',
        removeSpecialFunc: removeCommitteeMember,
        firstPart: 'basicdetails-committee',
        fields: ['user-userid', 'user-realname', 'user-searchField'],
        fieldToCheck: 'user-userid',
        itemCount: 10
    }
    items = pushToArray(items, item);
    
    setupItems(jQuery.extend(true, {}, items));

    var supervisorFields = ['capacity', 'departmentname', 'sector', 'phone', 'email'];
    $('#basicdetails-supervisor-userid-element').comboSelect(baseUrl+'/api/users.json', {
        resultsProperty: 'users',
        initialValue: $('#basicdetails-supervisor-realname').val(),
        onSelect: function() {
            $('#basicdetails-supervisor-userid').val($('#basicdetails-supervisor-userid-element_hidden').val());
            selectNewUser($('#basicdetails-supervisor-userid').val(), '#basicdetails-supervisor-', supervisorFields);
        }
    });

    $("#toggleSupervisorDetails").click(function() { toggleDetails('basicdetails-supervisor', supervisorFields); });
    toggleDetails('basicdetails-supervisor', supervisorFields);

    var click1 = function(aa) {
        $('#basicdetails-committee-'+aa+'-user-userid-element').comboSelect(baseUrl+'/api/users.json', {
            resultsProperty: 'users',
            initialValue: $('#basicdetails-committee-'+aa+'-user-realname').val()
        });
    }
    for(var i = 1; i <= 10; i++) {
        click1(i);
    }
    
    iscomplex_orig = $("#default-iscomplex").val();
    
    $("#default-iscomplex").change(function() {
        var iscomplex_new = $("#default-iscomplex").val()
        if(iscomplex_new != iscomplex_orig) {
            if(iscomplex_orig == 1 && !confirm('Εαν το έργο έχει υποέργα, αυτά θα διαγραφούν. Θέλετε να συνεχίσετε;')) {
                $("#default-iscomplex").val(iscomplex_orig);
            }
        }
    });
});

function removeCommitteeMember(element) {
    $("#"+element).val('');
}

function selectNewUser(newuser, prefix, fields) {
    for(var field in fields) {
        $(prefix+fields[field]).val('Παρακαλώ περιμένετε');
    }
    $.getJSON(baseUrl+'/api/users/'+newuser+'.json', function(data) {
        for(var field in fields) {
            var fieldname = fields[field];
            $(prefix+fields[field]).val(data[fieldname]);
        }
    });
}