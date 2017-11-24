<?php
unset($CFG);  // Ignore this line
global $CFG;  // This is necessary here for PHPUnit execution
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';      // 'pgsql', 'mariadb', 'mysqli', 'mssql', 'sqlsrv' or 'oci'
$CFG->dblibrary = 'native';     // 'native' only at the moment
$CFG->dbhost    = 'localhost';  // eg 'localhost' or 'db.isp.com' or IP
$CFG->dbname    = 'custom_moodle';     // database name, eg moodle <--CHANGE
$CFG->dbuser    = 'root';   // your database username <--CHANGE
$CFG->dbpass    = 'root';   // your database password <--CHANGE
$CFG->prefix    = 'mdl_';       // prefix to use for all table names
$CFG->dboptions = array(
    'dbpersist' => false, 
    'dbsocket'  => false,
    'dbport'    => '',
    'dbhandlesoptions' => false,
   // 'dbcollation' => 'utf8mb4_unicode_ci', 
);

// If you need both intranet and Internet access please read
// http://docs.moodle.org/en/masquerading

$CFG->wwwroot   = 'http://www.custom_moodle.dev';//<-CHANGE


//=========================================================================
// 3. DATA FILES LOCATION
//=========================================================================
// Now you need a place where Moodle can save uploaded files.  This
// directory should be readable AND WRITEABLE by the web server user
// (usually 'nobody' or 'apache'), but it should not be accessible
// directly via the web.


$CFG->dataroot  = '/home/dmlogica/custom_moodle/moodledata';//<--CHANGE
$CFG->directorypermissions = 02777;

//$CFG->admin = 'admin';

require_once(__DIR__ . '/lib/setup.php'); // Do not edit

/***************************CUSTOM MOODLE SETTINGS*******************************************/
//check the plugin exists and customclasses are enabled;
if(is_dir($CFG->dirroot.'/local/customclasses') && isset($CFG->enable_customclasses)):
	if($CFG->enable_customclasses=="1"):
		require_once($CFG->dirroot.'/local/customclasses/autoloader.php');
		$PAGE = CustomLoader::run('custom_page','core','page');
	endif;
endif;
/***************************END CUSTOM MOODLE SETTINGS***************************************/

//comment in production
//$CFG->cachejs = false; $CFG->jsrev = -1;
// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
