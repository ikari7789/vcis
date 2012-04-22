<?php
$this->pageTitle = 'Room List | '.Yii::app()->name;
$this->breadcrumbs=array(
	'List',
); ?>
<?php Yii::app()->clientScript->registerCoreScript('jquery.ui'); ?>
<?php Yii::app()->clientScript->registerScript('room',"
	$(document).ready(function() {
		// remove from list code
		$('button.remove').on('click', function() {
			var room = $(this).parent().find('input[type=hidden]');
			$.ajax({
				type: 'POST',
				url: '".CController::createUrl('list/remove')."',
				data: 'room_id='+room.val(),
				success: function(data) {
					//alert(data);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert('Error removing from list');
				}
			});
			$(this).parent().remove();
			if ($('#list li').length == 0)
				$('#list').html('<li>No rooms currently in list</li>');
		});
		$('#list').sortable({
			axis: 'y',
			cursor: 'move',
			disabled: false,
			items: 'li',
			opacity: '0.6',
			update: function(event, ui) {
				var roomOrder = '';
				$('input[type=hidden]').each(function() {
					roomOrder += 'room_id[]='+$(this).val()+'&';
				});
				roomOrder = roomOrder.substring(0, roomOrder.length-1);
				$.ajax({
					type: 'POST',
					url: '".CController::createUrl('list/update')."',
					data: roomOrder,
					success: function(data) {
						//alert(data);
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert('Error saving to list: '+errorThrown);
						//alert(jqXHR.responseText);
					}
				});
			}
		});
	});
	
	$('button.clear').on('click', function() {
		$.ajax({
			type: 'POST',
			url: '".CController::createUrl('list/clear')."',
			success: function(data) {
				$('#list').html('<li>No rooms currently in list</li>');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error saving to list: '+errorThrown);
				//alert(jqXHR.responseText);
			}
		});
	});
	
	$('a#email').on('click', function() {
		var subject = 'My favorite rooms';
		var body = '';
		$('#list li').each(function() {
			$(this).find('div').each(function() {
				body += $(this).text()
				if ($(this).has('a').length)
					body += ' - http://'+document.location.hostname+$(this).find('a').attr('href');
				body += '%0D';
			});
			body += '%0D';
		});
		$(this).attr('href','mailto:?subject='+subject+'&body='+body);
	});
"); ?>
<h1>Room List</h1><?php echo CHtml::htmlButton('Clear List',array('class'=>'clear')); ?>

<?php if (count($rooms) == 0): ?>
	<div class="error">No rooms currently in list</div>
<?php else: ?>
	<ul id="list">
		<?php foreach($rooms as $room): ?>
			<li>
				<?php echo CHtml::hiddenField('room_'.$room->id, $room->id); ?>
				<div>Room: <?php echo CHtml::link($room->number, array('room/view','id'=>$room->id)); ?></div>
				<div>Building: <?php echo $room->floor->building->name; ?></div>
				<div>Floor: <?php echo $room->floor->level; ?></div>
				<?php echo CHtml::htmlButton('Remove', array('class'=>'remove')); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php echo CHtml::link('Email this list', '#', array('id'=>'email')); ?>
<?php endif; ?>
<div class="warning">Warning: This list is only temporary and will be deleted when you leave the site.</div>
