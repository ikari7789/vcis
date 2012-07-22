<?php
$this->pageTitle='Search | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Search'
);
?>
<h2>Search</h2>
<?php $this->renderPartial('_search',array('advancedSearchOptions'=>$advancedSearchOptions)); ?>
<div id="searchResults" class="list-view">
	<p>Expand options to the left by clicking the "+" symbol to see search options.</p>
	<p>Search by clicking on the button labeled, "Advanced Search."</p>
</div>