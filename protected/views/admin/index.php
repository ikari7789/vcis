<?php
$this->breadcrumbs=array(
	'Administrative Tools',
);?>
<h1>Administrative Tools</h1>

<?php echo CHtml::link('Manage Buildings', array('building/admin')); ?><br />
<?php echo CHtml::link('Manage Rooms', array('room/admin')); ?><br />
<?php echo CHtml::link('Manage Feature Categories', array('category/admin')); ?><br />
<?php echo CHtml::link('Manage Features', array('feature/admin')); ?><br />