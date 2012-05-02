$(document).ready(function(){
  $("#help li a").toggle(
	  	function() {
	  		$(this).addClass('highlight');
	  		var menuItem = $(this).parent('li');
	  		menuItem.find('div').animate({
	  			opacity: 1,
				height: 'toggle'
			}, 500);
	  		$(this).find('span').html('-');
	  	},
  		function() {
  			$(this).removeClass('highlight');
  			var menuItem = $(this).parent('li');
  			menuItem.find('div').animate({
  				opacity: 0,
				height: 'toggle'
			}, 500);
  			$(this).find('span').html('+');
  		}
  );  
});