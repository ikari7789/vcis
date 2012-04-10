<?php
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
				$('#list').html('No rooms currently in list');
		});
		$('#list').sortable({
			axis: 'y',
			cursor: 'move',
			disabled: false,
			items: 'li',
			opacity: '0.6',
			update: function(event, ui) {
				alert('list changed');
			}
		});
		$('#list').on('change', function() {
			alert('list changed');
		});
	});
"); ?>
<h1>Room List</h1>

<?php if (count($rooms) == 0): ?>
	<div class="error">No rooms currently in list</div>
<?php else: ?>
	<ul id="list">
		<?php foreach($rooms as $room): ?>
			<li>
				<?php echo CHtml::hiddenField('room_'.$room->id, $room->id); ?>
				<span>Building: <?php echo $room->floor->building->name; ?></span>
				<span>Floor: <?php echo $room->floor->level; ?></span>
				<span>Room: <?php echo $room->number; ?></span>
				<?php echo CHtml::htmlButton('Remove', array('class'=>'remove')); ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
