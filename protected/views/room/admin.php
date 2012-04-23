<?php
$this->pageTitle='Room Administration | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Rooms'=>array('index'),
	'Manage Rooms',
);

$this->menu=array(
	//array('label'=>'List Room', 'url'=>array('index')),
	array('label'=>'Create Room', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('room-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Rooms</h1>

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
	
if (Yii::app()->user->checkAccess('updateRoom', Yii::app()->user->id))
	$template .= '{update}';

if (Yii::app()->user->checkAccess('deleteRoom', Yii::app()->user->id))
	$template .= '{delete}';

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'room-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
		array(
			'name'=>'building_name',
			'value'=>'$data->building->name',
		),
		array(
			'name'=>'floor_level',
			'value'=>'$data->floor->level',
		),
		'number',
		//'front_image',
		//'back_image',
		//'map_image',
		//'description',
		'create_time',
		'update_time',
		'status',
		array(
			'class'=>'CButtonColumn',
			'template'=>$template,
		)
	),
)); ?>
