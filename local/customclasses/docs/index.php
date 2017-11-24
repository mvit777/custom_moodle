<?php
require(__DIR__.'../../../../config.php');//includes local/customclasses/autoloader.php
require_once($CFG->libdir.'/adminlib.php');
require $CFG->dirroot.'/local/customclasses/docs/documentation.php';

require_login();

$content = optional_param('content','',PARAM_RAW);
$format = optional_param('format','html', PARAM_ALPHA);
$mode = 'read';
$context = context_system::instance();
require_capability('moodle/site:manageblocks', $context);

$docs = new local_docs('README');

$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$title = "Documentation";
$PAGE->set_title($title);
$rootdir = explode('/', $docs->root_path);
$url = "$CFG->wwwroot/blocks/".$rootdir[count($rootdir)-1]."/docs/index.php";
$PAGE->set_url($url);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

if($_SERVER['REQUEST_METHOD']=='POST'):
	echo $docs->get_layout('save', $format='markdown', $content);
else:
	$mode = $format == 'html' ? 'read' : 'edit';
	echo $docs->get_layout($mode, $format);
endif;


echo $OUTPUT->footer();