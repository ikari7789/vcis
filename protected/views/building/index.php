<?php
$this->breadcrumbs=array(
	'Buildings',
);

$this->menu=array(
	array('label'=>'Create Building', 'url'=>array('create')),
	array('label'=>'Manage Building', 'url'=>array('admin')),
);
?>

<h1>Buildings</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
