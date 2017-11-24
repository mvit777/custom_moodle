<?php foreach($data as $key => $value): ?>
	<a href="<?php echo $value ?>"><?php echo $PAGE->course_tab($key, $value);  ?></a> 
<?php endforeach; ?>
