<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/search.js', CClientScript::POS_HEAD); ?>

<?php echo CHtml::form(array('search/search'),'GET', array('id'=>'advanced_search')); ?>
	<div class="adv-search">
		<?php foreach ($advancedSearchOptions as $category => $features): ?>
			<div class="category">
				<div class="title"><?php echo $category; ?></div>
				<?php foreach ($features as $featureName => $feature): ?>
					<div class="feature">
						<div class="title"><span>+</span><?php echo $featureName; ?></div>
						<div class="detail">
							<?php if ($feature['searchType'] == SearchController::NUMERIC_SEARCH): ?>
								<?php echo CHtml::textField($feature['feature_id'].'_low', '',array('size'=>2,'maxlength'=>3)).' - '.CHtml::textField($feature['feature_id'].'_high', '', array('size'=>2,'maxlength'=>3)); ?>
							<?php elseif ($feature['searchType'] == SearchController::CHECKBOX_SEARCH): ?>
								<?php echo CHtml::checkBoxList($feature['feature_id'], array(), $feature['details']); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
		<div class="category">
			<?php echo CHtml::htmlButton('Advanced Search', array('name'=>'type','value'=>'advanced','type'=>'submit')); ?>
		</div>
	</div>
</form>