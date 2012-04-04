<?php
$this->breadcrumbs=array(
	'Floors',
);

$this->menu=array(
	array('label'=>'Create Floor', 'url'=>array('create')),
	array('label'=>'Manage Floor', 'url'=>array('admin')),
);
?>

<h1>Floors</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
