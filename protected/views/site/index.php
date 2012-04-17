<?php $this->pageTitle=Yii::app()->name; ?>

<?php $imageJs = ''; ?>
<?php foreach($buildingImages as $image): ?>
	<?php $imageJs.="$('".$image."').load(function(){\$('.map-images').prepend(\$(this))});\n"; ?>
<?php endforeach; ?>

<?php Yii::app()->clientScript->registerScript('imageHover',"
	
	$(window).bind('load', function(){
		".$imageJs."
	});
	
	$(document).ready(function() {
		// hover code
		$('a.building').hover(function() {
			$('#'+$(this).attr('id')+'_map').addClass('display');
		},function() {
			$('#'+$(this).attr('id')+'_map').removeClass('display');
		});
	});
"); ?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<?php if (!Yii::app()->user->isGuest): ?>
	<p>
		You last logged in on <?php echo date('l, F d, Y, g:i a', Yii::app()->user->lastLoginTime); ?>.
	</p>
<?php endif; ?>

<div class="leftcol">
	<h2>Buildings</h2>
	<ul class="buildings">
		<?php $buildingImages = array(); ?>
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
<div class="rightcol">
	<div class="map-images">
		<?php echo CHtml::image(
			Yii::app()->request->baseUrl.'/images/campus-map.png',
			'University of Wisconsin - Whitewater Campus Map',
			array(
				'id'=>'mapImage',
			)); ?>
	</div>
</div>
