<?php
//namespace customclasses;
require_once($CFG->dirroot.'/local/customclasses/vendor/autoload.php');
require_once($CFG->dirroot.'/local/customclasses/lib.php');

class CustomLoader{
	public static function run($classname, $component, $subtype, $target = null, $classargs = null){
		global $PAGE, $CFG;
		
		$include_file = $CFG->dirroot.'/local/customclasses/'.$subtype.'/'.$classname.'.php';
		if(is_file($include_file)): 
			require_once $include_file ;
			//...$classargs unpacks array $classargs in count($classargs) single parameters
			return new $classname(...$classargs);
		else:
			//throw new Exception("$include_file non trovato", 1);
			return false;
		endif;
	}
	
	public static function get_renderer($classname, $component, $subtype = null, $target = null, $classargs = null){
		global $PAGE;
		//echo "$classname in line 17 autoloader component is $component e subtype is $subtype<br />";
		if($classargs==null):
			$classargs = array($PAGE, null);
		endif;
		$renderer = CustomLoader::run($classname, $component, $subtype, $target, $classargs);
		if(is_object($renderer)):
			//var_dump($renderer);die();
			return $renderer;
		endif;
		
	}
}
