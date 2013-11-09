$(document).ready(function() {
        var items = new Array();
        var item = {
            addButtonName: 'default-competitions-addCompetition',
            firstPart: 'default-competitions',
            fields: ['recordid'],
            fieldToCheck: 'recordid',
            itemCount: 10,
            startItem: 2
        }
        items = pushToArray(items, item);

        setupItems(jQuery.extend(true, {}, items));

        $("#default-subprojectbudget").blur(function(){
            calculateBudgetWithFPA();
        });
        $("#default-subprojectbudgetfpa").blur(function(){
            calculateBudgetWithFPA();
        });
        calculateBudgetWithFPA(); // Μια φορά στο load της σελίδας
        
        var supervisorFields = ['capacity', 'departmentname', 'sector', 'phone', 'email'];
        $('#subprojectsupervisor-userid-element').comboSelect(baseUrl+'/api/users.json', {
            resultsProperty: 'users',
            initialValue: $('#subprojectsupervisor-realname').val(),
            onSelect: function() {
                $('#subprojectsupervisor-userid').val($('#subprojectsupervisor-userid-element_hidden').val());
                selectNewUser($('#subprojectsupervisor-userid').val(), '#subprojectsupervisor-', supervisorFields);
            }
        });

        $("#toggleSupervisorDetails").click(function() { toggleDetails('subprojectsupervisor', supervisorFields); });
        toggleDetails('subprojectsupervisor', supervisorFields);

        updateCompetitionVisibility($("#default-subprojectdirectlabor"));
        var origDirectlabor = $("#default-subprojectdirectlabor").val();
        $("#default-subprojectdirectlabor").change(function() {
            if($(this).val() != origDirectlabor) {
                if(!confirm('Αν το υποέργο έχει απασχολούμενους ή αναδόχους, αυτοί θα διαγραφούν. Θέλετε να προχωρήσετε;')) {
                    $(this).val(origDirectlabor);
                    return false;
                }
            }
            updateCompetitionVisibility(this);
        });

        $.each($('#fieldset-competitions dl > *'), function() {
            var id = $(this).attr('id');
            var idarr = id.split('-');
            var num = idarr[idarr.length - 1];
            updateCompetitionFieldsVisibility($("#default-competitions-"+num+"-competitiontype"), num);
            $("#default-competitions-"+num+"-competitiontype").change(function() {
                updateCompetitionFieldsVisibility($(this), num);
            });
        })
});

function calculateBudgetWithFPA() {
//       x.xxx,xx
    var a = parseFloat($("#default-subprojectbudget").val().ReplaceAll(".","").ReplaceAll(",","."));
//       xxxx.xx
    var b = parseFloat($("#default-subprojectbudgetfpa").val().ReplaceAll(".","").ReplaceAll(",","."));

    if(isNaN(b)) {
        $("#default-subprojectbudgetwithfpa").val(a);
    } else {
        $("#default-subprojectbudgetwithfpa").val(a+b);
    }
    $("#default-subprojectbudgetwithfpa").formatNumber({format:"#,###.00", locale:"gr"});
}

function updateCompetitionVisibility(item) {
    // Fix the isVisible property to make sure we don't get false competitions in
    var isVisibleOff = function() {
        $.each($('#fieldset-competitions dl > *'), function() {
            var id = $(this).attr('id');
            var idarr = id.split('-');
            var num = idarr[idarr.length - 1];
            $('#default-competitions-'+num+'-isvisible').data('old-isVisible', $('#default-competitions-1-isvisible').val());
            $('#default-competitions-'+num+'-isvisible').val('0');
        });
    };
    var isVisibleOn = function() {
        $.each($('#fieldset-competitions dl > *'), function() {
            var id = $(this).attr('id');
            var idarr = id.split('-');
            var num = idarr[idarr.length - 1];
            if(typeof $('#default-competitions-'+num+'-isvisible').data('old-isVisible') != 'undefined') {
                $('#default-competitions-'+num+'-isvisible').val($('#default-competitions-'+num+'-isvisible').data('old-isVisible'));
                $('#default-competitions-'+num+'-isvisible').removeData('old-isVisible');
            }
        });
    };
    if($(item).val() == 1) {
        $("#fieldset-competitions").hide();
        isVisibleOff();
    } else {
        $("#fieldset-competitions").show();
        isVisibleOn();
    }
}

function updateCompetitionFieldsVisibility(item, num) {
    if($(item).val() == 1) {
        $("#default-competitions-"+num+"-refnumassignment-div").show();
        $("#default-competitions-"+num+"-assignmentdate-div").show();
        $("#default-competitions-"+num+"-refnumnotice-div").hide();
        $("#default-competitions-"+num+"-noticedate-div").hide();
        $("#default-competitions-"+num+"-execdate-div").hide();
        $("#default-competitions-"+num+"-refnumaward-div").hide();
        $("#default-competitions-"+num+"-awarddate-div").hide();
    } else {
        $("#default-competitions-"+num+"-refnumassignment-div").hide();
        $("#default-competitions-"+num+"-assignmentdate-div").hide();
        $("#default-competitions-"+num+"-refnumnotice-div").show();
        $("#default-competitions-"+num+"-noticedate-div").show();
        $("#default-competitions-"+num+"-execdate-div").show();
        $("#default-competitions-"+num+"-refnumaward-div").show();
        $("#default-competitions-"+num+"-awarddate-div").show();
    }
}

function selectNewUser(newuser, prefix, fields) {
    for(var field in fields) {
        $(prefix+fields[field]).val('Παρακαλώ περιμένετε');
    }
    $.getJSON(baseUrl+'/api/users/'+newuser+'.json', function(data) {
        for(var field in fields) {
            var fieldname = fields[field];
            $(prefix+fields[field]).val(data[fieldname]);
        }
    });
}