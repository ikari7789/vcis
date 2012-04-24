<?php $this->beginContent('//layouts/main'); ?>
<div id="content">
	<div class="left-column <?php echo $this->class; ?>">
		<?php echo $content; ?>
	</div>
	<div class="right-column <?php echo $this->class; ?>">
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
</div><!-- content -->
<?php $this->endContent(); ?>