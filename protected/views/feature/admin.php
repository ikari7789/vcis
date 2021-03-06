<?php
$this->pageTitle = 'Feature Administration | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Features'=>array('index'),
	'Manage Features',
);

$this->menu=array(
	//array('label'=>'List Feature', 'url'=>array('index')),
	array('label'=>'Create Feature', 'url'=>array('create'), 'visible'=>Yii::app()->user->checkAccess('createFeature', Yii::app()->user->id)),
	array('label'=>'Manage Buildings', 'url'=>array('building/admin'), 'visible'=>Yii::app()->user->checkAccess('manageBuilding', Yii::app()->user->id)),
	array('label'=>'Manage Rooms', 'url'=>array('room/admin'), 'visible'=>Yii::app()->user->checkAccess('manageRoom', Yii::app()->user->id)),
	array('label'=>'Manage Feature Categories', 'url'=>array('category/admin'), 'visible'=>Yii::app()->user->checkAccess('manageCategory', Yii::app()->user->id)),
	array('label'=>'Manage Users', 'url'=>array('user/admin'), 'visible'=>Yii::app()->user->checkAccess('manageUser', Yii::app()->user->id)),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('feature-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="content-header">
	<h1>Manage Features</h1>
	
	<p>
	You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
	or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
	</p>
	
	<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
	<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search',array(
		'model'=>$model,
	)); ?>
	</div><!-- search-form -->
</div>
<div class="left-column admin">
	<?php 
	
	$template = '';
		
	if (Yii::app()->user->checkAccess('updateFeature', Yii::app()->user->id))
		$template .= '{update}';
	
	if (Yii::app()->user->checkAccess('deleteFeature', Yii::app()->user->id))
		$template .= '{delete}';
	
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'feature-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			//'id',
			array(
				'name'=>'category_id',
				'value'=>'$data->category->name',
			),
			'name',
			'description',
			array(
				'class'=>'CButtonColumn',
				'template'=>$template,
			),
		),
	)); ?>
</div>
<div class="right-column admin">
	<div id="sidebar">
	<?php
		$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>'Operations',
		));
		$this->widget('zii.widgets.CMenu', array(
			'items'=>$this->menu,
			'htmlOptions'=>array('class'=>'operations'),
		));
		$this->endWidget();
	?>
	</div><!-- sidebar -->
</div>
