<h2>Search Results for "<?php echo CHtml::encode($_GET['terms']); ?>"</h2>
<?php if ($results): ?>
	<?php foreach ($results as $result): ?>
		<p><?php echo CHtml::encode($result->building_name); ?> - <?php echo CHtml::encode($result->floor_level); ?> - <?php echo CHtml::encode($result->room_number); ?></p>
	<?php endforeach; ?>
<?php else: ?>
	<p class="error">No results matched your search terms.</p>
<?php endif; ?>

<?php print_r($results); ?>
