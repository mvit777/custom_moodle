<?php
require $CFG->dirroot.'/local/customclasses/documentation/documentation.php';

class local_docs extends Documentation{
	protected function get_root_path(){
		return dirname(__DIR__);
	}
}

