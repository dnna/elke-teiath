$(document).ready(function() {
    $('.chooseSynedriasi').ajaxFormDialog(baseUrl+"/synedriaseisee/index/choosesynedriasi", {
        cssclass: 'epilogisunedriasis',
        title: 'Επιλογή συνεδρίασης',
        width: 330,
        postLoad: postLoad,
        submit: submit
    });
});

function postLoad($clickedItem) {
    $('#default-id-element').comboSelect(baseUrl+'/api/synedriaseisee.json', {
        resultsProperty: 'synedriaseis',
        initialValue: $('#default-title').val(),
        displayValue: 'title',
        resultTemplate: '<div class="flexboxresultcol">{title}</div>',
        extraParams: {}
    });
    $('#default-id-element').append('<img id="synedriaseis_editbutton" src="'+baseUrl+'/images/calendar_edit.png" style="margin-left: 10px; cursor: pointer" title="Επεξεργασία Συνεδριάσεων" />');
    $('#synedriaseis_editbutton').click(function() {
        var height = $(window).height()*70/100;
        var width = $(window).width()*70/100;
        window.open (baseUrl+'/synedriaseisee/index/index/barebone/true', 'synedriaseis_editwindow', 'height='+height+', width='+width+',status=0,toolbar=0,location=0,menubar=0');
    });
}

function submit($clickedItem, callback) {
    if($('#default-id').val() == "null" || $('#default-id').val() == "") {
        alert('Επιλέξτε πρώτα αίτηση');
    } else {
        var params = {
            'num': $('#num').val(),
            'synedriasi[id]' : $('#default-id').val(),
            'aitisi[aitisiid]': $clickedItem.attr('id').replace('csaitisiid-', '')
        }
        $.post(baseUrl+'/api/synedriaseisee/subjects', params, function() {
            window.location.reload();
            callback();
        });
    }
}