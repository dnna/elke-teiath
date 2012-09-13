$(document).ready(function() {
    var supervisorFields = ['supervisor-capacity', 'supervisor-departmentname', 'supervisor-sector', 'supervisor-phone', 'supervisor-email'];
    $("#toggleSupervisorDetails").click(function() { toggleDetails(supervisorFields); });
    toggleDetails(supervisorFields);
});