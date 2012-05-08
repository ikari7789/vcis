<?php
$this->pageTitle='Update User: '.$model->username.' | '.Yii::app()->name;

if (Yii::app()->user->checkAccess('manageUser'))
	$this->breadcrumbs=array(
		'Administrative Tools'=>array('admin/index'),
		'Manage Users'=>array('user/admin'),
		'Update User: '.$model->username,
	);
else
	$this->breadcrumbs=array(
		'Administrative Tools'=>array('admin/index'),
		'Update User: '.$model->username,
	);

$this->menu=array(
	//array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	//array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage User', 'url'=>array('admin')),
);
?>

<h1>Update User: <?php echo $model->username; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>