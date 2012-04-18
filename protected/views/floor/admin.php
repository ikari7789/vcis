<?php
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	'Floors'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Floor', 'url'=>array('index')),
	array('label'=>'Create Floor', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('floor-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Floors</h1>

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

<?php

$template = '{view}';
	
if (Yii::app()->user->checkAccess('updateFloor', Yii::app()->user->id))
	$template .= '{update}';

if (Yii::app()->user->checkAccess('deleteFloor', Yii::app()->user->id))
	$template .= '{delete}';

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'floor-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
		array(
			'name'=>'building_id',
			'value'=>'$data->building->name',
		),
		'level',
		'map_image',
		'create_time',
		'update_time',
		array(
			'class'=>'CButtonColumn',
			'template'=>$template,
		),
	),
)); ?>
