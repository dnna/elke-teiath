$(document).ready(function() {
    $('#submit').attr('disabled', 'disabled');
    var origbackground = $('select#employee').css('background');
    $('select#employee').css('background', '#E799A3')
    $('#project-projectid-element').comboSelect(baseUrl+'/api/erga.json', {
        resultsProperty: 'projects',
        initialValue: $('#project-title').val(),
        onSelect: function() {
            $('select#employee').css('background', origbackground);
            $('#submit').attr('disabled', 'disabled');
            $('#project-projectid').val($('#project-projectid-element_hidden').val());
            $('select#employee').html('<option value="null">Παρακαλώ Περιμένετε...</option>');
            $.getJSON(baseUrl+'/api/erga/apasxoloumenoi.json?projectid='+$('#project-projectid').attr('value'), function(data){
                var html = '';
                var len = data.employees.length;
                if(len > 0) {
                    for (var i = 0; i< len; i++) {
                        var startdate = data['employees'][i].startdate.date;
                        startdate = new Date(startdate.substring(0, startdate.indexOf(' ')));
                        var enddate = data['employees'][i].enddate.date;
                        enddate = new Date(enddate.substring(0, enddate.indexOf(' ')));
                        html += '<option value="' + data['employees'][i].recordid + '">' + data['employees'][i].name + ' '+$.datepicker.formatDate('dd/mm/yy', startdate)+'-'+$.datepicker.formatDate('dd/mm/yy', enddate)+'</option>';
                    }
                    $('select#employee').html(html);
                    $('#submit').removeAttr('disabled');
                } else {
                    $('select#employee').css('background', '#E799A3')
                    $('select#employee').html('<option value="null">Δεν υπάρχουν απασχολούμενοι</option>');
                }
            });
        }
    });
});