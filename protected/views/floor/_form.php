<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'floor-form',
	'enableAjaxValidation'=>true,
	'htmlOptions' => array(
		'enctype' => 'multipart/form-data',
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'level'); ?>
		<?php echo $form->textField($model,'level'); ?>
		<?php echo $form->error($model,'level'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'map_image'); ?>
		<?php echo $form->fileField($model,'map_image',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'map_image'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'building_id'); ?>
		<?php echo $form->textField($model,'building_id'); ?>
		<?php echo $form->error($model,'building_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->