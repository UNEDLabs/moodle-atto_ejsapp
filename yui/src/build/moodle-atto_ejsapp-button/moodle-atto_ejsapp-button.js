YUI.add('moodle-atto_ejsapp-button', function (Y, NAME) {


/*
 * @package    atto_ejsapp
 * @copyright  2015 Jorge Esteban
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_ejsapp-button
 */

/**
 * Atto text editor ejsapp plugin.
 *
 * @namespace M.atto_ejsapp
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_ejsapp').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        this.addButton({
            icon: 'ejsapp_icon___',
            iconComponent: 'atto_ejsapp___',
            buttonName: 'ejsapp_icon'
        });
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_ejsapp-plugin"]});
