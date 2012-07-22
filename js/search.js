$(document).ready(function() {
	$('#advanced_search').submit(function() {
		
		var rangeVals = $('input[type=text]');
		
		rangeVals.each(function() {
			if ($(this).val() == '') {
				$(this).attr('disabled','disabled');
			}
		});
	});
	
	$('.title').toggle(
		function() {
	  		$(this).addClass('highlight');
	  		var menuItem = $(this).parent('.feature');
	  		menuItem.find('.detail').animate({
	  			opacity: 1,
				height: 'toggle'
			}, 500);
	  		$(this).find('span').html('-');
	  	},
  		function() {
  			$(this).removeClass('highlight');
  			var menuItem = $(this).parent('.feature');
  			menuItem.find('.detail').animate({
  				opacity: 0,
				height: 'toggle'
			}, 500);
  			$(this).find('span').html('+');
  		}
	);
});
