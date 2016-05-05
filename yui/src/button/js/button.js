
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

var TEMPLATE_JAVA = '' +
                '<applet code="{{code}}" ' +
                'codebase="{{codebase}}" id="{{applet_id}}" ' +
                'width="{{width}}" height="{{height}}">' +
                    '<param name="cache_archive" value="{{cache_archive}}" />' +
                    '<param name="permissions" value="sandbox" />' +
                    '<param name="codebase_lookup" value="false" />' +
                    '<param name="context_id" value="{{context_id}}" />' +
                    '<param name="user_id" value="2" />' +
                    '<param name="ejsapp_id" value="3" />' +
                    '<param name="language" value="en" />' +
                    '<param name="username" value="Admin Usuario" />' +
                    '<param name="user_moodle" value="admin" />' +
                    '<param name="password_moodle" value="DEPRECATED" />' +
                    '<param name="moodle_upload_file" ' +
                    'value="{{host}}/mod/ejsapp/upload_file.php" />' +
                    '<param name="lookandfeel" value="NIMBUS" />' +
                    '<param name="is_collaborative" value="false" />' +
                '</applet>';

Y.namespace('M.atto_ejsapp').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        this.addButton({
            icon: 'ejsapp_icon',
            callback: this._displayDialogue,
            iconComponent: 'atto_ejsapp',
            buttonName: 'ejsapp_icon'
        });

        var self = this;
        document.addEventListener('atto_ejsapp_form_submit_java', function(e) {
            self._dialogue.hide();
            self._insertContentJava(e.detail);
        });

        document.addEventListener('atto_ejsapp_form_submit_js', function(e) {
            self._dialogue.hide();
            self._insertContentJs(e.detail);
        });

    },


    /**
     * Display the form dialogue.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {

        var dialogue = this.getDialogue({
            headerContent: 'headerContent del dialogo EJSAPP',
            focusAfterHide: true,
            width: '800px'
        });

        var iframe = Y.Node.create('<iframe></iframe>');
        iframe.setStyles({
            height: '700px',
            border: 'none',
            width: '100%'
        });
        iframe.setAttribute('src', this._getIframeURL());

        var bodycontent =  Y.Node.create('<div></div>');
        bodycontent.append(iframe);

        // Set the dialogue content, and then show the dialogue.
        dialogue.set('bodyContent', bodycontent);
        dialogue.show();

        //Se invoca cuando ha cambiado el area editable
        this.markUpdated();
    },


    /**
     * Returns the URL to the EJSApp form
     *
     * @return {String} URL
     * @private
     */
    _getIframeURL: function() {
        return M.cfg.wwwroot + '/lib/editor/atto/plugins/ejsapp/dialog/ejsappdialog.php';
    },


    /**
     * Create the content for Java
     *
     * @private
     */
    _insertContentJava: function(campos_form) {
        this.editor.focus();

        var template = Y.Handlebars.compile(TEMPLATE_JAVA);
        var content = template({
            code: campos_form.code,
            codebase: campos_form.codebase,
            applet_id: campos_form.applet_id,
            width: campos_form.width,
            height: campos_form.height,
            cache_archive: campos_form.cache_archive,
            context_id: campos_form.context_id,
            host: campos_form.host
        });

        var applet_id = campos_form.applet_id;
        if(campos_form.simulation_state_file !== ''){
            var simfile = campos_form.simulation_state_file;
            var content_state = '<script type="text/javascript">function loadState(count) {' +
            'if (!' + applet_id + '._simulation && count > 0) {' +
            'window.setTimeout( function() { loadState( --count ); }, 1000 );' +
            '}' +
            'else if (' + applet_id + '._simulation) {' +
            'window.setTimeout( function() {' + applet_id + '._readState("url:' + simfile + '"); }, 100 );' +
            '' + applet_id + '._view.resetTraces();' +
            '}' +
            '}' +
            'loadState(10);</script>';

            content += content_state;
        }

        if(campos_form.simulation_controller_file !== ''){
            var cntfile = campos_form.simulation_controller_file;
            var content_cnt = '<script type="text/javascript">function loadController(count) {' +
                'if (!' + applet_id + '._model && count > 0) {' +
                'window.setTimeout( function() { loadController( --count ); }, 1000 );' +
                '}' +
                'else if (' + applet_id + '._model) {' +
                'window.setTimeout( function() {' +
                'var element = ' + applet_id + '._model.getUserData("_codeController");' +
                'element.setController(' + applet_id + '._readText("url:' + cntfile + '")); }, 100 );' +
                '}' +
                '}' +
                'loadController(10);</script>';

            content += content_cnt;
        }

        if(campos_form.simulation_recording_file !== ''){
            var recfile = campos_form.simulation_recording_file;
            var content_rec = '<script type="text/javascript">function loadExperiment(count) {' +
                'if (!' + applet_id + '._simulation && count > 0) {' +
                'window.setTimeout( function() { loadExperiment( --count ); }, 1000 );' +
                '}' +
                'else if (' + applet_id + '._simulation) {' +
                'window.setTimeout( function() {' +
                applet_id + '._simulation.runLoadExperiment("url:' + recfile + '"); }, 100 );' +
                '}' +
                '}' +
                'loadExperiment(10);</script>';

            content += content_rec;
        }

        this.get('host').insertContentAtFocusPoint(content);

        this.markUpdated();
    },

    /**
     * Create the content
     *
     * @private
     */
    _insertContentJs: function(campos_form) {
        this.editor.focus();
        var dialogo = this.get('host');

        var ioconfig = {
            method: 'POST',
            sync: true,
            data: {'sesskey' : M.cfg.sesskey},
            on: {
                success: function (o, response) {
                    dialogo.insertContentAtFocusPoint(response.responseText);
                },

                failure: function () {
                    dialogo.insertContentAtFocusPoint("error getting the simulation");
                }
            }
        };

        Y.io(campos_form.ruta_fichero, ioconfig);

        this.markUpdated();
    }

});
