<?php
require $CFG->dirroot.'/local/customclasses/documentation/documentation.php';

class block_custom_courselist_docs extends Documentation{
	protected function get_root_path(){
		return dirname(__DIR__);
	}
}

