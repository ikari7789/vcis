$(document).ready(function() {
	
	/*for ($floor = 1; $floor <= $('#floorNum').val(); $floor++)
		$('#floors').append('<div class="row">\n\t<label for="Floor['+$floor+'][map_image]">Floor '+$floor+' image</label>\n\t<input id="Floor_'+$floor+'_map_image" type="file" name="Floor['+$floor+'][map_image]" value="" maxlength="255" size="60">\n</div>');*/
	
	$('#floorNum').on('change', function() {
		$numOfFloors = $(this).val();
		$floorDiv = $('#floors');
		$floorDiv.children().remove();
		for ($floor = 1; $floor <= $numOfFloors; $floor++)
		{
			$floorDiv.append('<div class="row">\n\t<label for="Floor['+$floor+'][map_image]">Floor '+$floor+' image</label>\n\t<input id="Floor_'+$floor+'_map_image" type="file" name="Floor['+$floor+'][map_image]" value="" maxlength="255" size="60">\n</div>');
		}
	});
});