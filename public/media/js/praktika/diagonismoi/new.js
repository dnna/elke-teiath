$(document).ready(function() {
    $('#subproject-subprojectid-element').comboSelect(baseUrl+'/api/erga/ypoerga.json', {
        resultsProperty: 'subprojects',
        initialValue: $('#subproject-subprojecttitle').val()}
    );

    updateCompetitionFieldsVisibility($("#default-competitiontype"));
    $("#default-competitiontype").change(function() {
        updateCompetitionFieldsVisibility(this);
    });
});

function updateCompetitionFieldsVisibility(item) {
    if($(item).val() == 1) {
        $("#default-refnumassignment-div").show();
        $("#default-assignmentdate-div").show();
        $("#default-refnumnotice-div").hide();
        $("#default-noticedate-div").hide();
        $("#default-execdate-div").hide();
        $("#default-refnumaward-div").hide();
        $("#default-awarddate-div").hide();
    } else {
        $("#default-refnumassignment-div").hide();
        $("#default-assignmentdate-div").hide();
        $("#default-refnumnotice-div").show();
        $("#default-noticedate-div").show();
        $("#default-execdate-div").show();
        $("#default-refnumaward-div").show();
        $("#default-awarddate-div").show();
    }
}