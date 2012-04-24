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

$baseDir = Yii::getPathOfAlias('siteDir');
$uploadDir = $baseDir.'/images/rooms/';
$imageUrl = Yii::app()->request->baseUrl.'/images/rooms/';
?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row" id="building_row">
		<?php echo CHtml::label('Building','building_id'); ?>
		<?php echo CHtml::dropDownList('building_id', $model->floor->building->id, $buildings, array(
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
								jQuery("#Room_floor_id").removeAttr("disabled");
								jQuery("#Room_floor_id").append(
									data
								);
							}
							else
							{
								jQuery("#floor_row").hide();
								jQuery("#Room_floor_id").attr("disabled", "disabled");
								jQuery("#Room_floor_id")
									.show()
									.find("option")
									.remove()
									.end()
									.append("<option value=\"whatever\">--please select--</option>")
									.val("");
							}
						}',
				)
			)); ?>
	</div>

	<div class="row" id="floor_row">
		<?php echo $form->labelEx($model,'floor_id'); ?>
		<?php echo $form->dropDownList($model, 'floor_id', $floors, 
			array('disabled'=>(count($floors) > 0 ? '' : 'disabled'),'empty'=>'--please select--',)); ?>
		<?php echo $form->error($model,'floor_id'); ?>
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
					Yii::app()->request->baseUrl.$imageUrl.substr($model->front_image,0,-4).'_large'.substr($model->front_image,-4),
					array(
						'class'=>'fancy'
					)
				);
		?>
		<?php echo $form->fileField($model,'front_image',array('size'=>60,'maxlength'=>256)); ?>
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
					Yii::app()->request->baseUrl.$imageUrl.substr($model->back_image,0,-4).'_large'.substr($model->back_image,-4),
					array(
						'class'=>'fancy'
					)
				);
		?>
		<?php echo $form->fileField($model,'back_image',array('size'=>60,'maxlength'=>256)); ?>
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
					Yii::app()->request->baseUrl.$imageUrl.$model->map_image,
					array(
						'class'=>'fancy'
					)
				);
		?>
		<?php echo $form->fileField($model,'map_image',array('size'=>60,'maxlength'=>256)); ?>
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
					<?php if (isset($roomFeatures[$feature->id])) {
						$details = $roomFeatures[$feature->id]['details'];
						$verified = $roomFeatures[$feature->id]['verified'];
					} else {	
						$details = '';
						$verified = false;
					}; ?>
					<?php echo CHtml::label($feature->name, 'RoomFeature_'.$feature->id.'_name'); ?>
					<?php echo CHtml::textField('RoomFeature['.$feature->id.'][details]', $details); ?>
					<?php echo CHtml::label('Verified?', 'RoomFeature_'.$feature->id.'_verified'); ?>
					<?php echo CHtml::checkbox('RoomFeature['.$feature->id.'][verified]', $verified); ?>
				</div>
			<?php } ?>
		<?php } ?>
	<?php }	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->