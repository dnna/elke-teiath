$(document).ready(function() {
    var items = new Array();
    var item = {
        addButtonName: 'financialdetails-default-budgetitems-addBudgetItem',
        firstPart: 'financialdetails-default-budgetitems',
        fields: ['category', 'amount'],
        fieldToCheck: 'amount',
        itemCount: 10
    }
    items = pushToArray(items, item);

    item = {
        addButtonName: 'financialdetails-fundingreceipts-addFundingReceipt',
        firstPart: 'financialdetails-fundingreceipts',
        fields: ['date', 'amount'],
        fieldToCheck: 'date',
        itemCount: 10
    }
    items = pushToArray(items, item);

    setupItems($.extend(true, {}, items));

    // Funding agency autocomplete
    $('#financialdetails-fundingagency-id-element').comboSelect(baseUrl+'/api/agencies.json', {
        resultsProperty: 'agencies',
        initialValue: $('#financialdetails-fundingagency-name').val()
    });

    $("#financialdetails-default-budget").blur(function(){
        calculateBudgetWithFPA();
    });
    $("#financialdetails-default-budgetfpa").blur(function(){
        calculateBudgetWithFPA();
    });
    calculateBudgetWithFPA(); // Μια φορά στο load της σελίδας

    var origfundingframework = $.extend(true, {}, $("#financialdetails-default-fundingframework-fundingframeworkid").val());
    var origopprogramme = $.extend(true, {}, $("#financialdetails-default-opprogramme-opprogrammeid").val());
    $("#financialdetails-default-fundingframework-fundingframeworkid").change(function(){
        updateOpProgrammes(this, false, origfundingframework[0], origopprogramme[0]);
    });
    updateOpProgrammes($("#financialdetails-default-fundingframework-fundingframeworkid"), true, origfundingframework[0], origopprogramme[0]);

    $.Calculation.setDefaults({
            // a regular expression for detecting European-style formatted numbers
            reNumbers: /(-?\$?)(\d+(\.\d{3})*(,\d{1,})?|,\d{1,})/g
            // define a procedure to convert the string number into an actual usable number
            , cleanseNumber: function (v){
                    // cleanse the number one more time to remove extra data (like commas and dollar signs)
                    // use this for European numbers: v.replace(/[^0-9,\-]/g, "").replace(/,/g, ".")
                    if(!IsNumeric(v)) {
                        return 0.0;
                    } else {
                        return v.replace(/[^0-9,\-]/g, "").replace(/,/g, ".");
                    }
            }
    });

    $('#financialdetails-default-budgetitems-sum').val($(".calcSum").sum());
    $(".calcSum").keyup(function() {
        $('#financialdetails-default-budgetitems-sum').val($(".calcSum").sum());
    });

    $("#fieldset-budgetitems").prepend('<div><div class="tableSimLeft expenditureth">Κατηγορία Δαπάνης</div><div class="tableSimRight amountth">Ποσό</div></div><div class="tableSimClear"></div>');
    
    showBudgetItemsTotal();
});

function calculateBudgetWithFPA() {
//       x.xxx,xx
    var a = parseFloat($("#financialdetails-default-budget").val().ReplaceAll(".","").ReplaceAll(",","."));
//       xxxx.xx
    var b = parseFloat($("#financialdetails-default-budgetfpa").val().ReplaceAll(".","").ReplaceAll(",","."));

    if(isNaN(b)) {
        $("#financialdetails-default-budgetwithfpa").val(a);
    } else {
        $("#financialdetails-default-budgetwithfpa").val(a+b);
    }
    $("#financialdetails-default-budgetwithfpa").formatNumber({format:"#,###.00", locale:"gr"});
}

var original_addItem = addItem;
var addItem = (function(item) {
    var result = original_addItem(item);
    showBudgetItemsTotal();
    return result;
});

var original_removeItem = removeItem;
var removeItem = (function(item) {
    var result = original_removeItem(item);
    $('#financialdetails-default-budgetitems-sum').val($(".calcSum").sum());
    showBudgetItemsTotal();
    return result;
});

function showBudgetItemsTotal() {
    var visible = false;
    for(var i = 1; i <= 10; i++) {
        if($('#fieldset-financialdetails-default-budgetitems-'+i).is(':visible')) {
            visible = true;
        }
    }
    if(visible == false) {
        $('.expenditureth').hide();
        $('.amountth').hide();
        $('#financialdetails-default-budgetitems-sum-div').hide();
    } else {
        $('.expenditureth').show();
        $('.amountth').show();
        $('#financialdetails-default-budgetitems-sum-div').show();
    }
}

function updateOpProgrammes(fundingframeworkid, first, origfundingframework, origopprogramme) {
    $("#financialdetails-default-opprogramme-opprogrammeid").html('<option value="">Παρακαλώ περιμένετε</option>');
    $("#submit").attr('disabled', 'disabled');
    $.getJSON(baseUrl+'/api/lists/fundingframeworks/'+$(fundingframeworkid).val()+'.json', function(data){
        var html = '';
        $.each(data.opprogrammes, function(key, val) {
            html += '<option value="' + val.opprogrammeid + '">' + val.opprogrammename + '</option>';
        });
        $("#financialdetails-default-opprogramme-opprogrammeid").html(html);
        if($("#financialdetails-default-fundingframework-fundingframeworkid").val() == origfundingframework && typeof origopprogramme != "undefined") {
            $("#financialdetails-default-opprogramme-opprogrammeid").val(origopprogramme);
        }
        $("#submit").attr('disabled', '');
        if(first == true) {
            initialFormSetup();
        }
    });
}