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
//Se requiere el plugin /mod/ejsapp
require_once($CFG->libdir . '/../mod/ejsapp/locallib.php');

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

    //Obtains the folder number to store the file
    $incremental = 0;
    $codebase = '/lib/editor/atto/plugins/ejsapp/jarfiles/';
    $folderpath = $CFG->dirroot . $codebase;
    while((file_exists($folderpath.$incremental))){
        $incremental ++;
    }

    if ($draftitemid_applet) {
        file_save_draft_area_files($draftitemid_applet, $context->id, 'atto_ejsapp', 'jarfiles', $incremental, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => array('application/java-archive', 'application/zip')));
    }

    // Obtain the uploaded .zip or .jar file from moodledata using the information in the files table
    $file_records = $DB->get_records('files', array('contextid'=>$context->id, 'component'=>'atto_ejsapp', 'filearea'=>'jarfiles', 'itemid'=>$incremental), 'filesize DESC');
    $file_record = reset($file_records);
    $fs = get_file_storage();
    $file = $fs->get_file_by_id($file_record->id);

    // Create folders to store the .jar or .zip file
    if (!file_exists($folderpath)) {
        mkdir($folderpath, 0755);
    }
    $codebase = '/lib/editor/atto/plugins/ejsapp/jarfiles/'. $incremental . '/';
    $folderpath = $CFG->dirroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . '/';
    if (!file_exists($folderpath)) { // updating, not creating, the ejsapp activity
        mkdir($folderpath, 0770);
    }

    // Create folders to store the additional files like state
    if (!file_exists($folderpath."simfiles/")) {
        mkdir($folderpath."simfiles/", 0755);
    }

    // Copy the jar/zip file to its destination folder in jarfiles
    $filepath = $folderpath . $file_record->filename;
    $file->copy_content_to($filepath);

    $ext = pathinfo($file->get_filename(), PATHINFO_EXTENSION);

    //Simulation state file
    $simulation_state_file = "";
    file_save_draft_area_files($fromform->statefile, $context->id, 'atto_ejsapp', 'statefiles', $incremental, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => array('application/xml', 'application/json')));
    $file_records_state = $DB->get_records('files', array('contextid'=>$context->id, 'component'=>'atto_ejsapp', 'filearea'=>'statefiles', 'itemid'=>$incremental), 'filesize DESC');
    $file_record_state = reset($file_records_state);
    if($file_record_state) {
        $fs = get_file_storage();
        $file_state = $fs->get_file_by_id($file_record_state->id);
        $file_state->copy_content_to($folderpath . "simfiles/".$file_record_state->filename);
        $simulation_state_file = $CFG->wwwroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . "/simfiles/".$file_record_state->filename;
    }

    //controller file .cnt
    $simulation_controller_file = "";
    file_save_draft_area_files($fromform->controllerfile, $context->id, 'atto_ejsapp', 'controllerfile', $incremental, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '.cnt'));
    $file_records_controller = $DB->get_records('files', array('contextid'=>$context->id, 'component'=>'atto_ejsapp', 'filearea'=>'controllerfile', 'itemid'=>$incremental), 'filesize DESC');
    $file_record_controller = reset($file_records_controller);
    if($file_record_controller) {
        $fs = get_file_storage();
        $file_controller = $fs->get_file_by_id($file_record_controller->id);
        $file_controller->copy_content_to($folderpath . "simfiles/".$file_record_controller->filename);
        $simulation_controller_file = $CFG->wwwroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . "/simfiles/".$file_record_controller->filename;
    }

    //Recording file rec
    $simulation_recording_file = "";
    file_save_draft_area_files($fromform->recordingfile, $context->id, 'atto_ejsapp', 'recordingfiles', $incremental, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '.rec'));
    $file_records_recording = $DB->get_records('files', array('contextid'=>$context->id, 'component'=>'atto_ejsapp', 'filearea'=>'recordingfiles', 'itemid'=>$incremental), 'filesize DESC');
    $file_record_recording = reset($file_records_recording);
    if($file_record_recording) {
        $fs = get_file_storage();
        $file_recording = $fs->get_file_by_id($file_record_recording->id);
        $file_recording->copy_content_to($folderpath . "simfiles/".$file_record_recording->filename);
        $simulation_recording_file = $CFG->wwwroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . "/simfiles/".$file_record_recording->filename;
    }


    if ($ext == 'jar') {//Si el fichero subido es un applet

        //Inicializa los elementos
        $code = '';
        $codebase = '';
        $height = 0;
        $width = 0;
        $applet_id = substr($file_record->filename, 0, -4);
        $cache_archive = $applet_id . '.jar';
        $context_id = $context->id;
        $moodle_upload_file = $CFG->wwwroot . "/mod/ejsapp/upload_file.php";

        //Se saca el codebase
        preg_match('/http:\/\/.+?\/(.+)/', $CFG->wwwroot, $match_result);
        if (!empty($match_result) and $match_result[1]) {
            $codebase .= '/' . $match_result[1];
        }
        $codebase .= '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . '/';

        if (file_exists($filepath)) {
            // Extract the manifest.mf file from the .jar
            $manifest = file_get_contents('zip://' . $filepath . '#' . 'META-INF/MANIFEST.MF');


            // get the .class file
            $class_file = get_class_for_java($manifest);
            $code = preg_replace('/\s+/', "", $class_file); // delete all white-spaces and the first newline

            // width
            $manifest_width = get_width_for_java($manifest);
            if ($fromform->custom_width != "") {
                $width = $fromform->custom_width;
            } else {
                $width = $manifest_width;
            }

            //height
            $manifest_height = get_height_for_java($manifest);
            if ($fromform->custom_height != "") {
                $height = $fromform->custom_height;
            } else {
                $height = $manifest_height;
            }

            //aspect ratio
            if ($fromform->preserve_aspect_ratio != 0) {
                $height = floor($width * $manifest_height / $manifest_width);
            }
        }


        echo $mform->generateEventJava($code, $codebase, $applet_id, $width, $height, $cache_archive, $context_id, $CFG->wwwroot, $simulation_state_file, $simulation_controller_file, $simulation_recording_file);
    } else { //El fichero subido es un JS
        $ejsapp = new stdClass();
        $ejsapp->applet_name = "";
        $ejsapp->css = "";

        //Extracts de .zip, and modify some of the extracted files
        modifications_for_javascript($filepath, $ejsapp, $folderpath, $codebase);

        $www_path = $CFG->wwwroot . $codebase;
        $path  = $CFG->dirroot . $codebase;

        $filename = substr($ejsapp->applet_name, 0, strpos($ejsapp->applet_name, '.'));
        $extension = substr($ejsapp->applet_name, strpos($ejsapp->applet_name, ".") + 1);

        $language = current_language();
        $file_headers = @get_headers($www_path . $filename . '_' . $language . '.' . $extension);
        if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $www_fichero = $www_path . $ejsapp->applet_name;
            $ruta_fichero = $path . $ejsapp->applet_name;
        }
        else {
            $www_fichero = $www_path . $filename . '_' . $language . '.' . $extension;
            $ruta_fichero = $path . $filename . '_' . $language . '.' . $extension;
        }

        //loads state file if it exists
        if ($simulation_state_file != "") {

            $search = "window.addEventListener('scroll', function () { if (_model._resized) _model._resized(window.innerWidth,window.innerHeight); }, false);";
            $replace = "window.addEventListener('scroll', function () { if (_model._resized) _model._resized(window.innerWidth,window.innerHeight); }, false);
                        _model.readState('$simulation_state_file','.json');
                        clearInterval(interval);
                        };
                        }, 200)
                        var kk = setInterval(function() {
                        if(false) {
                        ";
            if (file_exists($ruta_fichero)) {
                $code = file_get_contents($ruta_fichero);
                $code = str_replace($search, $replace, $code);
                file_put_contents($ruta_fichero, $code);
            }
        }

        //loads .rec file if it exists
        if ($simulation_recording_file != "") {
            $end_message = get_string('end_message','ejsapp');
            $search = "window.addEventListener('scroll', function () { if (_model._resized) _model._resized(window.innerWidth,window.innerHeight); }, false);";
            $replace = "window.addEventListener('scroll', function () { if (_model._resized) _model._resized(window.innerWidth,window.innerHeight); }, false);
                        window.addEventListener('load', function() { _model.readText('$simulation_recording_file','.rec',function(content){_model.playCapture(JSON.parse(content),function(){alert('$end_message')})}); }, false);";

            if (file_exists($ruta_fichero)) {
                $code = file_get_contents($ruta_fichero);
                $code = str_replace($search, $replace, $code);
                file_put_contents($ruta_fichero, $code);
            }
        }


        echo $mform->generateEventJs($www_fichero);
    }
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