# Custom Courselist #
## Reason for this block ##
This block provides an alternative listing of available courses and my courses, much like the **blocks/course_list** block does.

This block is a proof of concept for a larger project (namely the **local/customclasses** plugin) which attempts to solve a larger problem when in need of customise moodle core classes and components.

This larger project is an attempt to remove moodle upgrades nightmare when some core files have been customised by not customising them at all, except for config.php which is anyway not a core file (moodle in fact comes bundled with config-dist.php).

The idea behind is to build a global autoload method for custom classes to be put in the local/customclasses folder and thus make possible to override all moodle core classes reachable by the global class *$PAGE*.

In turn this class, which is avalilable early in the pagecycle, (just after lib.php inclusion in config.php) gets overidden in
**config.php**
```
require_once(__DIR__ . '/lib/setup.php'); // Do not edit

//here default moodle $PAGE gets overriden
require_once(__DIR__.'/local/customclasses/autoloader.php');
$PAGE = CustomLoader::run('custom_page','core','page');
```

The expected behavior is the chance to customise any of those classes without touching any of moodle files. 

Read more docs at [/local/customclasses/docs/](/local/customclasses/docs/)

Finally this block features a simplistic markup editor inherited from  customclasses plugin.

## my requirements ##
- Available courses list and my courses list must be displayed in the center div called 'content' on both pages Dahsboard and FrontPage
- Every course block must show teacher(s)' picture(s)
- I want to override the absolute minimum from moodle core_renderer
- It must be possible to switch from one listing to another by tabs (as in a previous customised moodle version)
- They must feature additional informations from an external database or non standard moodle tables
- I want to be able to inject those additonal informations in every course block in the listing
- I don't want to alter any moodle files to ease the process of version upgrade
- I want to customise the behavior of some moodle core classes and have this customisations spread and be accessible all over the site.
- I want the customisation not to be theme dependent
- I want these customisation to be independent from the presence of this block
- I want a superquick way to switch on/off customclasses

## why not use.... ##
Core block **course_list** cannot be placed in the *content* div of FrontPage as it settings do not allow any block to be put in the *content* div.

...So even creating a custom block that inheriths from it does not solve the problem.

Couses list in FrontPage drags its data from **core_course_renderer** in  *course/renderer.php* (so does blocks/course_list).  
That is the class I need to customise.  
Namely the course

In that class I can put my own customisation and have them spread all over the site and all over any theme.
I dont want to alter that file but rather autoload a derived class that overrides it.

## Conclusion ##
I'am sure there is more then one way to achieve a very similar visual result by tampering with moodle settings.
But as I said I need to fulfil my requirements exactly and also this block is just a proof-of-concept implementation of a larger project that cannot be done through moodle settings (not that I'm aware) or code architecture (not that I'm aware).

In case I'm wrong let me know that I can happily abandon this project :)

## Dependencies ##
- requires moodle version >=3.3
- requires plugin local/customclasses (download link)
- installation folder *dirroot/blocks/*

## Gotchas & Todo ##
Fix installation problem not surviving self.test()

Block skeleton was generated from admin/tool plugin so there is really no clear reason why the self.test fails.
One possible reason could be that the tests folder is missing ;)

No time to investigate it now.

## License ##

2017 Free <free@example.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
