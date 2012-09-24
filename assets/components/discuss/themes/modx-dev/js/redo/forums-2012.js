// Forum Functions
$(function() {
    $('#F-Toggle-Options a.a-options').click(function(event) {
        event.preventDefault();
        $('#F-Toggle-List').slideToggle('fast');
    });
});