$(document).ready(function() {
    $('#subproject-subprojectid-element').comboSelect(baseUrl+'/api/erga/ypoerga.json', {
        resultsProperty: 'subprojects',
        initialValue: $('#subproject-subprojecttitle').val()}
    );

    updateCompetitionFieldsVisibility($("#competition-default-competitiontype"));
    $("#competition-default-competitiontype").change(function() {
        updateCompetitionFieldsVisibility(this);
    });

    var items = new Array();
    var item = {
        addButtonName: 'competitioncommittee-committeemembers-addMember',
        removeSpecialFunc: removeCommitteeMember,
        firstPart: 'competitioncommittee-committeemembers',
        fields: ['user-userid', 'user-realname', 'user-searchField'],
        fieldToCheck: 'user-userid',
        startItem: 2,
        itemCount: 10
    }
    items = pushToArray(items, item);

    item = {
        addButtonName: 'objectioncommittee-committeemembers-addMember',
        removeSpecialFunc: removeCommitteeMember,
        firstPart: 'objectioncommittee-committeemembers',
        fields: ['user-userid', 'user-realname', 'user-searchField'],
        fieldToCheck: 'user-userid',
        itemCount: 10
    }
    items = pushToArray(items, item);

    setupItems(jQuery.extend(true, {}, items));

    function click1(aa) {
        $('#competitioncommittee-committeemembers-'+aa+'-user-userid-element').comboSelect(baseUrl+'/api/users.json', {
            resultsProperty: 'users',
            initialValue: $('#competitioncommittee-committeemembers-'+aa+'-user-realname').val()
        });

        $('#objectioncommittee-committeemembers-'+aa+'-user-userid-element').comboSelect(baseUrl+'/api/users.json', {
            resultsProperty: 'users',
            initialValue: $('#objectioncommittee-committeemembers-'+aa+'-user-realname').val()
        });
    }
    for(var i = 1; i <= 10; i++) {
        click1(i);
    }
});

function removeCommitteeMember(element) {
    $("#"+element).val('');
}

function updateCompetitionFieldsVisibility(item) {
    if($(item).val() == 1) {
        $("#competition-default-refnumassignment-div").show();
        $("#competition-default-assignmentdate-div").show();
        $("#competition-default-refnumnotice-div").hide();
        $("#competition-default-noticedate-div").hide();
        $("#competition-default-execdate-div").hide();
        $("#competition-default-refnumaward-div").hide();
        $("#competition-default-awarddate-div").hide();
    } else {
        $("#competition-default-refnumassignment-div").hide();
        $("#competition-default-assignmentdate-div").hide();
        $("#competition-default-refnumnotice-div").show();
        $("#competition-default-noticedate-div").show();
        $("#competition-default-execdate-div").show();
        $("#competition-default-refnumaward-div").show();
        $("#competition-default-awarddate-div").show();
    }
}