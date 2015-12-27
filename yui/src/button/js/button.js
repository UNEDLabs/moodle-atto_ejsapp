
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

var TEMPLATE = '' +
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
                    'value="http://localhost/mod/ejsapp/upload_file.php" />' +
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
        document.addEventListener('atto_ejsapp_form_submit', function(e) {
            self._dialogue.hide();
            self._insertContent(e.detail);
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
     * Create the content
     *
     * @private
     */
    _insertContent: function(campos_form) {
        this.editor.focus();

        /*var html = '<applet code="users.dav.wc.stp.Ising2D_pkg.Ising2DApplet.class" ' +
            'codebase="/mod/ejsapp/jarfiles/2/3/" id="ejs_stp_Ising2D" ' +
            'width="553" height="578">' +
            '<param name="cache_archive" value="ejs_stp_Ising2D.jar"/>' +
            '<param name="permissions" value="sandbox"/>' +
            '<param name="codebase_lookup" value="false"/>' +
            '<param name="context_id" value="8"/>' +
            '<param name="user_id" value="2"/>' +
            '<param name="ejsapp_id" value="3"/>' +
            '<param name="language" value="en"/>' +
            '<param name="username" value="Admin Usuario"/>' +
            '<param name="user_moodle" value="admin"/>' +
            '<param name="password_moodle" value="DEPRECATED"/>' +
            '<param name="moodle_upload_file" ' +
            'value="http://localhost/mod/ejsapp/upload_file.php"/>' +
            '<param name="lookandfeel" value="NIMBUS"/>' +
            '<param name="is_collaborative" value="false"/>' +
            '</applet>';*/

        var template = Y.Handlebars.compile(TEMPLATE);
        var content = template({
            code: campos_form.code,
            codebase: campos_form.codebase,
            applet_id: campos_form.applet_id,
            width: campos_form.width,
            height: campos_form.height,
            cache_archive: campos_form.cache_archive,
            context_id: campos_form.context_id
        });
        this.get('host').insertContentAtFocusPoint(content);

        this.markUpdated();
    }

});
