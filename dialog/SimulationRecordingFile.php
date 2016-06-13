<?php

require_once ('SimulationInitializationFile.php');

/**
 * Created by PhpStorm.
 */
class SimulationRecordingFile extends SimulationInitializationFile
{
    public function setInitilizationFile($CFG, $DB, $context, $fromform, $incremental, $maxbytes, $folderpath){
        $this->setSimulationFilePath("");
        file_save_draft_area_files($fromform->recordingfile, $context->id, 'atto_ejsapp', 'recordingfiles', $incremental, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => '.rec'));

        $fs = get_file_storage();
        if ($files = $fs->get_area_files($context->id, 'atto_ejsapp', 'recordingfiles', $incremental, 'sortorder', false)) {
            foreach($files as $file) {
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                $this->setSimulationFilePath($fileurl);
            }
        }

        /*$file_records = $DB->get_records('files', array('contextid'=>$context->id, 'component'=>'atto_ejsapp', 'filearea'=>'recordingfiles', 'itemid'=>$incremental), 'filesize DESC');
        $file_record = reset($file_records);
        if($file_record) {
            $fs = get_file_storage();
            $file_state = $fs->get_file_by_id($file_record->id);
            $file_state->copy_content_to($folderpath . "simfiles/".$file_record->filename);
            $this->setSimulationFilePath($CFG->wwwroot . '/lib/editor/atto/plugins/ejsapp/jarfiles/' . $incremental . "/simfiles/".$file_record->filename);
        }*/
    }
}