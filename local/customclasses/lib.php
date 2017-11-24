<?php
//included by autoloader.php included by config.php
defined('MOODLE_INTERNAL') || die();

class base_local_customclass{
	use local_customclasses;
	
	protected $errors = array();
		
	protected function __construct(){	
		global $CFG, $DB, $PAGE;
		
		//to avoid retyping global declaration in every method
		$this->cfg = $CFG;
		$this->db = $DB;
		$this->page = $PAGE;
	}
	
	protected function set_error($errors){
		$this->errors = array_merge($this->errors, $errors);
	}
	
	protected function has_error(){
		return count($this->errors) > 0 ? true : false;
	}
	
	protected function get_error(){
		if($this->has_error()):
			$filename = $this->cfg->dirroot.'/local/customclasses/templates/error_panel.php';
			$THIS_ = $this;
			$data = new stdClass();
			$data->errors = $this->errors;
			return $this->_load_template($filename, $THIS_, $data);
		endif;
	}
	
} 

//these are available to any class that uses `use local_customclasses` without subclassing
//from `base_local_customclass`
// the local/custom_page class does so and that means these methods are already available all over the site
// if you want you can further customise these traits. See Example #5 Conflict Resolution http://php.net/manual/en/language.oop5.traits.php
trait local_customclasses {
	
	/* Loads a chunck of html and expand given variables in it
	 * @param string $filename -- fullpath to some template file
	 * @param object $THIS_ -- a reference to the class instance that originally called this method
	 * 						   This is useful in a number of situation and also good to have it injected in the template
	 * @param mixed $data -- can be a string/array/object/anything, 
	 * 						 it is your responsibility to treat it accordingly in the template.
	 * 					     This variable is template dependent so it does not get overwritten by sub-templates loaded via
	 * 						 this same method unless you deliberately want so
	 * 
	 * @return string $result -- processed template
	 */
	public function _load_template($filename, $THIS_, $data){
		global $CFG, $DB, $PAGE;
	
		ob_start();
		include $filename;
		$result = ob_get_contents();
		ob_end_clean();
		
		return $result;
	}
	
	public function _write_file($file, $content, $mode='w'){
		$handle = fopen($file, $mode);
		fputs($handle, $content);
		fclose($handle);
	}
	
	public function validate_file($file, $mode, $base_url=''){
		global $CFG;
		
		$error_log = array();
		
		if($base_url==''){
			$base_url = dirname($CFG->dirroot);//can also access moodledata
		}
		
		if(strstr($base_url, $file)){
			$str_error = "fatal error: $file is outside $base_url";
			$error_log[] = $str_error;
			throw new Exception("$file is outside $base_url", 1);
		}
		
		if(!is_file($file)){
			$error_log[] = "$file not found";
		}
		
		if(!is_writable($file) && ($mode =='w' || $mode='w+') ){
			$error_log[] = "$file must be writable for the server";
		}
		return $error_log;
	}
	
	public function box($content, $tag='span', $class='badge'){
		return html_writer::$tag($content, $class);
	}
	
	public function course_tab($label, $url){
		global $PAGE;
		$class = 'label ';
		$class .= $url == $PAGE->url ? 'label-info' : '';
		return $this->box($label, 'span', $class);
	}
	
	/*
	 * @param mixed $user  -- int or user object
	 * @param array $options -- the an array of image attributes and options
	 * 
	 * @return html string
	 */
	public function show_user_picture($user, $options = array('size'=>50)){
	    global $OUTPUT, $DB;
		
		if(is_integer($user)):
			$user = $DB->get_record('user', array('id'=>$user));
		endif;
		
		return $OUTPUT->user_picture($user, $options);
	}
}
