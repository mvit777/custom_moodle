<div id="doc-container">
	<?php echo $THIS_->get_error() ?>
	<div class="doc-topbar">
	<?php if($THIS_->mode=='edit' || $THIS_->mode == 'save'): ?>
		<span class="pull-left"><a href="./index.php?mode=read&format=html">view</a></span>	
	<?php else: ?>
		<a href="./index.php?mode=edit&format=markdown">edit</a>	
	<?php endif; ?>
	</div>
	<?php echo $data ?>
</div>