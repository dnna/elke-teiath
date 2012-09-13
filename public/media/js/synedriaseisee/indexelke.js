$(document).ready(function() {
    var id = 100;
    var $calendar = $('#calendar');
    if(typeof barebone != "undefined" && barebone == true) {
        $calendar.removeClass('calendar');
    }
    var $loadingDialog = $("#loadingdialog");
    $calendar.weekCalendar({
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
                open: function(event, ui) {$(".ui-dialog-titlebar-close").hide();},
                width:'auto'
            });
        },
        calendarAfterLoad: function(calendar) {
            $loadingDialog.dialog("destroy");
            $loadingDialog.remove();
        },
        draggable: function(calEvent, eventElement) {
            return false;
        },
        resizable: function(calEvent, eventElement) {
            return false;
        },
        eventRender : function(calEvent, eventElement) {
            if(typeof calEvent.cssClass != "undefined") {
                eventElement.addClass(calEvent.cssClass);
            }
        },
        eventNew : function(calEvent, $event) {
            var $dialogContent = $("#event_edit_container").clone();

            $dialogContent.dialog({
                modal: true,
                width:'auto',
                title: "Νέα Συνεδρίαση",
                open: function() {
                    $dialogContent.load(baseUrl+"/synedriaseisee/index/eventedit", function() {
                        var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
                        var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
                        setupStartAndEndTimeFields(startField, endField, calEvent, $calendar.weekCalendar("getTimeslotTimes", calEvent.start));
                        $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("enable");

                        setupSubjects();
                    });
                },
                close: function() {
                    $dialogContent.dialog("destroy");
                    $dialogContent.remove();
                    $calendar.weekCalendar("removeUnsavedEvents");
                },
                buttons: {
                    Αποθήκευση : function() {
                        var startField = $dialogContent.find("select[name='start']");
                        var endField = $dialogContent.find("select[name='end']");
                        var numField = $dialogContent.find("input[name='num']");

                        calEvent.id = id;
                        id++;
                        calEvent.start = new Date(startField.val());
                        calEvent.end = new Date(endField.val());
                        calEvent.num = numField.val();
                        var i = 1;
                        calEvent.subjects = {};
                        while($dialogContent.find("#fieldset-subjects-"+i).length > 0) {
                            var $curSubject = $dialogContent.find("#fieldset-subjects-"+i);
                            $.each($curSubject.find("input:enabled"), function(index, value) {
                                calEvent.subjects[value.name] = value.value;
                            })
                            i++;
                        }

                        if(calEvent.num != "") {
                            $dialogContent.html('Γίνεται αποθήκευση...');
                            $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("disable");
                            $(".ui-dialog-buttonpane button:contains('Ακύρωση')").button("disable");
                            $(".ui-dialog-titlebar-close").hide();
                            addSynedriasi(calEvent, function() {
                                if(typeof barebone != "undefined" && barebone == true) {
                                    $calendar.weekCalendar("removeUnsavedEvents");
                                    //$calendar.weekCalendar("updateEvent", calEvent);
                                    $dialogContent.dialog("close");
                                    $dialogContent.remove();
                                    $calendar.weekCalendar("refresh");
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            alert('Παρακαλώ συμπληρώστε όλα τα υποχρεωτικά πεδία');
                        }
                    },
                    Ακύρωση : function() {
                        $dialogContent.dialog("close");
                        $dialogContent.remove();
                    }
                }
            }).show();
            $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("disable");
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
                    $dialogContent.load(baseUrl+"/synedriaseisee/index/eventedit/id/"+calEvent.id, function() {
                        var startField = $dialogContent.find("select[name='start']").val(calEvent.start);
                        var endField = $dialogContent.find("select[name='end']").val(calEvent.end);
                        setupStartAndEndTimeFields(startField, endField, calEvent, $calendar.weekCalendar("getTimeslotTimes", calEvent.start));
                        $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("enable");
                        $(".ui-dialog-buttonpane button:contains('Διαγραφή')").button("enable");

                        setupSubjects();
                    });
                },
                close: function() {
                    $dialogContent.dialog("destroy");
                    $dialogContent.remove();
                    $calendar.weekCalendar("removeUnsavedEvents");
                },
                buttons: {
                    Αποθήκευση : function() {
                        var startField = $dialogContent.find("select[name='start']");
                        var endField = $dialogContent.find("select[name='end']");
                        var numField = $dialogContent.find("input[name='num']");

                        calEvent.start = new Date(startField.val());
                        calEvent.end = new Date(endField.val());
                        calEvent.num = numField.val();
                        var i = 1;
                        calEvent.subjects = {};
                        while($dialogContent.find("#fieldset-subjects-"+i).length > 0) {
                            var $curSubject = $dialogContent.find("#fieldset-subjects-"+i);
                            $.each($curSubject.find("input:enabled"), function(index, value) {
                                calEvent.subjects[value.name] = value.value;
                            })
                            i++;
                        }

                        if(calEvent.num != "") {
                            $dialogContent.html('Γίνεται αποθήκευση...');
                            $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("disable");
                            $(".ui-dialog-buttonpane button:contains('Διαγραφή')").button("disable");
                            $(".ui-dialog-buttonpane button:contains('Ακύρωση')").button("disable");
                            $(".ui-dialog-titlebar-close").hide();
                            modifySynedriasi(calEvent, function() {
                                if(typeof barebone != "undefined" && barebone == true) {
                                    //$calendar.weekCalendar("updateEvent", calEvent);
                                    $dialogContent.dialog("close");
                                    $dialogContent.remove();
                                    $calendar.weekCalendar("refresh");
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            alert('Παρακαλώ συμπληρώστε όλα τα υποχρεωτικά πεδία');
                        }
                    },
                    Διαγραφή : function() {
                        if(confirm('Θέλετε σίγουρα να διαγράψετε αυτή τη συνεδρίαση;')) {
                            $dialogContent.html('Γίνεται διαγραφή...');
                            $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("disable");
                            $(".ui-dialog-buttonpane button:contains('Διαγραφή')").button("disable");
                            $(".ui-dialog-buttonpane button:contains('Ακύρωση')").button("disable");
                            $(".ui-dialog-titlebar-close").hide();
                            deleteSynedriasi(calEvent.id, function() {
                                if(typeof barebone != "undefined" && barebone == true) {
                                    $calendar.weekCalendar("removeEvent", calEvent.id);
                                    $dialogContent.dialog("close");
                                    $dialogContent.remove();
                                } else {
                                    window.location.reload();
                                }
                            });
                        }
                    },
                    Ακύρωση : function() {
                        $dialogContent.dialog("close");
                        $dialogContent.remove();
                    }
                }
            }).show();

            $(".ui-dialog-buttonpane button:contains('Αποθήκευση')").button("disable");
            $(".ui-dialog-buttonpane button:contains('Διαγραφή')").button("disable");
            $(window).resize().resize(); //fixes a bug in modal overlay size ??

        }
    });

    /*
    * Sets up the start and end time fields in the calendar event
    * form for editing based on the calendar event being edited
    */
    function setupStartAndEndTimeFields($startTimeField, $endTimeField, calEvent, timeslotTimes) {

        for (var i = 0; i < timeslotTimes.length; i++) {
            var startTime = timeslotTimes[i].start;
            var endTime = timeslotTimes[i].end;
            var startSelected = "";
            if (startTime.getTime() === calEvent.start.getTime()) {
                startSelected = "selected=\"selected\"";
            }
            var endSelected = "";
            if (endTime.getTime() === calEvent.end.getTime()) {
                endSelected = "selected=\"selected\"";
            }
            $startTimeField.append("<option value=\"" + startTime + "\" " + startSelected + ">" + timeslotTimes[i].startFormatted + "</option>");
            $endTimeField.append("<option value=\"" + endTime + "\" " + endSelected + ">" + timeslotTimes[i].endFormatted + "</option>");

        }
        $endTimeOptions = $endTimeField.find("option");
        $startTimeField.trigger("change");
    }

    var $endTimeField = $("select[name='end']");
    var $endTimeOptions = $endTimeField.find("option");

    //reduces the end time options to be only after the start time options.
    $("select[name='start']").change(function() {
        var startTime = $(this).find(":selected").val();
        var currentEndTime = $endTimeField.find("option:selected").val();
        $endTimeField.html(
            $endTimeOptions.filter(function() {
                return startTime < $(this).val();
            })
            );

        var endTimeSelected = false;
        $endTimeField.find("option").each(function() {
            if ($(this).val() === currentEndTime) {
                $(this).attr("selected", "selected");
                endTimeSelected = true;
                return false;
            }
        });

        if (!endTimeSelected) {
            //automatically select an end date 2 slots away.
            $endTimeField.find("option:eq(1)").attr("selected", "selected");
        }

    });
});

function setupSubjects() {
    var items = new Array();
    var item = {
        addButtonName: 'subjects-addSubject',
        removeSpecialFunc: removeSubject,
        firstPart: 'subjects',
        fields: ['num', 'title', 'aitisi-aitisiname'],
        fieldToCheck: 'title',
        itemCount: 20
    };
    items = pushToArray(items, item);
    setupItems($.extend(true, {}, items));

    for(var i = 1; i <= item.itemCount; i++) {
        if($('#subjects-'+i+'-aitisi-aitisiname').val() != "" && $('#subjects-'+i+'-aitisi-aitisiname').val() != "null") {
            $('#subjects-'+i+'-title').val($('#subjects-'+i+'-aitisi-aitisiname').val());
            $('#subjects-'+i+'-title').attr('disabled', 'disabled');
        }
    }
    function removeSubject(element) {
        $("#"+element).removeAttr('disabled');
        var $aitisiId = $("#"+element).parent().parent().parent().children('[id$="aitisiid"]');
        $aitisiId.val('null');
        var k = 1;
        for(var i = 1; i <= item.itemCount; i++) {
            if($('#subjects-'+i+'-aitisi-aitisiname').val() == "" || $('#subjects-'+i+'-aitisi-aitisiname').val() == "null" ||
                    'subjects-'+i+'-title' == element) {
                $('#subjects-'+i+'-num').val(k);
                k++;
            }
        }
    }
}

function addSynedriasi(calEvent, callback) {
    var params = {
        'start' : ISODateString(calEvent.start),
        'end' : ISODateString(calEvent.end),
        'num' : calEvent.num
    }
    params = $.extend({}, params, calEvent.subjects);
    $.post(baseUrl+'/api/synedriaseisee', params, callback);
}

function modifySynedriasi(calEvent, callback) {
    var params = {
        'start' : ISODateString(calEvent.start),
        'end' : ISODateString(calEvent.end),
        'num' : calEvent.num
    }
    params = $.extend({}, params, calEvent.subjects);
    $.ajax({
      type: 'PUT',
      url: baseUrl+'/api/synedriaseisee/'+calEvent.id,
      data: params,
      success: callback
    });
}

function deleteSynedriasi(id, callback) {
    $.ajax({
      type: 'DELETE',
      url: baseUrl+'/api/synedriaseisee/'+id,
      success: callback
    });
}

function ISODateString(d){
 function pad(n){return n<10 ? '0'+n : n}
 return d.getUTCFullYear()+'-'
      + pad(d.getUTCMonth()+1)+'-'
      + pad(d.getUTCDate())+'T'
      + pad(d.getUTCHours())+':'
      + pad(d.getUTCMinutes())+':'
      + pad(d.getUTCSeconds())+'Z'}
