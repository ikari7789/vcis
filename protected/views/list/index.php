<?php
$this->pageTitle = 'Room List | '.Yii::app()->name;
$this->breadcrumbs=array(
	'List',
); 

$this->widget('application.extensions.fancybox.EFancyBox', array(
    'target'=>'a.fancy',
    'config'=>array(),
    )
);
?>
<?php Yii::app()->clientScript->registerCoreScript('jquery.ui'); ?>
<?php Yii::app()->clientScript->registerScript('room',"
	$(document).ready(function() {
		// remove from list code
		$('button.remove').on('click', function() {
			var msg = 'Are you sure you want to remove this room from the list?';
			if (confirm(msg)) {
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
			}
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
		count = 1;
		$('#list li').each(function() {
			body += count+'%0D';
			$(this).find('div').each(function() {
				body += $(this).text()
				if ($(this).has('a').length)
					body += ' - http://'+document.location.hostname+$(this).find('a').attr('href');
				body += '%0D';
			});
			body += '%0D';
			count++;
		});
		$(this).attr('href','mailto:?subject='+subject+'&body='+body);
	});
"); ?>

<div class="content-header">
	<h1>Room List</h1>
	<div class="information">Click and drag to reorder items.</div>
</div>

<ol id="list">
	<?php if (count($rooms) == 0): ?>
		<li>
			<div class="error">No rooms currently in list</div>
		</li>
	<?php else: ?>
		<?php foreach($rooms as $room): ?>
			<li class="sortable">
				<?php echo CHtml::hiddenField('room_'.$room->id, $room->id); ?>
				<?php echo CHtml::htmlButton('X', array('class'=>'remove')); ?>
				<div class="image">
					<?php echo CHtml::link(
						CHtml::image(
							Yii::app()->request->baseUrl.'/images/rooms/'.$room->front_image,
							'Room '.$room->number.' front view',
							array('class'=>'list-image')
						),
						Yii::app()->request->baseUrl.'/images/rooms/'.substr($room->front_image,0,-4).'_large'.substr($room->front_image,-4),
						array('class'=>'fancy')
					); ?>
				</div>
				<div class="room">Room: <?php echo CHtml::link($room->number, array('room/view','id'=>$room->id)); ?></div>
				<div class="building">Building: <?php echo $room->floor->building->name; ?></div>
				<div class="floor">Floor: <?php echo $room->floor->level; ?></div>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ol>
<div class="list-tools">
	<?php echo CHtml::link('Email this list', '#', array('id'=>'email')); ?>
	<?php echo CHtml::htmlButton('Clear List',array('class'=>'clear')); ?>
</div>
<span class="warning">Warning: This list is only temporary and will be deleted when you leave the site.</span>
