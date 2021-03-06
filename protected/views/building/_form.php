<div class="form">
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl."/js/building-form.js",CClientScript::POS_HEAD); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/form.js', CClientScript::POS_HEAD); ?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'building-form',
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

$rootPath = pathinfo(Yii::app()->request->scriptFile);

?>

	<p class="note">
		Fields with <span class="required">*</span> are required.<br />
		Click on images to increase size.<br />
		There is a max filesize limit of 2MB on images.<br />
		Newly added image might not show immediately after upload. Attempt a hard-refresh of the page to clear the cache by pressing Ctrl+F5 on Windows.
	</p>

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
			$baseDir = $rootPath['dirname'];
			$uploadDir = $baseDir.'/images/buildings/';
			if ($model->map_image != '' && file_exists($uploadDir.$model->map_image))
				echo CHtml::link(
					CHtml::image(
						Yii::app()->request->baseUrl.'/images/buildings/'.$model->map_image,
						'Map Image'
					),
					Yii::app()->request->baseUrl.'/images/buildings/'.$model->map_image,
					array(
						'class'=>'fancy'
					)
				);
				//echo CHtml::image(Yii::app()->request->baseUrl.'/images/buildings/'.$model->map_image);
		?>
		<?php echo $form->fileField($model,'map_image',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'map_image'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'street_image'); ?>
		<?php
			$baseDir = $rootPath['dirname'];
			$uploadDir = $baseDir.'/images/buildings/';
			if ($model->street_image != '' && file_exists($uploadDir.$model->street_image))
				echo CHtml::link(
					CHtml::image(
						Yii::app()->request->baseUrl.'/images/buildings/'.$model->street_image,
						'Map Image'
					),
					Yii::app()->request->baseUrl.'/images/buildings/'.$model->street_image,
					array(
						'class'=>'fancy'
					)
				);
				//echo CHtml::image(Yii::app()->request->baseUrl.'/images/buildings/'.$model->street_image);
		?>
		<?php echo $form->fileField($model,'street_image',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'street_image'); ?>
	</div>
	
	<?php 
		$maxFloor = 1;
		if (count($model->floors) > 0) {
			foreach ($model->floors as $floor) {
				$floors[$floor->level] = $floor;
				$maxFloor = $floor->level;
			}
		}
	?>
	
    <?php if ($model->isNewRecord): ?>
		<div class="row">
			<?php echo CHtml::label('Number of Floors','floorNum'); ?>
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
			<?php echo CHtml::htmlButton('Add a Basement',array('name'=>'basementBtn', 'id'=>'basementBtn')); ?>
	    </div>
	<?php endif; ?>
    
    <div id="floors">
    	<?php
			$baseDir = $rootPath['dirname'];
			$uploadDir = $baseDir.'/images/floors/';
		?>
		
		<?php if (isset($floors[0])): ?>
			<div class="row basement">
				<label for="Floor[<?php echo $floors[0]->level; ?>][map_image]">Basement image</label>
				<?php if (!empty($floors[0]->map_image) && file_exists($uploadDir.$floors[0]->map_image)): ?>
					<?php echo CHtml::link(
						CHtml::image(
							Yii::app()->request->baseUrl.'/images/floors/'.$floors[0]->map_image,
							$model->name.' - Floor '.$floors[0]->level
						),
						Yii::app()->request->baseUrl.'/images/floors/'.$floors[0]->map_image,
						array(
							'class'=>'fancy'
						)
					); ?>
				<?php endif; ?>
				<input id="Floor_<?php echo $floors[0]->level; ?>_map_image" type="file" size="60" maxlength="255" value="" name="Floor[0][map_image]">
			</div>		
		<?php endif; ?>
		
		<?php for ($floor = 1; $floor <= $maxFloor; $floor++): ?>
			<div class="row floor">
				<?php if (isset($floors[$floor])): ?>
					<label for="Floor[<?php echo $floors[$floor]->level; ?>][map_image]">Floor <?php echo $floors[$floor]->level; ?> image</label>
					<?php if (!empty($floors[$floor]->map_image) && file_exists($uploadDir.$floors[$floor]->map_image))
						echo CHtml::link(
							CHtml::image(
								Yii::app()->request->baseUrl.'/images/floors/'.$floors[$floor]->map_image,
								$model->name.' - Floor '.$floors[$floor]->level
							),
							Yii::app()->request->baseUrl.'/images/floors/'.$floors[$floor]->map_image,
							array(
								'class'=>'fancy'
							)
						);
						//echo CHtml::image(Yii::app()->request->baseUrl.'/images/floors/'.$floors[$floor]->map_image, 
						//	$model->name.' - Floor '.$floors[$floor]->level)."\n"; 
					?>
					<input id="Floor_<?php echo $floors[$floor]->level; ?>_map_image" type="file" size="60" maxlength="255" value="" name="Floor[<?php echo $floors[$floor]->level; ?>][map_image]">
				<?php else: ?>
					<label for="Floor[<?php echo $floor; ?>][map_image]">Floor <?php echo $floor; ?> image</label>
					<input id="Floor_<?php echo $floor; ?>_map_image" type="file" size="60" maxlength="255" value="" name="Floor[<?php echo $floor; ?>][map_image]">
				<?php endif; ?>
			</div>
		<?php endfor; ?>
    </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->