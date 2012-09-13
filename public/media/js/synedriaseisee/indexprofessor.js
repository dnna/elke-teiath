$(document).ready(function() {
    var $calendar = $('#calendar');
    if(typeof barebone != "undefined" && barebone == true) {
        $calendar.removeClass('calendar');
    }
    var $loadingDialog = $("#loadingdialog");
    $calendar.weekCalendar({
        readonly: true,
        timeslotsPerHour: 1,
        allowCalEventOverlap: true,
        shortMonths: ['Ιαν', 'Φεβ', 'Μαρ', 'Απρ', 'Μαι', 'Ιουν', 'Ιουλ', 'Αυγ', 'Σεπτ', 'Οκτ', 'Νοε', 'Δεκ'],
        longDays: ['Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο'],
        buttonText: {
            today : "σήμερα", 
            lastWeek : "προηγούμενη", 
            nextWeek : "επόμενη"
        },
        businessHours: {
            start: 8,
            end: 20,
            limitDisplay: true
        },
        use24Hour: true,
        daysToShow: 5,
        height: function($calendar){
            if(typeof barebone != "undefined" && barebone == true) {
                return $(window).height();
            } else {
                return $(window).height()/1.2;
            }
        },
        timeslotHeight: 40,
        //readonly: true,
        data: baseUrl+'/api/synedriaseisee.json?iso8601=true&nowrap=true',
        calendarBeforeLoad: function(calendar) {
            $loadingDialog.dialog({
                modal: true,
                title: 'Loading',
                closeOnEscape: false,
                draggable: false,
                resizable: false,
                open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
                width:'auto'
            });
        },
        calendarAfterLoad: function(calendar) {
            $loadingDialog.dialog("destroy");
            $loadingDialog.remove();
        },
        eventRender : function(calEvent, eventElement) {
            if(typeof calEvent.cssClass != "undefined") {
                eventElement.addClass(calEvent.cssClass);
            }
        },
        eventClick : function(calEvent, $event) {

            if (calEvent.readOnly) {
                return;
            }
            if (!$event.hasClass('synedriasi')) {
                return;
            }

            var $dialogContent = $("#event_edit_container").clone();

            $dialogContent.dialog({
                modal: true,
                width:'auto',
                title: calEvent.title,
                open: function() {
                    $dialogContent.load(baseUrl+"/synedriaseisee/index/eventview/stripped/true/id/"+calEvent.id);
                },
                close: function() {
                    $dialogContent.dialog("destroy");
                    $dialogContent.remove();
                    $calendar.weekCalendar("removeUnsavedEvents");
                },
                buttons: {
                    Κλείσιμο : function() {
                        $dialogContent.dialog("close");
                        $dialogContent.remove();
                    }
                }
            }).show();

            $(window).resize().resize(); //fixes a bug in modal overlay size ??

        }
    });
});