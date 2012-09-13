$(document).ready(function() {
    $('#project-projectid-element').comboSelect(baseUrl+'/api/erga.json', {
        resultsProperty: 'projects',
        initialValue: $('#project-title').val()
    });

    var items = new Array();
    var item = {
        addButtonName: 'receiptcommittee-committeemembers-addMember',
        removeSpecialFunc: removeCommitteeMember,
        firstPart: 'receiptcommittee-committeemembers',
        fields: ['user-userid', 'user-realname', 'user-searchField'],
        fieldToCheck: 'user-userid',
        startItem: 2,
        itemCount: 10
    }
    items = pushToArray(items, item);

    setupItems(jQuery.extend(true, {}, items));

    function click1(aa) {
        $('#receiptcommittee-committeemembers-'+aa+'-user-userid-element').comboSelect(baseUrl+'/api/users.json', {
            resultsProperty: 'users',
            initialValue: $('#receiptcommittee-committeemembers-'+aa+'-user-realname').val()
        });
    }
    for(var i = 1; i <= 10; i++) {
        click1(i);
    }
});

function removeCommitteeMember(element) {
    $("#"+element).val('');
}