$(document).ready(function() {
    $('#renamesubprojects').ajaxFormDialog(baseUrl+"/erga/Diaxeirisi/subprojectsname/projectid/"+projectid, {
        cssclass: 'epilogionomatosypoergwn',
        title: 'Τι περιλαμβάνει το έργο;',
        width: 330,
        submit: submit
    });
});

function submit($clickedItem, callback) {
    if($('#subprojectsname').val() == "null" || $('#default-id').val() == "") {
        alert('Το όνομα δεν μπορεί να είναι κενό');
    } else {
        var params = {
            'subprojectsname': $('#subprojectsname').val()
        }
        $.post(baseUrl+'/api/erga/subprojectsnamec/'+projectid, params, function() {
            window.location.reload();
            callback();
        });
    }
}