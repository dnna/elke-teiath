$(document).ready(function() {
    var items = new Array();
    var item = {
        addButtonName: 'budgetitems-addBudgetItem',
        firstPart: 'budgetitems',
        fields: ['category', 'amount'],
        fieldToCheck: 'amount',
        //startItem: 2,
        itemCount: 10
    };
    items = pushToArray(items, item);

    setupItems($.extend(true, {}, items));

    // Funding agency autocomplete
    $('#fundingagency-id-element').comboSelect(baseUrl+'/api/agencies.json', {
        resultsProperty: 'agencies',
        initialValue: $('#fundingagency-name').val()
    });

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

    $('#budgetitems-sum').val($(".calcSum").sum());
    $(".calcSum").keyup(function() {
        $('#budgetitems-sum').val($(".calcSum").sum());
    });
});