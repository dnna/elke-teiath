$(document).ready(function() {
/*    var sessionsubjectOrig = $('#default-sessionsubject-recordid-element').html();
    if($('#default-session-id').val() == "" || $('#default-session-id').val() == "null") {
        setupSubjectSelect(false, $('#default-session-id').val());
    } else {
        setupSubjectSelect(true, $('#default-session-id').val());
    }*/
    $('#default-session-id-element').comboSelect(baseUrl+'/api/synedriaseisee.json', {
        resultsProperty: 'synedriaseis',
        initialValue: $('#default-session-title').val(),
        displayValue: 'title',
        resultTemplate: '<div class="flexboxresultcol">{title}</div>',
        extraParams: {}
    });

    $('#default-session-id-element').append('<img id="synedriaseis_editbutton" src="'+baseUrl+'/images/calendar_edit.png" style="margin-left: 5px; cursor: pointer" title="Επεξεργασία Συνεδριάσεων" />');
    $('#synedriaseis_editbutton').click(function() {
        var height = $(window).height()*70/100;
        var width = $(window).width()*70/100;
        window.open (baseUrl+'/synedriaseisee/index/index/barebone/true', 'synedriaseis_editwindow', 'height='+height+', width='+width+',status=0,toolbar=0,location=0,menubar=0');
    });

/*    function setupSubjectSelect(sessionselected, synedriasiid) {
        $('#default-sessionsubject-recordid-element').html(sessionsubjectOrig);
        if(sessionselected == true) {
            $('#default-sessionsubject-recordid-element').flexbox(baseUrl+'/api/synedriaseisee/subjects.json?synedriasiid='+synedriasiid+'&aitisiid='+$('#default-aitisiid').val(), {
                queryDelay: 400,
                minChars: 2,
                width: 330,
                displayValue: 'titlewithnum',
                hiddenValue: 'recordid',
                watermark: 'Παρακαλώ επιλέξτε...',
                resultsProperty: 'subjects',
                resultTemplate: '<div class="flexboxresultcol">{titlewithnum}</div>',
                initialValue: $('#default-sessionsubject-titlewithnum').val(),
                onSelect: function() {
                    $('#default-sessionsubject-recordid').val($('#default-sessionsubject-recordid-element_hidden').val());
                },
                extraParams: {}
            });
        } else {
            $('#default-sessionsubject-recordid-element').html('<input type="text" disabled="disabled" value="Επιλέξετε συνεδρίαση"/>');
        }
    }*/
});