$(document).ready(function() {
    $('#default-project-projectid-element').comboSelect(baseUrl+'/api/erga.json', {
        resultsProperty: 'projects',
        initialValue: $('#default-project-title').val()
    });
});