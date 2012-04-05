<?php
$this->pageTitle=$model->name.' | '.Yii::app()->name;

$this->breadcrumbs=array(
	$model->name,
);

/*$this->menu=array(
	//array('label'=>'List Building', 'url'=>array('index')),
	array('label'=>'Create Building', 'url'=>array('create')),
	array('label'=>'Update Building', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Building', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Building', 'url'=>array('admin')),
);*/
?>

<?php Yii::app()->clientScript->registerScript('imageHover',"
	
	$(window).bind('load', function(){
		".$floorImageJs.$roomImageJs."
	});
	
	$(document).ready(function() {
		
		$('select#floors').on('change', function() {
			$('ul.rooms').html('');
			$('.map-images img').each(function() {
				if ($(this).hasClass('display'))
					$(this).removeClass('display');
			});
			$('#floor_'+$(this).val()+'_map').addClass('display');
			$.ajax({
				type: 'POST',
				url: '".CController::createUrl('floor/ajaxRooms')."',
				data: $(this).serialize(),
				success: function(data) {
					$(data).find('li').each(function() {
						$(this).appendTo('ul.rooms');
					});
					$(data).find('img').each(function() {
						if(!$('#'+this.id).length > 0) {
							$('.map-images').prepend($(this));
						}
					})
				}
			});
		});
		
		var \$currentImage;
		
		// hover code for rooms
		$('a.room').live({
			mouseenter: function() {
				$('.map-images img').each(function() {
					if ($(this).hasClass('display')) {
						\$currentImage = $(this);
						$(this).removeClass('display');
					}
				});
				$('#'+$(this).attr('id')+'_map').addClass('display');
			},
			mouseleave: function() {
				\$currentImage.addClass('display');
				$('#'+$(this).attr('id')+'_map').removeClass('display');
			}
		});
	});
"); ?>

<h1><?php echo $model->name; ?></h1>
<div class="leftcol">
	<?php echo CHtml::dropDownList('floors', '', $floors, array('id'=>'floors',)); ?>
	<ul class="rooms">
		<?php foreach ($rooms as $room): ?>
			<li><?php echo CHtml::link($room->number, array('room/view', 'id'=>$room->id), array('id'=>'room_'.$room->id,'class'=>'room')); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
<div class="rightcol">
	<div class="map-images">
	</div>
</div>