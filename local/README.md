# Custom Classes plugin  #

(not to be used in production because I'm still developing the idea ;)

## My Problem ##
Ever wanted to customise some moodle core component and want this customisation to be theme indipendent?  
Ever wanted this customisation to survive a moodle version upgrade?  
If this sounds familiar continue reading....

As far as I know Moodle has a powerful way to override some of its core components via a [custom theme](https://docs.moodle.org/dev/Overriding_a_renderer) but there is no clear way to spread those customisations over all the themes.

This is, most probably, a well-thought architectural decision by Moodle core developers rather than a shortcoming of the application.  
Nevertheless I wish to demonstrate that it could be possible to easily add a system for overriding most of Moodle core classes without neglecting the decision mention above.

Moreover a user would have the choice to easily switch on/off this system according to his needs.  
The obvious way to do it is through a plugin. Namely a [local plugin](https://docs.moodle.org/dev/Local_plugins), one of those plugin that get installed in the local folder (which is  *mostly suitable for things that do not fit standard plugins*).

## Some background ##
Moodle's initialise 3 global variables ($CFG, $DB, $PAGE) very early in its pagecycle.  
In fact they are available just after the inclusion of the giant *lib/setup.php* file in **config.php**
```
require_once(__DIR__ . '/lib/setup.php');
```
the $PAGE variable is an instance of moodlepage class (that has a lot of functionality itself) and being present so early makes it the perfect candidate for reaching and overriding most of Moodle core classes. So adding the below lines in your config.php is safe

```
require_once(__DIR__ . '/lib/setup.php');

//check the plugin exists and customclasses are enabled;
if(is_dir($CFG->dirroot.'/local/customclasses') && isset($CFG->enable_customclasses)):
	if($CFG->enable_customclasses=="1"):
		require_once($CFG->dirroot.'/local/customclasses/autoloader.php');
		$PAGE = CustomLoader::run('custom_page','core','page');
	endif;
endif;
```
It is also worth noting that config.php is the only file that you don't overwrite when upgrading Moodle.
Indeed, during a Moodle version upgrade, turning off customclasses via it is configuration page and remove the customclasses folder **along with everything else**
is probably a good idea, as per instructions on [moodle upgrade page](https://docs.moodle.org/33/en/Upgrading)

## Introducing *local/customclasses* plugin ##
After installing customclasses plugin the first time, nothing still happens because you have to enable its use as per lines in config.php
```
if(is_dir($CFG->dirroot.'/local/customclasses') && isset($CFG->enable_customclasses)):
 ...rest of the code
```
You do it on the **Category: Administration / Plugins / Local plugins** (/admin/category.php?category=localplugins),  
ticking the **enable custom classes** checkbox.

From this point on, the standard $PAGE instance gets overriden by the already mentioned code in config.php
```
       if($CFG->enable_customclasses=="1"):
		require_once($CFG->dirroot.'/local/customclasses/autoloader.php');
		$PAGE = CustomLoader::run('custom_page','core','page');
	endif;
```
The */local/customclasses/autoloader.php* file include two other files (more on this later on)
```
require_once($CFG->dirroot.'/local/customclasses/vendor/autoload.php');
require_once($CFG->dirroot.'/local/customclasses/lib.php');
``` 
and more importantly contains the class CustomLoader which roughly imitates the Moodle's [franken_style](https://docs.moodle.org/dev/Frankenstyle) mechanism of loading classes.
The main difference, from a user point of view, is that its static method **run** requires 3 parameters ($classname, $component, $subtype) rather than 2 to allow naming a class as you want and remain compatible with Moodle's mechanism for defaulting to original class (when it is possible, more on this later on). 
```
class CustomLoader{
	public static function run($classname, $component, $subtype, $target = null, $classargs = null){
		global $PAGE, $CFG;
		
		$include_file = $CFG->dirroot.'/local/customclasses/'.$subtype.'/'.$classname.'.php';
		if(is_file($include_file)): 
			require_once $include_file ;
			//...$classargs unpacks array $classargs in count($classargs) single parameters
			return new $classname(...$classargs);
		else:
			return false;
		endif;
	}
```
what it does is search for file $classname in folder local/customclasses/$subtype and instantiates a class whose name is again $classname
```
$include_file = $CFG->dirroot.'/local/customclasses/'.$subtype.'/'.$classname.'.php';
...
 return new $classname(...$classargs);
```
If you look in the customclasses folder tree you will see that it also imitates Moodle directories structure.  
Undoubtely implementing namespaces and a proper autoload mechanism *ala* [composer](https://getcomposer.org/) would be a great idea, but I'm not sure it would be compatible out-of-the-box with Moodle as a lot of classes are not namespaced.

Back on track, what we get now is a brand new $PAGE instance created from derived class custom_page in *local/customclasses/* folder.
In this class we can override a lot of Moodle functionality

```
class custom_page extends moodle_page{
...
```
we can for instance add traits that would be accessible to any moodle class....
```
class custom_page extends moodle_page{ 
   use local_customclasses;//included by local/customclasses/lib.php
```
override its methods....
```
public function get_renderer($component, $subtype = null, $target = null) {  
        if ($this->pagelayout === 'maintenance') {
            $target = RENDERER_TARGET_MAINTENANCE;
        }
		
		$renderer = CustomLoader::get_renderer('renderer', $component, $subtype, $target);
		
		if(is_object($renderer)):
			return $renderer;
		else:
			return parent::magic_get_theme()->get_renderer($this, $component, $subtype, $target);
		endif;
    }

```
...compose other overriden classes (like course/renderer, for example, overridden by local/customclasses/course/renderer)

```
               $renderer = CustomLoader::get_renderer('renderer', $component, $subtype, $target);
                if(is_object($renderer)):
			return $renderer;
```
and default to Moodle standard class if something goes wrong...(well this is pretty weird but can prove useful under some circumstances)

```
               
		else:
			return parent::magic_get_theme()->get_renderer($this, $component, $subtype, $target);
		endif;

```
## Other plugin features ##
As said, before local/customclasses/autoloader.php include two other files:
```
require_once($CFG->dirroot.'/local/customclasses/vendor/autoload.php');
require_once($CFG->dirroot.'/local/customclasses/lib.php');
``` 
the first one is composer autoload for custom classes that you want to extend from 3d party libraries  
the second one contains a baseclass which uses [traits](https://en.wikipedia.org/wiki/Trait_(computer_programming)), that you can use for your custom classes that don't already have a parent class or to add those behaviors to classes derived from Moodle who already have a parent class.

Finally, the local/customclasses/docs folder contains a very basic markdown editor (with which I created this doc) that you can use in any block/plugin. See the blocks/custom_courselist/docs folder to see an implementation.

Also look at the blocks/custom_courselist/README for a general proof-of-concept of all the plugin exposed here.

## Personal opinions and conclusions ##
- A subclassing system like the one exposed (probably refined) should be included in core Moodle.  
- A lot of Moodle classes need a robust refactoring. Let's make an example:  
As a proof of concept, I wanted to add the teacher(s) picture(s) in every course box.  
I found the relevant code in *course/renderer.php* in the method **coursecat_coursebox_content**, so following my idea I just needed to create a local/customclasses/course/renderer and access it through the overriden $PAGE instance.  
All nice until I realised that the relevant lines are buried inside a method like this (which is by the way, the average length of most core classes methods), and I had to override it all (copy & paste of almost 70 lines just to add **one** line):

```
protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        global $CFG, $PAGE;
		
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';

        // display course summary
        if ($course->has_summary()) {
            $content .= html_writer::start_tag('div', array('class' => 'summary'));
            $content .= $chelper->get_course_formatted_summary($course,
                    array('overflowdiv' => true, 'noclean' => true, 'para' => false));
            $content .= html_writer::end_tag('div'); // .summary
        }

        // display course overview files
        $contentimages = $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                $contentimages .= html_writer::tag('div',
                        html_writer::empty_tag('img', array('src' => $url)),
                        array('class' => 'courseimage'));
            } else {
                $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                        html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                $contentfiles .= html_writer::tag('span',
                        html_writer::link($url, $filename),
                        array('class' => 'coursefile fp-filename-icon'));
            }
        }
        $content .= $contentimages. $contentfiles;

        // display course contacts. See course_in_list::get_course_contacts()
        if ($course->has_course_contacts()) {
            $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
            foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                $name = $coursecontact['rolename'].': '.
                        html_writer::link(new moodle_url('/user/view.php',
                                array('id' => $userid, 'course' => SITEID)),
                            $coursecontact['username']);
                     /*****************My edit to show  TEACHERS' picture :( ****/
			    $name .= $PAGE->show_user_picture($userid);
		     /*****************************************************************/		  
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul'); // .teachers
        }

        // display course category if necessary (for example in search results)
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = coursecat::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                $content .= html_writer::end_tag('div'); // .coursecat
            }
        }

        return $content;
    }
```
If you look closely this method you could see how easy it would be to refactor it into something more approachable like this:

```
protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        global $CFG, $PAGE;
		
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';

        // display course summary
        if ($course->has_summary()) {
           $content .= $course->get_summary();
        }

        // display course overview files
        $content .= $course->get_overviewfiles();

        // display course contacts. See course_in_list::get_course_contacts()
        if ($course->has_course_contacts()) {
            $content .= $course->get_contacts();
        }

        // display course category if necessary (for example in search results)
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            $content .= $course->display_category();
        }

        return $content;
    }
```
which would make adding those pictures a breeze by overriding the now very short method $course->get_contacts()

Comments, criticism, suggestions are all welcome....

## License ##

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
