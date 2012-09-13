$(document).ready(function() {
    $("#default-employee-afm").autocomplete(baseUrl+'/api/employees/', {
                                                delay: 50,
                                                matchSubset: true,
                                                matchContains: true,
                                                minChars: 2,
                                                parse: parseXML,
                                                formatItem: formatItem,
                                                formatResult: formatResult,
                                                extraParams: {
                                                    q: '',
                                                    afm: function () { return escape($("#default-employee-afm").val()); }
                                                }
                                             });
    $("#default-employee-surname").autocomplete(baseUrl+'/api/employees/', {
                                                delay: 50,
                                                matchSubset: true,
                                                matchContains: true,
                                                minChars: 2,
                                                parse: parseXML,
                                                formatItem: formatItem,
                                                formatResult: formatResult,
                                                extraParams: {
                                                    q: '',
                                                    surname: function () { return escape($("#default-employee-surname").val()); }
                                                }
                                             });
    $("#default-employee-afm,#default-employee-surname").result(function(event, data, formatted) {
        if(data != false) {
            selectNewUser(formatted);
        }
    });
});

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

function selectNewUser(afm) {
    $('#default-employee-afm').attr('disabled', 'true');
    $('#default-employee-doy').attr('disabled', 'true');
    $('#default-employee-adt').attr('disabled', 'true');
    $('#default-employee-surname').attr('disabled', 'true');
    $('#default-employee-firstname').attr('disabled', 'true');
    $('#default-employee-address').attr('disabled', 'true');
    $('#default-employee-ldapusername').attr('disabled', 'true');
    $('#default-employee-maxhours').attr('disabled', 'true');

    $('#default-employee-afm').setOptions({minChars: 999999});
    $('#default-employee-afm').val('Παρακαλώ περιμένετε');
    $('#default-employee-doy').val('Παρακαλώ περιμένετε');
    $('#default-employee-adt').val('Παρακαλώ περιμένετε');
    $('#default-employee-surname').val('Παρακαλώ περιμένετε');
    $('#default-employee-firstname').val('Παρακαλώ περιμένετε');
    $('#default-employee-address').val('Παρακαλώ περιμένετε');
    $('#default-employee-ldapusername').val('Παρακαλώ περιμένετε');
    $('#default-employee-maxhours').val('Παρακαλώ περιμένετε');
    
    $.ajax({
        type: "GET",
        url: baseUrl+'/api/employees/'+afm+'.json',
        dataType: "json",
        error: function(xml) { $('#default-employee-afm').val('Παρουσιάστηκε σφάλμα'); },
        success: function(employee) {
            $('#default-employee-afm').val(employee.afm);
            $('#default-employee-doy').val(employee.doy);
            $('#default-employee-adt').val(employee.adt);
            $('#default-employee-surname').val(employee.surname);
            $('#default-employee-firstname').val(employee.firstname);
            $('#default-employee-address').val(employee.address);
            $('#default-employee-ldapusername').val(employee.ldapusername);
            $('#default-employee-maxhours').val(employee.maxhours);

            $('#default-employee-afm').attr('disabled', '');
            $('#default-employee-doy').attr('disabled', '');
            $('#default-employee-adt').attr('disabled', '');
            $('#default-employee-surname').attr('disabled', '');
            $('#default-employee-firstname').attr('disabled', '');
            $('#default-employee-address').attr('disabled', '');
            $('#default-employee-ldapusername').attr('disabled', '');
            $('#default-employee-maxhours').attr('disabled', '');
            $('#default-employee-afm').setOptions({minChars: 2});
        }
    });
}