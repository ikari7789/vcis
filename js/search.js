$(document).ready(function() {
	$('#advanced_search').submit(function() {
		
		var rangeVals = $('input[type=text]');
		
		rangeVals.each(function() {
			if ($(this).val() == '') {
				var searchElement;
				$(this);
			}
		});
			
		alert($(this).serialize());
		return false;
	});
});
