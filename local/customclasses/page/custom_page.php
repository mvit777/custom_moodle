<?php

class custom_page extends moodle_page{
	use local_customclasses;
	/**
     * Returns instance of page renderer
     *
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @param string $target one of rendering target constants
     * @return custom_render if exists or standard moodle xxx_render invoked
     */
    public function get_renderer($component, $subtype = null, $target = null) {
    	//echo $component.'-'.$subtype; echo " in line 13 custom_page<br />";
        if ($this->pagelayout === 'maintenance') {
            // If the page is using the maintenance layout then we're going to force target to maintenance.
            // This leads to a special core renderer that is designed to block access to API's that are likely unavailable for this
            // page layout.
            $target = RENDERER_TARGET_MAINTENANCE;
        }
		/*if (is_null($this->_theme)):
			//echo "sono qui";die();
			$this->initialise_theme_and_output();
		else:
			//var_dump($this->_theme);die();
		endif;*/
		$renderer = CustomLoader::get_renderer('renderer', $component, $subtype, $target);
		
		if(is_object($renderer)):
			return $renderer;
		else:
			return parent::magic_get_theme()->get_renderer($this, $component, $subtype, $target);
		endif;
    }
	
	/*
	 * @param string $template -- a file path to some templates folder
	 * 
	 * return string $content -- a bunch of html
	 */
	public function load_template($template, $data){
		$THIS_ = $this;
		//inherited from traits in customclasses/lib.php
		return $this->_load_template($template, $THIS_, $data);
	}
}
