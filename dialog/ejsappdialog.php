<?php
/**
 * Created by PhpStorm.
 * @package    ejsapp
 * @subpackage atto_ejsapp
 */


//defined('MOODLE_INTERNAL') || die();
require(__DIR__ . '/../../../../../../config.php');
require_once(__DIR__ . '/ejsappdialog_form.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/filestorage/zip_packer.php');

$context = context_system::instance();
$PAGE->set_context($context);
$title = get_string('title', 'atto_ejsapp');

$PAGE->set_url('/lib/editor/atto/plugins/ejsapp/ejsappdialog.php');
$PAGE->set_title($title);
$PAGE->set_pagelayout('popup');

$mform = new atto_ejsapp_mod_form();
if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    /*echo "<pre>";
    var_dump($fromform);
    die();*/

    $maxbytes = get_max_upload_file_size($CFG->maxbytes);

    // Creating the .jar or .zip file in dataroot and updating the files table in the database
    $draftitemid_applet = $fromform->appletfile;
    /*echo "<pre>";
    var_dump($fromform->appletfile);
    die();*/
    $incremental = 5;
    if ($draftitemid_applet) {
        file_save_draft_area_files($draftitemid_applet, $context->id, 'atto_ejsapp', 'jarfiles', $incremental, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => array('application/java-archive', 'application/zip')));
    }
    //$ejsapp->id sustituido por un numero 5 a pelo, estudiar como hacer esto

    // Obtain the uploaded .zip or .jar file from moodledata using the information in the files table
    //$file_records = $DB->get_records('files', array('component'=>'user', 'filearea'=>'draft', 'itemid'=>$draftitemid_applet), 'filesize DESC');
    $file_records = $DB->get_records('files', array('contextid'=>$context->id, 'component'=>'atto_ejsapp', 'filearea'=>'jarfiles', 'itemid'=>$incremental), 'filesize DESC');
    $file_record = reset($file_records);
    $fs = get_file_storage();
    $file = $fs->get_file_by_id($file_record->id);

    // Create folders to store the .jar or .zip file
    $path = $CFG->dirroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/';
    if (!file_exists($path)) {
        mkdir($path, 0755);
    }
    $path = $CFG->dirroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . '/';
    if (!file_exists($path)) { // updating, not creating, the ejsapp activity
        mkdir($path, 0770);
    }

    // Copy the jar/zip file to its destination folder in jarfiles
    $filepath = $path . $file_record->filename;
    $file->copy_content_to($filepath);

    /*echo "<pre>";
    var_dump($file);
    die();*/

    //Inicializa los elementos
    $code = '';
    $codebase = '';
    $height = 0;
    $width = 0;
    $applet_id = substr($file_record->filename, 0, -4);
    $cache_archive = $applet_id.'.jar';
    $context_id = $context->id;
    $moodle_upload_file = $CFG->wwwroot."/mod/ejsapp/upload_file.php";

    //Se saca el codebase
    preg_match('/http:\/\/.+?\/(.+)/', $CFG->wwwroot, $match_result);
    if (!empty($match_result) and $match_result[1]) {
        $codebase .= '/' . $match_result[1];
    }
    $codebase .= '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . '/';

    if (file_exists($filepath)) {
        // Extract the manifest.mf file from the .jar
        $manifest = file_get_contents('zip://' . $filepath . '#' . 'META-INF/MANIFEST.MF');

        // class_file
        $pattern = '/Main-Class\s*:\s*(.+)\s*/';
        preg_match($pattern, $manifest, $matches, PREG_OFFSET_CAPTURE);
        $sub_str = $matches[1][0];
        if (strlen($matches[1][0]) == 59) {
            $pattern = '/^\s(.+)\s*/m';
            if (preg_match($pattern, $manifest, $matches, PREG_OFFSET_CAPTURE) > 0) {
                if (preg_match('/\s*:\s*/', $matches[1][0], $matches2, PREG_OFFSET_CAPTURE) == 0) {
                    $sub_str = $sub_str . $matches[1][0];
                }
            }
        }
        $code = $sub_str . 'Applet.class';
        $code = preg_replace('/\s+/', "", $code); // delete all white-spaces and the first newline

        // height
        $pattern = '/Applet-Height\s*:\s*(\w+)/';
        preg_match($pattern, $manifest, $matches, PREG_OFFSET_CAPTURE);
        if (count($matches) == 0) {
            $height = 0;
            // If this field does not exist in the manifest, it means the version of EJS used to compile the jar does not support Moodle.
            if ($alert) {
                $message = get_string('EJS_version', 'ejsapp');
                $alert = "<script type=\"text/javascript\">
                      window.alert(\"$message\")
                      </script>";
                echo $alert;
            }
        } else {
            $ejs_ok = true;
            $height = $matches[1][0];
            $height = preg_replace('/\s+/', "", $height); // delete all white-spaces
        }

        // width
        $pattern = '/Applet-Width\s*:\s*(\w+)/';
        preg_match($pattern, $manifest, $matches, PREG_OFFSET_CAPTURE);
        if (count($matches) == 0) {
            $width = 0;
        } else {
            $width = $matches[1][0];
            $width = preg_replace('/\s+/', "", $width); // delete all white-spaces
        }
        $width = $width;
    }



    echo $mform->generateEvent($code, $codebase, $applet_id, $width, $height, $cache_archive, $context_id, $moodle_upload_file);
} else {

    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    //Set default data (if any)
    $toform = array();
    $mform->set_data($toform);

    //displays the form
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
?>