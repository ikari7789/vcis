<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'building-form',
	'enableAjaxValidation'=>true,
	'htmlOptions' => array(
		'enctype' => 'multipart/form-data',
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
	<?php //echo $form->errorSummary($floors); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'map_image'); ?>
		<?php
			$baseDir = Yii::getPathOfAlias('siteDir');
			$uploadDir = $baseDir.'/images/buildings/';
			if ($model->map_image != '' && file_exists($uploadDir.$model->map_image))
				echo CHtml::image(Yii::app()->request->baseUrl.'/images/buildings/'.$model->map_image);
		?>
		<?php echo $form->fileField($model,'map_image',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'map_image'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'street_image'); ?>
		<?php
			$baseDir = Yii::getPathOfAlias('siteDir');
			$uploadDir = $baseDir.'/images/buildings/';
			if ($model->street_image != '' && file_exists($uploadDir.$model->street_image))
				echo CHtml::image(Yii::app()->request->baseUrl.'/images/buildings/'.$model->street_image);
		?>
		<?php echo $form->fileField($model,'street_image',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'street_image'); ?>
	</div>
    
	<div class="row">
		<?php echo CHtml::label('Number of Floors','floorNum'); ?>
		<?php $maxFloor = 1;
			if (count($model->floors) > 0) {
				foreach ($model->floors as $floor) {
					$floors[$floor->level] = $floor;
					$maxFloor = $floor->level;
				}
			}?>
        <?php echo CHtml::dropDownList('floorNum', $maxFloor, array(
			 1 =>  1,
			 2 =>  2,
			 3 =>  3,
			 4 =>  4,
			 5 =>  5,
			 6 =>  6,
			 7 =>  7,
			 8 =>  8,
			 9 =>  9,
			10 => 10,
		)); ?>
    </div>
    
    <div id="floors">
    	<?php
		$baseDir = Yii::getPathOfAlias('siteDir');
		$uploadDir = $baseDir.'/images/floors/';
		
		for ($floor = 1; $floor <= $maxFloor; $floor++) {
		?>
		<div class="row">
			<?php 
			if (isset($floors[$floor])) {?>
				<label for="Floor[<?php echo $floors[$floor]->level; ?>][map_image]">Floor <?php echo $floors[$floor]->level; ?> image</label> <?php
				if (!empty($floors[$floor]->map_image) && file_exists($uploadDir.$floors[$floor]->map_image))
					echo CHtml::image(Yii::app()->request->baseUrl.'/images/floors/'.$floors[$floor]->map_image, 
						$model->name.' - Floor '.$floors[$floor]->level)."\n"; 
				?>
				<input id="Floor_<?php echo $floors[$floor]->level; ?>_map_image" type="file" size="60" maxlength="255" value="" name="Floor[<?php echo $floors[$floor]->level; ?>][map_image]">
			<?php } else { ?>
				<label for="Floor[<?php echo $floor; ?>][map_image]">Floor <?php echo $floor; ?> image</label>
				<input id="Floor_<?php echo $floor; ?>_map_image" type="file" size="60" maxlength="255" value="" name="Floor[<?php echo $floor; ?>][map_image]">
			<?php } ?>
		</div>
		<?php } ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->