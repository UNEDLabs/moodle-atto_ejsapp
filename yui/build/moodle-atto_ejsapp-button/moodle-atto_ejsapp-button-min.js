YUI.add("moodle-atto_ejsapp-button",function(e,t){var n='<applet code="{{code}}" codebase="{{codebase}}" id="{{applet_id}}" width="{{width}}" height="{{height}}"><param name="cache_archive" value="{{cache_archive}}" /><param name="permissions" value="sandbox" /><param name="codebase_lookup" value="false" /><param name="context_id" value="{{context_id}}" /><param name="user_id" value="2" /><param name="ejsapp_id" value="3" /><param name="language" value="en" /><param name="username" value="Admin Usuario" /><param name="user_moodle" value="admin" /><param name="password_moodle" value="DEPRECATED" /><param name="moodle_upload_file" value="http://localhost/mod/ejsapp/upload_file.php" /><param name="lookandfeel" value="NIMBUS" /><param name="is_collaborative" value="false" /></applet>';e.namespace("M.atto_ejsapp").Button=e.Base.create("button",e.M.editor_atto.EditorPlugin,[],{initializer:function(){this.addButton({icon:"ejsapp_icon",callback:this._displayDialogue,iconComponent:"atto_ejsapp",buttonName:"ejsapp_icon"});var e=this;document.addEventListener("atto_ejsapp_form_submit",function(t){e._dialogue.hide(),e._insertContent(t.detail)})},_displayDialogue:function(){var t=this.getDialogue({headerContent:"headerContent del dialogo EJSAPP",focusAfterHide:!0,width:"800px"}),n=e.Node.create("<iframe></iframe>");n.setStyles({height:"700px",border:"none",width:"100%"}),n.setAttribute("src",this._getIframeURL());var r=e.Node.create("<div></div>");r.append(n),t.set("bodyContent",r),t.show(),this.markUpdated()},_getIframeURL:function(){return M.cfg.wwwroot+"/lib/editor/atto/plugins/ejsapp/dialog/ejsappdialog.php"},_insertContent:function(t){this.editor.focus();var r=e.Handlebars.compile(n),i=r({code:t.code,codebase:t.codebase,applet_id:t.applet_id,width:t.width,height:t.height,cache_archive:t.cache_archive,context_id:t.context_id});this.get("host").insertContentAtFocusPoint(i),this.markUpdated()}})},"@VERSION@",{requires:["moodle-editor_ejsapp-plugin"]});
