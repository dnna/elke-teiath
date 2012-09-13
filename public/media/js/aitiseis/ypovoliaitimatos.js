$(document).ready(function() {
    $('#default-project-projectid-element').comboSelect(baseUrl+'/api/erga.json', {
        resultsProperty: 'projects',
        initialValue: $('#default-project-title').val()
    });

    var origAttachmentContents = $('#default-attachment-div').html();
    setupAttachmentField();
    
    function setupAttachmentField() {
        if($('#default-attachmentname').val() != '') {
            $('#default-attachment-div').html('<dt><label for="curattachment">Όνομα συνημμένου:</label></dt>\n\
                    <dd><input id="curattachment" type="text" disabled="disabled" value="'+$('#default-attachmentname').val()+'"></dd>');
        } else {
            $("#default-attachment-div").html(origAttachmentContents);
        }
        $("#default-attachment-div > dd").append('<img src=' + baseUrl + '/images/delete_x.gif id="clearattachment" title="Καθαρισμός συνημμένου" style="cursor: pointer;">');
        $("#clearattachment").click(function(event){
            event.preventDefault();
            $('#default-attachmentname').val('');
            setupAttachmentField();
        });
    }
});