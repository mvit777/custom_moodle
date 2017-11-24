<div class="alert alert-error">
	<h4>Please fix following error(s):</h4>
	<ul>
	<?php foreach($data->errors as $err): ?>
		<li><?php echo $err ?></li>
	<?php endforeach; ?>
	</ul>
</div>
