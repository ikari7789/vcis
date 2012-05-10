<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'room-form',
	'enableAjaxValidation'=>true,
	'htmlOptions' => array(
		'enctype' => 'multipart/form-data',
	),
)); 

$this->widget('application.extensions.fancybox.EFancyBox', array(
    'target'=>'a.fancy',
    'config'=>array(),
    )
);

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/form.js', CClientScript::POS_HEAD);

$rootPath = pathinfo(Yii::app()->request->scriptFile);
$baseDir = $rootPath['dirname'];
$uploadDir = $baseDir.'/images/rooms/';
$imageUrl = Yii::app()->request->baseUrl.'/images/rooms/';
?>

	<p class="note">
		Fields with <span class="required">*</span> are required.<br />
		Click on images to increase size.<br />
		There is a max filesize limit of 2MB on images.<br />
		Newly added image might not show immediately after upload. Attempt a hard-refresh of the page to clear the cache by pressing Ctrl+F5 on Windows.
	</p>
	
	<?php echo $form->errorSummary($model); ?>

	<div class="row" id="building_row">
		<?php echo CHtml::label('Building','building_id'); ?>
		<?php if (!$model->isNewRecord): ?>
			<?php echo $model->building->name; ?>
		<?php else: ?>
			<?php
				echo CHtml::dropDownList(
					'building_id',
					isset($model->floor->building->id) ? $model->floor->building->id : '', $buildings,
					array(
						'empty'=>'--please select--',
						'ajax' => array(
							'type'=>'POST', // request type
							'url'=>CController::createUrl('building/ajaxFloors'), // url to call
							//Style: CController::createUrl('currentcontroller/methodToCall')
							'update'=>'#Product_subcategory_id', // selector to update
							'success'=>'js:function(data) {
								if (data != "")
								{
									jQuery("#floor_row").show();
									jQuery("#Room_floor_id")
										.removeAttr("disabled")
										.find("option").each(function() {
											$(this).remove();
										}).end()
										.append("<option value=\"whatever\">--please select--</option>")
										.append(data);
										//alert(data);
								}
								else
								{
									jQuery("#floor_row").hide();
									jQuery("#Room_floor_id")
										.attr("disabled", "disabled")
										.find("option").each(function() {
											$(this).remove();
										}).end()
										.append("<option value=\"whatever\">--please select--</option>");
								}
							}',
						)
					)
				);
			?>
		<?php endif; ?>
	</div>

	<div class="row" id="floor_row">
		<?php echo $form->labelEx($model,'floor_id'); ?>
		<?php if (!$model->isNewRecord): ?>
			<?php if ($model->floor->level == '0'): ?>
				Basement
			<?php else: ?>
				<?php echo $model->floor->level; ?>
			<?php endif; ?>
		<?php else: ?>
			<?php echo $form->dropDownList($model, 'floor_id', $floors, 
				array('disabled'=>(count($floors) > 0 ? '' : 'disabled'),'empty'=>'--please select--',)); ?>
			<?php echo $form->error($model,'floor_id'); ?>
		<?php endif; ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'number'); ?>
		<?php echo $form->textField($model,'number',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'front_image'); ?>
		<?php
			if (file_exists($uploadDir.$model->front_image) && !empty($model->front_image))
				echo CHtml::link(
					CHtml::image(
						$imageUrl.$model->front_image,
						'Front Image'
					),
					$imageUrl.substr($model->front_image,0,-4).'_large'.substr($model->front_image,-4),
					array(
						'class'=>'fancy'
					)
				);
		?>
		<?php echo $form->fileField($model,'front_image',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'front_image'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'back_image'); ?>
		<?php
			if (file_exists($uploadDir.$model->back_image) && !empty($model->back_image))
				echo CHtml::link(
					CHtml::image(
						$imageUrl.$model->back_image,
						'Back Image'
					),
					$imageUrl.substr($model->back_image,0,-4).'_large'.substr($model->back_image,-4),
					array(
						'class'=>'fancy'
					)
				);
		?>
		<?php echo $form->fileField($model,'back_image',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'back_image'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'map_image'); ?>
		<?php
			if (file_exists($uploadDir.$model->map_image) && !empty($model->map_image))
				echo CHtml::link(
					CHtml::image(
						$imageUrl.$model->map_image,
						'Map Image'
					),
					$imageUrl.$model->map_image,
					array(
						'class'=>'fancy'
					)
				);
		?>
		<?php echo $form->fileField($model,'map_image',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'map_image'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->checkbox($model,'status', array('checked'=>'checked')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
	
	<h2>Room Features</h2>
	<?php foreach($categories as $category) { ?>
		<?php if (count($category->features) > 0) { ?>
			<h3><?php echo $category->name; ?></h3>
			<?php foreach($category->features as $feature) { ?>
				<div class="row">
					<?php if (isset($roomFeatures[$feature->id])): ?>
						<?php $details = $roomFeatures[$feature->id]['details']; ?>
						<?php if ($roomFeatures[$feature->id]['verification_time'] != '0000-00-00 00:00:00'): ?>
							<?php $verification_time = 'Last verified: '.$roomFeatures[$feature->id]['verification_time']; ?>
						<?php else: ?>
							<?php $verification_time = 'Unverified'; ?>
						<?php endif; ?>
					<?php else: ?>
						<?php $details = ''; ?>
						<?php $verification_time = 'Unverified'; ?>
					<?php endif; ?>
					<?php echo CHtml::label($feature->name, 'RoomFeature_'.$feature->id.'_name'); ?>
					<?php echo CHtml::textField('RoomFeature['.$feature->id.'][details]', $details,array('size'=>20,'maxlength'=>45)); ?>
					<?php echo CHtml::label('Update verification?', 'RoomFeature_'.$feature->id.'_verified', array('class'=>'verification')); ?>
					<?php echo CHtml::checkbox('RoomFeature['.$feature->id.'][verified]', false); ?>
					<span class="feature info"><?php echo $verification_time; ?></span>
				</div>
			<?php } ?>
		<?php } ?>
	<?php }	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->