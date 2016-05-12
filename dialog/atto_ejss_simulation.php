<?php

require_once (__DIR__ . '/SimulationStateFile.php');
require_once (__DIR__ . '/SimulationControllerFile.php');
require_once (__DIR__ . '/SimulationRecordingFile.php');

/**
 * This class represents the simulation
 */
class atto_ejss_simulation
{
    /**
     * Max bytes in the file
     */
    private $maxbytes;

    /**
     * folder number to store the simulation files
     */
    private $incremental = 0;

    /**
     * plugin path
     */
    private $codebase = '/lib/editor/atto/plugins/ejsapp/jarfiles/';

    /**
     * path of the folder to store the simulation files
     */
    private $folderpath;

    /**
     * path of the file to store the simulation files
     */
    private $filepath;

    /**
     * simulation큦 file extension
     */
    private $ext;

    /**
     * simulation큦 state file
     */
    private $simulationStateFile;

    /**
     * simulation큦 Controller file
     */
    private $simulationControllerFile;

    /**
     * simulation큦 Recording file
     */
    private $simulationRecordingFile;

    /**
     * file큦 name
     */
    private $fileName;


    /**
     * Class constructor
     */
    public function __construct($CFG) {
        $this->folderpath = $CFG->dirroot . $this->codebase;
    }

    /**
     * Saves the simulation file into the draft area
     * @return boolean
     */
    public function saveSimulationFilesToDraft($CFG, $context, $draftitemid_applet){
        $result = false;

        $this->maxbytes = get_max_upload_file_size($CFG->maxbytes);

        //Obtains the folder number to store the file
        while((file_exists($this->folderpath.$this->incremental))){
            $this->incremental ++;
        }

        //Saves the file in draft area
        if ($draftitemid_applet) {
            file_save_draft_area_files($draftitemid_applet, $context->id, 'atto_ejsapp', 'jarfiles', $this->incremental, array('subdirs' => 0, 'maxbytes' => $this->maxbytes, 'maxfiles' => 1, 'accepted_types' => array('application/java-archive', 'application/zip')));
            $result = true;
        }

        return $result;
    }

    /**
     * Saves the simulation file into the draft area
     * @return String with the path to the .zip or the .jar
     */
    public function createSimulationFiles($CFG, $DB, $context){
        // Obtain the uploaded .zip or .jar file from moodledata using the information in the files table
        $file_records = $DB->get_records('files', array('contextid'=>$context->id, 'component'=>'atto_ejsapp', 'filearea'=>'jarfiles', 'itemid'=>$this->incremental), 'filesize DESC');
        $file_record = reset($file_records);
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($file_record->id);
        $this->fileName = $file_record->filename;

        // Create folders to store the .jar or .zip file
        if (!file_exists($this->folderpath)) {
            mkdir($this->folderpath, 0755);
        }
        $this->codebase = '/lib/editor/atto/plugins/ejsapp/jarfiles/'. $this->incremental . '/';
        $this->folderpath = $CFG->dirroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $this->incremental . '/';
        if (!file_exists($this->folderpath)) { // updating, not creating, the ejsapp activity
            mkdir($this->folderpath, 0770);
        }

        // Create folders to store the additional files like state
        if (!file_exists($this->folderpath."simfiles/")) {
            mkdir($this->folderpath."simfiles/", 0755);
        }

        // Copy the jar/zip file to its destination folder in jarfiles
        $this->filepath = $this->folderpath . $file_record->filename;
        $file->copy_content_to($this->filepath);

        $this->ext = pathinfo($file->get_filename(), PATHINFO_EXTENSION);

        return $this->ext;
    }

    /**
     * Creates the initialization files if the exists
     *
     */
    public function createInitializationFiles($CFG, $DB, $context, $fromform){
        //State file
        $objSimulationStateFile = new SimulationStateFile();
        $objSimulationStateFile->setInitilizationFile($CFG, $DB, $context, $fromform, $this->incremental, $this->maxbytes, $this->folderpath);
        $this->simulationStateFile = $objSimulationStateFile->getSimulationFilePath();

        //Controller File
        $objSimulationControllerFile = new SimulationControllerFile();
        $objSimulationControllerFile->setInitilizationFile($CFG, $DB, $context, $fromform, $this->incremental, $this->maxbytes, $this->folderpath);
        $this->simulationControllerFile = $objSimulationControllerFile->getSimulationFilePath();

        //Recording file
        $objSimulationRecordingFile = new SimulationRecordingFile();
        $objSimulationRecordingFile->setInitilizationFile($CFG, $DB, $context, $fromform, $this->incremental, $this->maxbytes, $this->folderpath);
        $this->simulationRecordingFile = $objSimulationRecordingFile->getSimulationFilePath();
    }

    public function getSimulationStateFile(){
        return $this->simulationStateFile;
    }

    public function getSimulationControllerFile(){
        return $this->simulationControllerFile;
    }

    public function getSimulationRecordingFile(){
        return $this->simulationRecordingFile;
    }

    public function getFileName(){
        return $this->fileName;
    }

    public function getIncremental(){
        return $this->incremental;
    }

    public function getCodebase(){
        return $this->codebase;
    }

    public function getFilePath(){
        return $this->filepath;
    }

    public function getFolderPath(){
        return $this->folderpath;
    }
}