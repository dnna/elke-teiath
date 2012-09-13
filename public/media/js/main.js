refreshInterval = 60000;
$.idleTimer(59000);

sessionTimeLeft = sessionTimeout - 2 * refreshInterval; // Less time than the real session to avoid bugs
$.ajaxSetup({
    cache:false
});

$.fn.preventDoubleSubmit = function() {
    jQuery(this).submit(function() {
        if (this.beenSubmitted) {
            return false;
        } else {
            this.beenSubmitted = true;
            return true;
        }
    });
};

$(document).ready(function() {
//    textAreaAutogrow($);

    if (userLoggedIn) {
        window.setInterval(sessionRefresh, refreshInterval);
    }

    setTimeout(function(){
        $("div.flashmessages").fadeOut("slow", function () {
        $("div.flashmessages").remove();
        });
    }, 5000);

    // Prevent double form submissions
    $('form').preventDoubleSubmit();


    //slide effect sto submenu
    $('.submenu').hide().slideDown('slow');

    $(".formatFloat").blur(function() {
        var usfloat = parseFloat($(this).val().ReplaceAll(".","").ReplaceAll(",","."));
        if(usfloat != 0) {
            $(this).val(usfloat);
        }
        $(this).formatNumber({
            format:"#,###.00", 
            locale:"gr"
        });
    });


    //Να μεινουν και τα 2 datepickers
    if(typeof $(".usedatepicker:not([readonly='readonly'])").datepicker == 'function') { // Έλεγχος αν υπάρχει το datepicker
        $(".usedatepicker:not([readonly='readonly'])").datepicker({
            dateFormat: 'dd/mm/yy' , 
            changeMonth: true , 
            changeYear: true , 
            dayNamesMin: ['ΚΥ', 'ΔΕ', 'ΤΡ', 'ΤΕ', 'ΠΕ', 'ΠΑ', 'ΣΑ'] , 
            monthNamesShort:['Ιανουάριος', 'Φεβρουάριος', 'Μάρτιος', 'Απρίλιος', 'Μαΐος', 'Ιούνιος', 'Ιούλιος', 'Αύγουστος', 'Σεπτέμβριος', 'Οκτώβριος', 'Νοέμβριος', 'Δεκέμβριος'] , 
            firstDay: 1 ,
            showAnim: 'slideDown'
        });
    }

    if(typeof $("#startDate:not([readonly='readonly']), #endDate:not([readonly='readonly'])").datepicker == 'function') { // Έλεγχος αν υπάρχει το datepicker
        var dates = $("#startDate:not([readonly='readonly']), #endDate:not([readonly='readonly'])").datepicker({
            dateFormat: 'dd/mm/yy' ,
            changeMonth: true,
            changeYear: true ,
            dayNamesMin: ['ΚΥ', 'ΔΕ', 'ΤΡ', 'ΤΕ', 'ΠΕ', 'ΠΑ', 'ΣΑ'] ,
            firstDay: 1,
            showAnim: 'slideDown',
            monthNamesShort:['Ιανουάριος', 'Φεβρουάριος', 'Μάρτιος', 'Απρίλιος', 'Μαΐος', 'Ιούνιος', 'Ιούλιος', 'Αύγουστος', 'Σεπτέμβριος', 'Οκτώβριος', 'Νοέμβριος', 'Δεκέμβριος'] ,
            onSelect: function(selectedDate) {
                var option = this.id == "startDate" ? "minDate" : "maxDate",
                instance = $(this).data("datepicker"),
                date = $.datepicker.parseDate(
                    instance.settings.dateFormat ||
                    $.datepicker._defaults.dateFormat,
                    selectedDate, instance.settings);
                dates.not(this).datepicker("option", option, date);
            }
        });
    }
    //disable form submit with enter
    textboxes = $("input:text");

    if ($.browser.mozilla) {
        $(textboxes).keypress(checkForEnter); //o mozzila perimenei diaforetiko event
    } else {
        $(textboxes).keydown(checkForEnter);
    }

    function checkForEnter(event) {
        if (event.keyCode == 13) {
            currentTextboxNumber = textboxes.index(this);

            if (textboxes[currentTextboxNumber + 1] != null) {
                nextTextbox = textboxes[currentTextboxNumber + 1];
                nextTextbox.select();
                return false;
            }

            event.preventDefault();
            return false;
        }
    }


    //filter table data upoerga
    $('input#id_search').quicksearch('table tbody tr');

    //filtrarisma listwn TODO prepei na ginetai pio apla
    $("#searchlist").next().next().next().addClass("searchlist");
    $("#searchlist").quicksearch(".searchlist tbody tr");

    //    fade in sthn au8entikopoihsh
    $('#fieldset-loginForm').fadeOut(0).fadeIn(900);


    //table sorting

    $("#ypoergacollapsible").tablesorter({
        headers:{
            0 : {
                sorter: false
            }, 
            3 : {
                sorter: false
            }, 
            4 : {
                sorter: false
            }
        }
    });


$('#descriptionText').fadeOut(0).slideDown(900);


    ////    hide empty fieldsets
    //      $("#committee-element").prepend('<button id="show" class="addButton">Επιστημονική Επιτροπή Έργου</button>');
    //    $("#fieldset-committee").hide();
    //    $("#show").live('click',function(){
    // $("#show").hide();
    //          $("#fieldset-committee").slideDown();


    //     $('#fieldset-committee legend').click(function(){
    //   $(this).siblings().slideToggle("slow");
    //});

    $('#fieldset-committee').collapse({
        closed : determineFieldsetCollapsibleStatus($('#fieldset-committee'))
    });
    $('#fieldset-modifications').collapse({
        closed : determineFieldsetCollapsibleStatus($('#fieldset-modifications'))
    });
    $('#fieldset-partners').collapse({
        closed : determineFieldsetCollapsibleStatus($('#fieldset-partners'))
    });
    //     $('#fieldset-budgetitems').collapse({ closed : determineFieldsetCollapsibleStatus($('#fieldset-budgetitems')) });
    $('#fieldset-fundingreceipts').collapse({
        closed : determineFieldsetCollapsibleStatus($('#fieldset-fundingreceipts'))
    });
    $('#fieldset-personnelcategories').collapse({
        closed : determineFieldsetCollapsibleStatus($('#fieldset-personnelcategories'))
    });
    //     $('#fieldset-employees').collapse({ closed : determineFieldsetCollapsibleStatus($('#fieldset-employees')) });


    $("#default-label").hide();

}); //document ready


$("textarea").TextAreaExpander(21,600);


//function textAreaAutogrow($) {
//    // Autogrow text areas
//    if($.find('textarea').length > 0) {
//        if(typeof $.find('textarea').autoGrow == 'undefined') {
//            jQuery.getScript('/'+baseUrl+'media/js/jquery.autogrowtextarea.js', function() {
//               $.find('textarea').autoGrow();
//            })
//        } else {
//            $.find('textarea').autoGrow();
//        }
//    }
//}




function sessionRefresh() {
    sessionTimeLeft = sessionTimeLeft - refreshInterval;
    if ($.data(document, 'idleTimer') == 'active') {
        $.get(baseUrl + '/Polling');
        sessionTimeLeft = sessionTimeout - 2 * refreshInterval;
    }
/* else {
     if(sessionTimeLeft <= 0) {
     window.location.replace(baseUrl+'/Login/logout');
     }
     }*/
}

function determineFieldsetCollapsibleStatus(item) {
    if (item.find('dl > fieldset:first > input:hidden[id*=isvisible]').val() == 1 || item.find('dl > input:hidden[id*=isvisible]').val() == 1) {
        return false;
    } else {
        return true;
    }
}

String.prototype.ReplaceAll = function(stringToFind, stringToReplace) {
    var temp = this;
    var index = temp.indexOf(stringToFind);
    while (index != -1) {
        temp = temp.replace(stringToFind, stringToReplace);
        index = temp.indexOf(stringToFind);
    }
    return temp;
}

function IsNumeric(sText) {
    var ValidChars = "0123456789.,";
    var IsNumber = true;
    var Char;

    if(sText.length <= 0) {
        IsNumber = false;
    }
    for (i = 0; i < sText.length && IsNumber == true; i++) {
        Char = sText.charAt(i);
        if ((i == 0) && (Char == "-")) // check first character for minus sign
            continue;
        if (ValidChars.indexOf(Char) == -1) {
            IsNumber = false;
        }
    }
    return IsNumber;
}