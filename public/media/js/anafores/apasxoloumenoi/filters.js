$(document).ready(function() {
    if(window.location.hash == "#filtersexpanded") {
     $('#fieldset-filters').collapse({ closed : false });
    } else {
     $('#fieldset-filters').collapse({ closed : true });
    }
    $('#fieldset-filters').find("legend:first").click(function() {
     if(window.location.hash == "#filtersexpanded") {
        window.location.hash = '';
     } else {
         window.location.hash = 'filtersexpanded';
     }
    });

    $("#filters-search-element").append('<img src=' + baseUrl + '/images/tip.gif id="filters-search-elementPopUp" style="width: 18px; margin-right: -18px; cursor: pointer;">');
    $("#filters-search-elementPopUp").qtip({content: 'Η αναζήτηση αφορά τα πεδία "ΑΦΜ" και "Όνομα"/"Επωνυμία" ',show: { when: { event: 'mouseenter' }}})
});