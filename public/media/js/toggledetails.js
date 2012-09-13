function toggleDetails(parm1, parm2, parm3, parm4) {
    var aa;
    var prepend;
    var fields;
    var subforms;
    if (typeof(parm1) != "undefined" && typeof(parm2) != "undefined" && typeof(parm3) != "undefined" && typeof(parm4) != "undefined") {
        aa = parm1;
        prepend = parm2;
        fields = parm3;
        subforms = parm4;
    } else if (typeof(parm1) != "undefined" && typeof(parm2) != "undefined" && typeof(parm3) != "undefined") {
        aa = parm1;
        prepend = parm2;
        fields = parm3;
    } else if(typeof(parm1) != "undefined" && typeof(parm2) != "undefined") {
        prepend = parm1;
        fields = parm2;
    } else if(typeof(parm1) != "undefined") {
        fields = parm1;
    } else {
        alert('Error');
    }

    var finalprepend;
    if(typeof(prepend) != "undefined" && typeof(aa) != "undefined") {
        finalprepend = prepend+aa+'-';
    } else if(typeof(prepend) != "undefined") {
        finalprepend = prepend+'-';
    } else {
        finalprepend = '';
    }

    var curField;
    if($('#'+finalprepend+fields[0]).is(':visible')) {
        for(curField in fields) {
            hideField('#'+finalprepend+fields[curField]);
        }
    } else {
        for(curField in fields) {
            showField('#'+finalprepend+fields[curField]);
        }
    }

    if(typeof(subforms) != "undefined") {
        var curSubForm;
        for(curSubForm in subforms) {
            if($('fieldset.subform-'+finalprepend+subforms[curSubForm]).is(':visible')) {
                hideField('fieldset.subform-'+finalprepend+subforms[curSubForm]);
            } else {
                showField('fieldset.subform-'+finalprepend+subforms[curSubForm]);
            }
        }
    }
}

function hideField(fieldname) {
    $(fieldname+'-div').hide();
    $(fieldname+'-label').hide();
    $(fieldname+'-element').hide();
    $(fieldname).hide();
}

function showField(fieldname) {
    $(fieldname+'-div').fadeIn();
    $(fieldname+'-label').fadeIn();
    $(fieldname+'-element').fadeIn();
    $(fieldname).fadeIn();
}