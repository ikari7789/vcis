<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'number'); ?>
		<?php echo $form->textField($model,'number',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'front_image'); ?>
		<?php echo $form->textField($model,'front_image',array('size'=>60,'maxlength'=>256)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'back_image'); ?>
		<?php echo $form->textField($model,'back_image',array('size'=>60,'maxlength'=>256)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'map_image'); ?>
		<?php echo $form->textField($model,'map_image',array('size'=>60,'maxlength'=>256)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'floor_id'); ?>
		<?php echo $form->textField($model,'floor_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_time'); ?>
		<?php echo $form->textField($model,'create_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'update_time'); ?>
		<?php echo $form->textField($model,'update_time'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->