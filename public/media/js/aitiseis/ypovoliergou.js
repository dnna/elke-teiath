$(document).ready(function() {
    var supervisorFields = ['capacity', 'departmentname', 'sector', 'phone', 'email'];
    $("#toggleSupervisorDetails").click(function() { toggleDetails('supervisor', supervisorFields); });
    toggleDetails('supervisor', supervisorFields);

    // Funding agency autocomplete
    $('#default-fundingagency-id-element').comboSelect(baseUrl+'/api/agencies.json', {
        resultsProperty: 'agencies',
        initialValue: $('#default-fundingagency-name').val()
    });
    // Co-Funding agency autocomplete
    $('#default-cofundingagency-id-element').comboSelect(baseUrl+'/api/agencies.json', {
        resultsProperty: 'agencies',
        initialValue: $('#default-cofundingagency-name').val()
    });
    // Contractor autocomplete
    $('#default-contractor-id-element').comboSelect(baseUrl+'/api/agencies.json', {
        resultsProperty: 'agencies',
        initialValue: $('#default-contractor-name').val()
    });
});