<?php

require_once (__DIR__ . '/SimulationInitializationFile.php');

/**
 * Created by PhpStorm.
 */
class SimulationStateFile extends SimulationInitializationFile
{
    public function setInitilizationFile($CFG, $DB, $context, $fromform, $incremental, $maxbytes, $folderpath){
        $this->setSimulationFilePath("");
        file_save_draft_area_files($fromform->statefile, $context->id, 'atto_ejsapp', 'statefiles', $incremental, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => array('application/xml', 'application/json')));
        $file_records = $DB->get_records('files', array('contextid'=>$context->id, 'component'=>'atto_ejsapp', 'filearea'=>'statefiles', 'itemid'=>$incremental), 'filesize DESC');
        $file_record = reset($file_records);
        if($file_record) {
            $fs = get_file_storage();
            $file_state = $fs->get_file_by_id($file_record->id);
            $file_state->copy_content_to($folderpath . "simfiles/".$file_record->filename);
            $this->setSimulationFilePath($CFG->wwwroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . "/simfiles/".$file_record->filename);
        }
    }
}