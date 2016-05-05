<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'atto_ejsapp', language 'en'.
 *
 * @package    atto_ejsapp
 * @copyright  2015 Jorge Esteban
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'atto_ejsapp';
$string['xxxxxx'] = 'texto xxxxx';
$string['jar_file'] = '.jar or .zip file that encapsulates the  EJsS lab';
$string['appletfile'] = 'Easy Java(script) Simulation';
$string['appletfile_required'] = 'A .jar or a .zip file must be selected';
$string['appletfile_help'] = 'Select the .jar or .zip file that encapsulates the Easy Java(script) Simulation (EJsS) application. The official website of EJsS is http://fem.um.es/Ejs/';
$string['title'] = 'page title';

$string['size_header'] = 'Customize size';
$string['applet_size_conf'] = 'Size the applet';
$string['applet_size_conf_help'] = 'Three options: 1) "Preserve original size" will preserve the original size of the EJS applet, 2) "Let Moodle set the size" will resize the applet to take up all the possible space while mantaining the original aspect ratio, 3) "Let the user set the size" will let the user to set the size of the applet and select whether to preserve its original aspect ratio or not.';
$string['preserve_applet_size'] = 'Preserve original size';
$string['moodle_resize'] = 'Let Moodle set the size';
$string['user_resize'] = 'Let the user set the size';

$string['preserve_aspect_ratio'] = 'Preserve aspect ratio';
$string['preserve_aspect_ratio_help'] = 'If this option is selected, the original aspect ratio of the applet will be respected. In this case, the user will be able to modify the width of the applet and the system will automatically adjust its height. If this option is set to "no", the user will be able to set both the width and the height of the applet.';

$string['custom_width'] = 'Applet width (px)';
$string['custom_width_required'] = 'WARNING: Applet width was not set. You must provide a different value.';

$string['custom_height'] = 'Applet height (px)';
$string['custom_height_required'] = 'WARNING: Applet height was not set. You must provide a different value.';

$string['state_file'] = '.xml or .json file with the state to be read when this EJsS lab loads';
$string['statefile'] = 'Easy Java(script) Simulation State';
$string['statefile_help'] = 'Select the .xml (for Java) or .json (for Javascript) file with the state the EJsS application should load.';

$string['controller_file'] = '.cnt file with the controller to be load when the EJS is initialized';
$string['controllerfile'] = 'Easy Java(script) Simulation Controller';
$string['controllerfile_help'] = 'Select the .cnt file with the code of the controller to be load when the the EJS application is initialized.';

$string['recording_file'] = '.rec file with the recording to be run when this EJS lab loads';
$string['recordingfile'] = 'Easy Java(script) Simulations Recording';
$string['recordingfile_help'] = 'Select the .rec file with the interaction recording the EJS application should run.';

$string['end_message'] = 'End of reproduction';
