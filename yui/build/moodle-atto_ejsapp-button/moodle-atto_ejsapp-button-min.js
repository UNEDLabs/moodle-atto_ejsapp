YUI.add("moodle-atto_ejsapp-button",function(e,t){var n='<applet code="{{code}}" codebase="{{codebase}}" id="{{applet_id}}" width="{{width}}" height="{{height}}"><param name="cache_archive" value="{{cache_archive}}" /><param name="permissions" value="sandbox" /><param name="codebase_lookup" value="false" /><param name="context_id" value="{{context_id}}" /><param name="user_id" value="2" /><param name="ejsapp_id" value="3" /><param name="language" value="en" /><param name="username" value="Admin Usuario" /><param name="user_moodle" value="admin" /><param name="password_moodle" value="DEPRECATED" /><param name="moodle_upload_file" value="{{host}}/mod/ejsapp/upload_file.php" /><param name="lookandfeel" value="NIMBUS" /><param name="is_collaborative" value="false" /></applet>';e.namespace("M.atto_ejsapp").Button=e.Base.create("button",e.M.editor_atto.EditorPlugin,[],{initializer:function(){this.addButton({icon:"ejsapp_icon",callback:this._displayDialogue,iconComponent:"atto_ejsapp",buttonName:"ejsapp_icon"});var e=this;document.addEventListener("atto_ejsapp_form_submit_java",function(t){e._dialogue.hide(),e._insertContentJava(t.detail)}),document.addEventListener("atto_ejsapp_form_submit_js",function(t){e._dialogue.hide(),e._insertContentJs(t.detail)})},_displayDialogue:function(){var t=this.getDialogue({headerContent:"headerContent del dialogo EJSAPP",focusAfterHide:!0,width:"800px"}),n=e.Node.create("<iframe></iframe>");n.setStyles({height:"700px",border:"none",width:"100%"}),n.setAttribute("src",this._getIframeURL());var r=e.Node.create("<div></div>");r.append(n),t.set("bodyContent",r),t.show(),this.markUpdated()},_getIframeURL:function(){return M.cfg.wwwroot+"/lib/editor/atto/plugins/ejsapp/dialog/ejsappdialog.php"},_insertContentJava:function(t){this.editor.focus();var r=e.Handlebars.compile(n),i=r({code:t.code,codebase:t.codebase,applet_id:t.applet_id,width:t.width,height:t.height,cache_archive:t.cache_archive,context_id:t.context_id,host:t.host}),s=t.applet_id;if(t.simulation_state_file!==""){var o=t.simulation_state_file,u='<script type="text/javascript">function loadState(count) {if (!'+s+"._simulation && count > 0) {"+"window.setTimeout( function() { loadState( --count ); }, 1000 );"+"}"+"else if ("+s+"._simulation) {"+"window.setTimeout( function() {"+s+'._readState("url:'+o+'"); }, 100 );'+""+s+"._view.resetTraces();"+"}"+"}"+"loadState(10);</script>";i+=u}if(t.simulation_controller_file!==""){var a=t.simulation_controller_file,f='<script type="text/javascript">function loadController(count) {if (!'+s+"._model && count > 0) {"+"window.setTimeout( function() { loadController( --count ); }, 1000 );"+"}"+"else if ("+s+"._model) {"+"window.setTimeout( function() {"+"var element = "+s+'._model.getUserData("_codeController");'+"element.setController("+s+'._readText("url:'+a+'")); }, 100 );'+"}"+"}"+"loadController(10);</script>";i+=f}if(t.simulation_recording_file!==""){var l=t.simulation_recording_file,c='<script type="text/javascript">function loadExperiment(count) {if (!'+s+"._simulation && count > 0) {"+"window.setTimeout( function() { loadExperiment( --count ); }, 1000 );"+"}"+"else if ("+s+"._simulation) {"+"window.setTimeout( function() {"+s+'._simulation.runLoadExperiment("url:'+l+'"); }, 100 );'+"}"+"}"+"loadExperiment(10);</script>";i+=c}this.get("host").insertContentAtFocusPoint(i),this.markUpdated()},_insertContentJs:function(t){this.editor.focus();var n=this.get("host"),r={method:"POST",sync:!0,data:{sesskey:M.cfg.sesskey},on:{success:function(e,t){n.insertContentAtFocusPoint(t.responseText)},failure:function(){n.insertContentAtFocusPoint("error getting the simulation")}}};e.io(t.ruta_fichero,r),this.markUpdated()}})},"@VERSION@",{requires:["moodle-editor_ejsapp-plugin"]});
