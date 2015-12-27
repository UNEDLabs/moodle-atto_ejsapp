<?php

/**
 * Created by PhpStorm.
 * @package    ejsapp
 * @subpackage atto_ejsapp
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Initialise the strings required for JS.
 *
 * @return void
 */
function atto_ejsapp_strings_for_js() {
    global $PAGE;
    $PAGE->requires->strings_for_js(array('xxxxxx'), 'atto_ejsapp');
}

