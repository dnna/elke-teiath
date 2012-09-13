$(document).ready(function() {
    var items = new Array();
    var item = {
        addButtonName: 'committeemembers-addMember',
        removeSpecialFunc: removeCommitteeMember,
        firstPart: 'committeemembers',
        fields: ['user-userid', 'user-realname', 'user-searchField'],
        fieldToCheck: 'user-userid',
        startItem: 2,
        itemCount: 10
    }
    items = pushToArray(items, item);

    setupItems(jQuery.extend(true, {}, items));

    function click1(aa) {
        $('#committeemembers-'+aa+'-user-userid-element').comboSelect(baseUrl+'/api/users.json', {
            resultsProperty: 'users',
            initialValue: $('#committeemembers-'+aa+'-user-realname').val()
        });
    }
    for(var i = 1; i <= 10; i++) {
        click1(i);
    }

    $('#competitiontype-type').change(function(){
        changeType($('#competitiontype-type').val());
    });
    if($('.errors').size() <= 0) {
        changeType($('#competitiontype-type').val());
        $('#submit').attr('disabled', 'disabled');
    }
});

function removeCommitteeMember(element) {
    $("#"+element).val('');
}

function changeType(newtype) {
    $('#fieldset-default').html('<div><img src="'+baseUrl+'/images/autocomplete/indicator.gif" alt="Indicator" />Παρακαλώ περιμένετε</div>');
    $.ajax({
        url: baseUrl+'/praktika/epitropes/ajaxform/?type='+newtype+'&committeeid='+$('#id').val(),
        success: function(data) {
            $('#fieldset-default').html(data);
            $('#submit').removeAttr('disabled');
            $('#default-project-projectid-element').comboSelect(baseUrl+'/api/erga.json', {
                resultsProperty: 'projects',
                initialValue: $('#default-project-title').val()
            });
        }
    });
    $('#fieldset-default').ajaxSuccess(function() {
$("textarea").TextAreaExpander();
    });
}