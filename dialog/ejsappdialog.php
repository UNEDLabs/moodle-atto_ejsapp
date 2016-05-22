<?php
/**
 * Created by PhpStorm.
 * @package    ejsapp
 * @subpackage atto_ejsapp
 */


//defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../../../../../config.php');
require_once (__DIR__ . '/atto_ejss_simulation.php');
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
    //If there is date in the form Post, the simulation object is created
    $objSimulation = new atto_ejss_simulation($CFG);

    $simulation_state_file = "";
    $simulation_controller_file = "";
    $simulation_recording_file = "";

    //Saves simulation files to draft
    if($objSimulation->saveSimulationFilesToDraft($CFG, $context, $fromform->appletfile)) {
        //Saves the simulations files into the required path
        $ext = $objSimulation->createSimulationFiles($CFG, $DB, $context);

        //Creates initialization files if they exist
        $objSimulation->createInitializationFiles($CFG, $DB, $context, $fromform);

        //Asigns the path of the initialization files
        $simulation_state_file = $objSimulation->getSimulationStateFile();
        $simulation_controller_file = $objSimulation->getSimulationControllerFile();
        $simulation_recording_file = $objSimulation->getSimulationRecordingFile();

    }


    if ($ext == 'jar') {//Si el fichero subido es un applet

        //Inicializa los elementos
        $code = '';
        $codebase = '';
        $height = 0;
        $width = 0;
        $applet_id = substr($objSimulation->getFileName(), 0, -4);
        $cache_archive = $applet_id . '.jar';
        $context_id = $context->id;
        $moodle_upload_file = $CFG->wwwroot . "/mod/ejsapp/upload_file.php";

        //Se saca el codebase
        preg_match('/http:\/\/.+?\/(.+)/', $CFG->wwwroot, $match_result);
        if (!empty($match_result) and $match_result[1]) {
            $codebase .= '/' . $match_result[1];
        }
        $codebase .= $objSimulation->getCodebase();

        if (file_exists($objSimulation->getFilePath())) {
            // Extract the manifest.mf file from the .jar
            $manifest = file_get_contents('zip://' . $objSimulation->getFilePath() . '#' . 'META-INF/MANIFEST.MF');


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
        modifications_for_javascript($objSimulation->getFilePath(), $ejsapp, $objSimulation->getFolderPath(), $objSimulation->getCodebase());

        $www_path = $CFG->wwwroot . $objSimulation->getCodebase();
        $path  = $CFG->dirroot . $objSimulation->getCodebase();

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
                        _model.readText('$simulation_recording_file','.rec',function(content){_model.playCapture(JSON.parse(content),function(){alert('$end_message')})});
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