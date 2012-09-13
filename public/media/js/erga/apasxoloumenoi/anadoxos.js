$(document).ready(function() {
    $("#default-agency-afm").autocomplete(baseUrl+'/api/contractors/', {
                                                delay: 50,
                                                matchSubset: true,
                                                matchContains: true,
                                                minChars: 2,
                                                parse: parseXML,
                                                formatItem: formatItem,
                                                formatResult: formatResult,
                                                extraParams: {
                                                    q: '',
                                                    afm: function () { return escape($("#default-agency-afm").val()); }
                                                }
                                             });
    $("#default-agency-name").autocomplete(baseUrl+'/api/contractors/', {
                                                delay: 50,
                                                matchSubset: true,
                                                matchContains: true,
                                                minChars: 2,
                                                parse: parseXML,
                                                formatItem: formatItem,
                                                formatResult: formatResult,
                                                extraParams: {
                                                    q: '',
                                                    name: function () { return escape($("#default-agency-name").val()); }
                                                }
                                             });
    $("#default-agency-afm,#default-agency-name").result(function(event, data, formatted) {
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

function selectNewUser(name) {
    $('#default-agency-afm').attr('disabled', 'true');
    $('#default-agency-doy').attr('disabled', 'true');
    $('#default-agency-name').attr('disabled', 'true');
    $('#default-agency-address').attr('disabled', 'true');

    $('#default-agency-afm').setOptions({minChars: 999999});
    $('#default-agency-afm').val('Παρακαλώ περιμένετε');
    $('#default-agency-doy').val('Παρακαλώ περιμένετε');
    $('#default-agency-name').val('Παρακαλώ περιμένετε');
    $('#default-agency-address').val('Παρακαλώ περιμένετε');
    
    $.ajax({
        type: "GET",
        url: baseUrl+'/api/contractors/'+name+'.json',
        dataType: "json",
        error: function(xml) { $('#default-agency-name').val('Παρουσιάστηκε σφάλμα'); },
        success: function(contractor) {
            $('#default-agency-afm').val(contractor.afm);
            $('#default-agency-doy').val(contractor.doy);
            $('#default-agency-name').val(contractor.name);
            $('#default-agency-address').val(contractor.address);

            $('#default-agency-afm').attr('disabled', '');
            $('#default-agency-doy').attr('disabled', '');
            $('#default-agency-name').attr('disabled', '');
            $('#default-agency-address').attr('disabled', '');
            $('#default-agency-afm').setOptions({minChars: 2});
        }
    });
}