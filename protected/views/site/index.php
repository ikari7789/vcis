<?php $this->pageTitle=Yii::app()->name; ?>

<?php Yii::app()->clientScript->registerScript('imageHover',"
	$(window).bind('load', function(){
		".$imageJs."
	});
	
	$(document).ready(function() {
		// hover code
		$('a.building').hover(function() {
			$('#mapImage').removeClass('display');
			$('#'+$(this).attr('id')+'_map').addClass('display');
		},function() {
			$('#'+$(this).attr('id')+'_map').removeClass('display');
			$('#mapImage').addClass('display');
		});
	});
"); ?>

<div class="content-header">
	<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

	<?php if(Yii::app()->user->hasFlash('lastLoginFlash')): ?>
		<p>
			You last logged in on <?php echo date('l, F d, Y, g:i a', Yii::app()->user->lastLoginTime); ?>.
		</p>
	<?php endif; ?>
</div>
<div class="left-column">
	<h2>Buildings</h2>
	<ul class="buildings">
		<?php foreach ($buildings as $building): ?>
			<li>
				<?php echo CHtml::link(
					$building->name,
					array(
						'building/view',
						'id'=>$building->id
					),
					array(
						'id'=>'building_'.$building->id,
						'class'=>'building',
					)
				); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<div class="right-column">
	<div class="map-images">
		<?php echo CHtml::image(
			Yii::app()->request->baseUrl.'/images/campus-map.jpg',
			'University of Wisconsin - Whitewater Campus Map',
			array(
				'id'=>'mapImage',
				'class'=>'map-image display',
			)); ?>
	</div>
</div>
