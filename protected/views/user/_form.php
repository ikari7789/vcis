<div class="form">
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/form.js', CClientScript::POS_HEAD); ?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php if (!$model->isNewRecord): ?>
			<?php echo $model->username; ?>
			<?php echo $form->hiddenField($model,'username'); ?>
		<?php else: ?>
			<?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>256)); ?>
		<?php endif; ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
	
	<?php if (!$model->isNewRecord): ?>
		<div class="row">
			<?php $model->currentPassword = ''; ?>
			<?php echo $form->labelEx($model,'currentPassword'); ?>
			<?php echo $form->passwordField($model,'currentPassword', array('size'=>60, 'maxlength'=>256)); ?>
			<?php echo $form->error($model,'currentPassword'); ?>
		</div>
		<?php $model->password = ''; ?>
	<?php endif; ?>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password_repeat'); ?>
		<?php echo $form->passwordField($model,'password_repeat',array('size'=>60,'max_length'=>256)); ?>
		<?php echo $form->error($model,'password_repeat'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'role'); ?>
		<?php echo $form->dropDownList($model,'role',$model->getUserRoles()); ?>
		<?php echo $form->error($model,'role'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->