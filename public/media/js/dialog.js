//		$( '<div id="dialog" title="MIS help Text"><p>something to help you fill this field</p></div> ' ).dialog({
//			autoOpen: false,
//			show: "blind",
//			hide: "blind"
//		});
//
//		$( "#popUp" ).click(function() {
//			$( "#dialog" ).dialog( "open" );
//			return false;
//		});
$(function() {
    $(".lockeditem").css('cursor','help');
    $(".lockeditem").qtip({content: 'Ο σύνδεσμος αυτός θα γίνει διαθέσιμος όταν συμπληρωθούν και υποβληθούν τα "Βασικά Στοιχεία Έργου" ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'topleft', tooltip: 'bottomright'}}});

    $("#budgetWithFPA-element").append('<img src=' + baseUrl + '/images/tip.gif id="budgetWithFPA-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;">');
    $("#budgetWithFPA-elementPopUp").qtip({content: 'Προυπολογισμος Εργου * ΦΠΑ ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'topleft', tooltip: 'bottomright'}}});

    $("#basicdetails-default-mis-element").append('<img src=' + baseUrl + '/images/tip.gif id="basicdetails-default-mis-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;z">');
    $("#basicdetails-default-mis-elementPopUp").qtip({content: 'Σε περίπτωση που δεν υπάρχει MIS συμπληρώνετε τον αριθμό πρωτοκόλλου της σύμβασης φορέα ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'topleft', tooltip: 'bottomright'}}});

    $("#basicdetails-default-startdate-element").append('<img src=' + baseUrl + '/images/tip.gif id="basicdetails-default-startdate-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;">');
    $("#basicdetails-default-startdate-elementPopUp").qtip({content: 'Ημερομηνία έναρξης/λήξης του φυσικού αντικειμένου του έργου ή της σύμβασης ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'topleft', tooltip: 'bottomright'}}});

    $("#basicdetails-default-refnumstart-element").append('<img src=' + baseUrl + '/images/tip.gif id="basicdetails-default-refnumstart-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;">');
    $("#basicdetails-default-refnumstart-elementPopUp").qtip({content: 'Αν δεν υπάρχει απόφαση ένταξης συμπληρώστε την απόφαση ανάθεσης του έργου απο τρίτο ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'bottomright', tooltip: 'topright'}}});

    $("#basicdetails-default-iscomplex-element").append('<img src=' + baseUrl + '/images/tip.gif id="basicdetails-default-iscomplex-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;">');
    $("#basicdetails-default-iscomplex-elementPopUp").qtip({content: 'Σύνθετα ονομάζονται τα έργα που περιέχουν υποέργα ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'topleft', tooltip: 'bottomright'}}});
    
    $("#basicdetails-supervisor-searchField-element").append('<img src=' + baseUrl + '/images/tip.gif id="basicdetails-supervisor-searchField-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;">');
    $("#basicdetails-supervisor-searchField-elementPopUp").qtip({content: 'Σημείωση: Σε ορισμένες περιπτώσεις η λίστα ενδέχεται να καθυστερήσει να εμφανιστεί για εως και 10 δευτερόλεπτα ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'topleft', tooltip: 'bottomright'}}})
    
    $("#apasxoloumenoi-view-plhrwteo").append('<img src=' + baseUrl + '/images/tip.gif id="apasxoloumenoi-view-plhrwteo-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;">');
    $("#apasxoloumenoi-view-plhrwteo-elementPopUp").qtip({content: 'Υπολογίζεται αυτόματα με βάση τις ώρες που δηλώθηκαν στα μηνιαία φύλλα παρακολούθησης, συναρτήσει του ωρομισθίου για τα αντίστοιχα παραδοτέα ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'topleft', tooltip: 'bottomright'}}});

    $("#default-employee-ldapusername-element").append('<img src=' + baseUrl + '/images/tip.gif id="default-employee-ldapusername-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;">');
    $("#default-employee-ldapusername-elementPopUp").qtip({content: 'Το συγκεκριμένο πεδίο είναι απαραίτητο αν ο απασχολούμενος πρέπει να υποβάλλει φύλλα χρονοχρέωσης ',show: { when: { event: 'mouseenter' }},position:{corner:{target: 'topleft', tooltip: 'bottomright'}}});
});