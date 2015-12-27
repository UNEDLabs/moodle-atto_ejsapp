<?php
/**
 * Created by PhpStorm.
 * User: casa
 * Date: 08/11/2015
 * Time: 21:42
 */

//defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir."/formslib.php");

/**
 * Class that defines the atto_EJSApp settings form.
 */
class atto_ejsapp_mod_form extends moodleform
{


    /**
     * Called from Moodle to define this form
     *
     * @return void
     */
    function definition()
    {
        global $CFG;
        $mform =& $this->_form;

        // Adding atto_ejsapp settings by adding more fieldsets
        $mform->addElement('header', 'conf_parameters', get_string('jar_file', 'atto_ejsapp'));

        $mform->addElement('hidden', 'class_file', null);
        $mform->setType('class_file', PARAM_TEXT);
        $mform->setDefault('class_file', 'null');

        $mform->addElement('hidden', 'codebase', null);
        $mform->setType('codebase', PARAM_TEXT);
        $mform->setDefault('codebase', 'null');

        $mform->addElement('hidden', 'mainframe', null);
        $mform->setType('mainframe', PARAM_TEXT);
        $mform->setDefault('mainframe', 'null');

        $mform->addElement('hidden', 'is_collaborative', null);
        $mform->setType('is_collaborative', PARAM_TEXT);
        $mform->setDefault('is_collaborative', 0);

        $mform->addElement('hidden', 'manifest', null);
        $mform->setType('manifest', PARAM_TEXT);
        $mform->setDefault('manifest', '');

        $mform->addElement('hidden', 'applet_name', null);
        $mform->setType('applet_name', PARAM_TEXT);
        $mform->setDefault('applet_name', '');

        $maxbytes = get_max_upload_file_size($CFG->maxbytes);
        $mform->addElement('filemanager', 'appletfile', get_string('file'), null, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1, 'accepted_types' => array('application/java-archive', 'application/zip')));
        $mform->addRule('appletfile', get_string('appletfile_required', 'atto_ejsapp'), 'required');
        $mform->addHelpButton('appletfile', 'appletfile', 'atto_ejsapp');

        $mform->addElement('select', 'applet_size_conf', get_string('applet_size_conf','ejsapp'), array(get_string('preserve_applet_size','ejsapp'), get_string('moodle_resize','ejsapp'), get_string('user_resize','ejsapp')));
        $mform->addHelpButton('applet_size_conf', 'applet_size_conf', 'atto_ejsapp');

        $mform->addElement('selectyesno', 'preserve_aspect_ratio', get_string('preserve_aspect_ratio', 'atto_ejsapp'));
        $mform->addHelpButton('preserve_aspect_ratio', 'preserve_aspect_ratio', 'atto_ejsapp');
        $mform->disabledIf('preserve_aspect_ratio', 'applet_size_conf', 'neq', 2);

        $mform->addElement('text', 'custom_width', get_string('custom_width', 'atto_ejsapp'), array('size' => '3'));
        $mform->setType('custom_width', PARAM_INT);
        $mform->disabledIf('custom_width', 'applet_size_conf', 'neq', 2);

        $mform->addElement('text', 'custom_height', get_string('custom_height', 'atto_ejsapp'), array('size' => '3'));
        $mform->setType('custom_height', PARAM_INT);
        $mform->disabledIf('custom_height', 'applet_size_conf', 'neq', 2);
        $mform->disabledIf('custom_height', 'preserve_aspect_ratio', 'eq', 1);

        $this->add_action_buttons();

    } // definition



    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        if ($data['applet_size_conf'] == 2) {
            if (empty($data['custom_width'])) {
                $errors['custom_width'] = get_string('custom_width_required', 'ejsapp');
            }
            if ($data['preserve_aspect_ratio'] == 0) {
                if (empty($data['custom_height'])) {
                    $errors['custom_height'] = get_string('custom_height_required', 'ejsapp');
                }
            }
        }

        return $errors;
    } // validation

    function generateEvent($code, $codebase, $applet_id, $width, $height, $cache_archive, $context_id, $moodle_upload_file){
        $script ="<script language='JavaScript'>
                    var event = new CustomEvent('atto_ejsapp_form_submit', {
                        detail: {
                            code: '$code',
                            codebase: '$codebase',
                            applet_id: '$applet_id',
                            width: '$width',
                            height: '$height',
                            cache_archive: '$cache_archive',
                            context_id: '$context_id'
                        }
                    });
                    window.parent.document.dispatchEvent(event);
                </script>";

        return $script;
    }

} // class mod_ejsapp_mod_form