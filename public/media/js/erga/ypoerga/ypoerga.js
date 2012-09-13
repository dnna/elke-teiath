$(document).ready(function() {
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

        updateCompetitionFieldsVisibility($("#default-competition-competitiontype"));
        $("#default-competition-competitiontype").change(function() {
            updateCompetitionFieldsVisibility(this);
        });
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
    if($(item).val() == 1) {
        $("#fieldset-competition").hide();
    } else {
        $("#fieldset-competition").show();
    }
}

function updateCompetitionFieldsVisibility(item) {
    if($(item).val() == 1) {
        $("#default-competition-refnumassignment-div").show();
        $("#default-competition-assignmentdate-div").show();
        $("#default-competition-refnumnotice-div").hide();
        $("#default-competition-noticedate-div").hide();
        $("#default-competition-execdate-div").hide();
        $("#default-competition-refnumaward-div").hide();
        $("#default-competition-awarddate-div").hide();
    } else {
        $("#default-competition-refnumassignment-div").hide();
        $("#default-competition-assignmentdate-div").hide();
        $("#default-competition-refnumnotice-div").show();
        $("#default-competition-noticedate-div").show();
        $("#default-competition-execdate-div").show();
        $("#default-competition-refnumaward-div").show();
        $("#default-competition-awarddate-div").show();
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