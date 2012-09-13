$(document).ready(function() {
    var items = new Array();
    var item = {
        addButtonName: 'employees-addEmployee',
        firstPart: 'employees',
        fields: ['employee-surname', 'employee-firstname', 'employee-address', 'employee-adt', 'employee-afm', 'employee-doy', 'startdate',
                          'enddate', 'category-id', 'manmonths', 'amount', 'specialty-id', 'comments'],
        fieldToCheck: 'employee-surname',
        itemCount: 20
    }
    items = pushToArray(items, item);
    setupItems($.extend(true, {}, items));

    $('#parentaitisi-aitisiid-element').comboSelect(baseUrl+'/api/aitiseis/ypovoliergou.json', {
        resultsProperty: 'aitiseis',
        initialValue: $('#parentaitisi-aitisiname').val(),
        extraParams: {
            'approved' : '1'
        }
    });

/*    var employeeFields = item.fields;
    employeeFields.splice(0, 2);

    function click1(aa) {
        $("#employees-toggleEmployeeDetails_"+aa).click(function() { toggleDetails(aa, 'employees', employeeFields, 'employees'); });
        toggleDetails(aa, 'employees', employeeFields, 'employees');
    }*/

    for(var i = 1; i <= 20; i++) {
        //click1(i);
        setupAutocomplete('#employees-'+i);
    }
});

function setupAutocomplete(prefix) {
    $(prefix+"-employee-afm").autocomplete(baseUrl+'/api/employees/', {
                                                delay: 50,
                                                matchSubset: true,
                                                matchContains: true,
                                                minChars: 2,
                                                parse: parseXML,
                                                formatItem: formatItem,
                                                formatResult: formatResult,
                                                extraParams: {
                                                    q: '',
                                                    afm: function () { return escape($(prefix+"-employee-afm").val()); }
                                                }
                                             });
    $(prefix+"-employee-surname").autocomplete(baseUrl+'/api/employees/', {
                                                delay: 50,
                                                matchSubset: true,
                                                matchContains: true,
                                                minChars: 2,
                                                parse: parseXML,
                                                formatItem: formatItem,
                                                formatResult: formatResult,
                                                extraParams: {
                                                    q: '',
                                                    surname: function () { return escape($(prefix+"-employee-surname").val()); }
                                                }
                                             });
    $(prefix+"-employee-afm,"+prefix+"-employee-surname").result(function(event, data, formatted) {
        if(data != false) {
            selectNewUser(formatted, prefix);
        }
    });
}

function parseXML(xml) {
    var results = [];
    $(xml).find('item').each(function() {
        var text = $.trim($(this).find('afm').text()+" "+$(this).find('name').text());
        var value = $.trim($(this).find('afm').text());
        results[results.length] = { 'data': { text: text, value: value },
            'result': text, 'value': value
        };
    });
    return results;
};

function formatItem(data) {
    return data.text;
};

function formatResult(data) {
    return data.text;
};

function selectNewUser(afm, prefix) {
    $(prefix+'-employee-afm').attr('disabled', 'true');
    $(prefix+'-employee-doy').attr('disabled', 'true');
    $(prefix+'-employee-adt').attr('disabled', 'true');
    $(prefix+'-employee-surname').attr('disabled', 'true');
    $(prefix+'-employee-firstname').attr('disabled', 'true');
    $(prefix+'-employee-address').attr('disabled', 'true');

    $(prefix+'-employee-afm').setOptions({minChars: 999999});
    $(prefix+'-employee-afm').val('Παρακαλώ περιμένετε');
    $(prefix+'-employee-doy').val('Παρακαλώ περιμένετε');
    $(prefix+'-employee-adt').val('Παρακαλώ περιμένετε');
    $(prefix+'-employee-surname').val('Παρακαλώ περιμένετε');
    $(prefix+'-employee-firstname').val('Παρακαλώ περιμένετε');
    $(prefix+'-employee-address').val('Παρακαλώ περιμένετε');

    $.ajax({
        type: "GET",
        url: baseUrl+'/api/employees/'+afm+'.json',
        dataType: "json",
        error: function(xml) { $(prefix+'-employee-afm').val('Παρουσιάστηκε σφάλμα'); },
        success: function(employee) {
            $(prefix+'-employee-afm').val(employee.afm);
            $(prefix+'-employee-doy').val(employee.doy);
            $(prefix+'-employee-adt').val(employee.adt);
            $(prefix+'-employee-surname').val(employee.surname);
            $(prefix+'-employee-firstname').val(employee.firstname);
            $(prefix+'-employee-address').val(employee.address);

            $(prefix+'-employee-afm').attr('disabled', '');
            $(prefix+'-employee-doy').attr('disabled', '');
            $(prefix+'-employee-adt').attr('disabled', '');
            $(prefix+'-employee-surname').attr('disabled', '');
            $(prefix+'-employee-firstname').attr('disabled', '');
            $(prefix+'-employee-address').attr('disabled', '');
            $(prefix+'-employee-afm').setOptions({minChars: 2});
        }
    });
}