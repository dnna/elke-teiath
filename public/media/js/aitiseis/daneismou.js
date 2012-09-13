$(document).ready(function() {
    $('#project-projectid-element').comboSelect(baseUrl+'/api/erga.json', {
        resultsProperty: 'projects',
        initialValue: $('#project-title').val(),
        onSelect: function() {
            $('#project-projectid').val($('#project-projectid-element_hidden').val());
            changeProject($('#project-projectid').val());
        }
    });

    $.Calculation.setDefaults({
        // a regular expression for detecting European-style formatted numbers
        reNumbers: /(-?\$?)(\d+(\.\d{3})*(,\d{1,})?|,\d{1,})/g
        // define a procedure to convert the string number into an actual usable number
        ,
        cleanseNumber: function (v){
            // cleanse the number one more time to remove extra data (like commas and dollar signs)
            // use this for European numbers: v.replace(/[^0-9,\-]/g, "").replace(/,/g, ".")
            if(!IsNumeric(v)) {
                return 0.0;
            } else {
                return v.replace(/[^0-9,\-]/g, "").replace(/,/g, ".");
            }
        }
    });

    $('#loanitems-sum').val($(".calcSum").sum());

    if($('#aitisiid').val() != "") {
        changeProject($('#aitisiid').val(), 'aitisiid');
    }
    $('#submit').attr('disabled', 'disabled');
});

function changeProject(newprojectid, paramType) {
    if(typeof paramType == "undefined") {
        paramType = "projectid";
    }
    $('#fieldset-loanitems').html('<div><img src="'+baseUrl+'/images/autocomplete/indicator.gif" alt="Indicator" />Παρακαλώ περιμένετε</div>');
    $.ajax({
        url: baseUrl+'/aitiseis/view/ajaxform/?type=daneismou&subform=loanitems&'+paramType+'='+newprojectid,
        success: function(data) {
            $('#fieldset-loanitems').replaceWith(data);
            $('#loanitems-sum').val($(".calcSum").sum());
            $(".calcSum").keyup(function() {
                $('#loanitems-sum').val($(".calcSum").sum());
            });
            $('#fieldset-default').collapse({
                closed : determineFieldsetCollapsibleStatus($('#fieldset-default'))
            });
            $('#submit').removeAttr('disabled');
        }
    });
}