$(function() {
	$("input[name='class_key']").click(function(){
	   $("input[name='class_key']").removeClass('current');
	   $(this).parent('li').addClass('current');
	});​
});