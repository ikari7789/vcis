<?php
$this->pageTitle='Search Results | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Search'=>'search',
	'Search Results',
); 

$this->widget('application.extensions.fancybox.EFancyBox', array(
    'target'=>'a.fancy',
    'config'=>array(),
    )
);
?>

<h2 class="search">Search Results</h2>

<?php $this->renderPartial('_search',array('advancedSearchOptions'=>$advancedSearchOptions)); ?>
<?php if ($results): ?>
	<?php $this->widget('zii.widgets.CListView', array(
		'ajaxUpdate'=>false,
		'dataProvider'=>$results,
		'id'=>'searchResults',
		'itemView'=>'_view',
		'itemsCssClass'=>'search-results',
	)); ?>
<?php else: ?>
	<p class="error">No results matched your search terms.</p>
<?php endif; ?>
