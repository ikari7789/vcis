<?php
$this->pageTitle='User Administration | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Users'=>array('index'),
	'Manage Users',
);

$this->menu=array(
	//array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create'), 'visible'=>Yii::app()->user->checkAccess('createUser', Yii::app()->user->id)),
	array('label'=>'Manage Buildings', 'url'=>array('building/admin'), 'visible'=>Yii::app()->user->checkAccess('manageBuilding', Yii::app()->user->id)),
	array('label'=>'Manage Rooms', 'url'=>array('room/admin'), 'visible'=>Yii::app()->user->checkAccess('manageRoom', Yii::app()->user->id)),
	array('label'=>'Manage Feature Categories', 'url'=>array('category/admin'), 'visible'=>Yii::app()->user->checkAccess('manageCategory', Yii::app()->user->id)),
	array('label'=>'Manage Features', 'url'=>array('feature/admin'), 'visible'=>Yii::app()->user->checkAccess('manageFeature', Yii::app()->user->id)),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="content-header">
	<h1>Manage Users</h1>
	
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
		
	$userType = 'updateOwnUser';
	if (Yii::app()->user->checkAccess('administrator'))
		$userType = 'updateUser';

	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'user-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			'username',
			'email',
			'last_login_time',
			/*
			'create_time',
			'update_time',
			'create_user_id',
			'update_user_id',
			*/
			array(
				'class'=>'CButtonColumn',
				//'template'=>'{view}{update}{delete}', //$template,
				'buttons'=>array(
					'view'=>array(
						'visible'=>'true',
					),
					'update'=>array(
						'visible'=>'Yii::app()->user->checkAccess("'.$userType.'", array("id"=>$data->id))',
					),
					'delete'=>array(
						'visible'=>'Yii::app()->user->checkAccess("deleteOtherUser", array("id"=>$data->id))',
					),
				),
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
