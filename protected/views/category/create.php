<?php
$this->pageTitle = 'Create a Category | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Categories'=>array('index'),
	'Manage Feature Categories'=>array('category/admin'),
	'Create Feature Category',
);

$this->menu=array(
	//array('label'=>'List Category', 'url'=>array('index')),
	array('label'=>'Manage Category', 'url'=>array('admin')),
);
?>

<h1>Create Category</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>