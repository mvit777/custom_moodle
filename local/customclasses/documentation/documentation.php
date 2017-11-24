<?php

class Documentation extends base_local_customclass{
	//use local_customclasses moved in base class;
	
	public function __construct($file){
		//localise $CFG, $DB, $PAGE variables;
		parent::__construct();
		
		$this->environment = \League\CommonMark\Environment::createCommonMarkEnvironment();
        $this->environment->addExtension(new \Webuni\CommonMark\TableExtension\TableExtension());
		$this->converter = new \League\CommonMark\Converter(
										new \League\CommonMark\DocParser($this->environment), 
										new \League\CommonMark\HtmlRenderer($this->environment));
	    $this->root_path = $this->get_root_path();
		 
		$this->_init($file);
	}
	
	protected function get_root_path(){
		return dirname(__DIR__);
	}
	
	protected function _init($file){
		$this->file = $this->root_path."/$file.md";
		
		$errors = $this->validate_file($this->file,'w');
		if(count($errors)>0){
			$this->set_error($errors);
		}
		$this->templates_path = $this->root_path .'/templates/';
		$this->init_editor();
		$this->content = file_get_contents($this->file);
	}
	
	/*
	 * TODO: dead code for the moment, cannot find a suitable markdown js editor
	 * tried: bootstrap-markdown -- buttons show without icons, preview mode does not render markdown -> html
	 * 		  simplemde -- cannot load properly
	 */
	protected function init_editor(){
		/*global $PAGE;*/
		//$this->page->requires->css('/local/customclasses/css/simplemde.css');
		//$this->page->requires->js('/local/customclasses/js/hello.js');
		//$this->page->requires->js('/local/customclasses/js/markdown-editor/js/simplemde.js', true);
		//requires->js_call_amd('frankenstyle_path/your_js_filename', 'init');
		//$this->page->requires->js_call_amd('local_customclasses/simplemde.min', 'init');
		
	}
	
	public function read($format='html'){  
        
		if($format!='html'):
			return $this->content;
		endif;
		
        return $this->converter->convertToHtml($this->content);
    }
	
	public function save($format='markdown', $content=''){
			
		$this->content = $content;
        //save
        $this->write_file();
		//show edit form with new content
		return $this->edit($format);
	}
	
	public function edit($format='markdown'){
		return $this->load_template('edit', $this->read($format));
	}
	
	public function get_layout($mode='read' ,$format='html', $content=''){
		$this->format = $format;
		
		if(!in_array($mode, array('read','edit','save'))):
			$mode = 'read';
			$this->$mode = $mode;
		endif;

		$this->mode = $mode;
		
		$layout = 'layout';
		$args = array($format, $content); 
		return $this->load_template($layout, $this->$mode(...$args));
	}
	
	protected function write_file(){
		//scrivi in un file temporaneo vecchio contenuto
		//poi...
		parent::_write_file($this->file, $this->content, 'w');
	}
	
	protected function load_template($template, $data){
		$filename = $this->templates_path.'doc_'.$template.'.php';
		$THIS_ = $this;
		
		$result = parent::_load_template($filename, $THIS_, $data);
		
		return $result;
	}
	
}
