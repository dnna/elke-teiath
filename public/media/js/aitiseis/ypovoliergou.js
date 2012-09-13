$(document).ready(function() {
    var supervisorFields = ['capacity', 'departmentname', 'sector', 'phone', 'email'];
    $("#toggleSupervisorDetails").click(function() { toggleDetails('supervisor', supervisorFields); });
    toggleDetails('supervisor', supervisorFields);
});