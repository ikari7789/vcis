<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/search.js', CClientScript::POS_HEAD); ?>

<?php echo CHtml::form(Yii::app()->request->baseUrl.'/search','GET', array('id'=>'advanced_search')); ?>
	<div class="adv-search">
		<?php //print_r($_GET); ?>
		<?php //print_r($advancedSearchOptions); ?>
		<?php echo CHtml::submitButton('Search'); ?>
		<?php echo CHtml::hiddenField('advanced_search', true); ?>
		<?php foreach ($advancedSearchOptions as $category => $features): ?>
			<div class="category">
				<div class="title"><?php echo $category; ?></div>
				<?php foreach ($features as $featureName => $feature): ?>
					<div class="feature">
						<div class="title"><span>+</span><?php echo $featureName; ?></div>
						<div class="detail">
							<?php $featureName = str_replace(array('"','(',')',' '), array('','','','_') ,$featureName); ?>
							<?php if ($feature['searchType'] == SearchController::NUMERIC_SEARCH): ?>
								<?php echo CHtml::textField($featureName.'_low', '',array('size'=>2,'maxlength'=>3)).' - '.CHtml::textField($featureName.'_high', '', array('size'=>2,'maxlength'=>3)); ?>
							<?php elseif ($feature['searchType'] == SearchController::CHECKBOX_SEARCH): ?>
								<?php echo CHtml::checkBoxList($featureName, array(), $feature['details']); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</form>
