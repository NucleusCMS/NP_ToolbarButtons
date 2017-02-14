<?php
class NP_ToolbarButtons extends NucleusPlugin {
    function getName()              { return __CLASS__; }
    function getAuthor()            { return 'Katsumi + nakahara21'; }
    function getVersion()           { return '0.4'; }
    function getURL()               { return 'http://japan.nucleuscms.org/bb/viewtopic.php?t=3413';}
    function getMinNucleusVersion() { return 380; }
    function getDescription()       { return $this->getName().' plugin'; }
    function supportsFeature($key)  { return (int)in_array($key, array('NoSql')); }
    function getEventList()         { return array('PrepareItemForEdit', 'PreAddItemForm',
        'AdminPrePageHead', 'AdminPrePageFoot',
        'AddItemFormExtras', 'EditItemFormExtras',
        'PreToolbarParse','PrePluginOptionsEdit'); }
    function install(){
        $this->createOption("lbtns", _TOOLBARBUTTONS_CODESBEFOREDEFAULT, "textarea",''."\n");
        $this->createOption("rbtns", _TOOLBARBUTTONS_CODESAFTERDEFAULT, "textarea",''."\n");
        $this->createOption("addscripts", _TOOLBARBUTTONS_ADDITIONALSCRIPTS, "textarea",''."\n");
    }
    function event_PrePluginOptionsEdit(&$data) {
        if ($data['context']!='global' || $data['plugid']!=$this->getID()) return;
        foreach($data['options'] as $tmp){
            switch($tmp['name']){
            case 'lbtns':
                $lbtns = 'plugoption['.$tmp['oid'].']['.$tmp['contextid'].']';
                break;
            case 'rbtns':
                $rbtns = 'plugoption['.$tmp['oid'].']['.$tmp['contextid'].']';
                $oid = $tmp['oid'];
            default:
                break;
            }
        }
        $tpl = file_get_contents($this->getDirectory().'maker.tpl');
        $ph['buttontype']    = _TOOLBARBUTTONS_BUTTONTYPE;
        $ph['addtags']       = _TOOLBARBUTTONS_ADDTAGS;
        $ph['inserttext']    = _TOOLBARBUTTONS_INSERTTEXT;
        $ph['codebefore']    = _TOOLBARBUTTONS_CODEBEFORE;
        $ph['bothab']        = _TOOLBARBUTTONS_BOTHAB;
        $ph['codeafter']     = _TOOLBARBUTTONS_CODEAFTER;
        $ph['aonly']         = _TOOLBARBUTTONS_AONLY;
        $ph['tip']           = _TOOLBARBUTTONS_TIP;
        $ph['buttoncaption'] = _TOOLBARBUTTONS_BUTTONCAPTION;
        $ph['bothab']        = _TOOLBARBUTTONS_BOTHAB;
        $ph['createcode']    = _TOOLBARBUTTONS_CREATECODE;
        $ph['addbefore']     = _TOOLBARBUTTONS_ADDBEFORE;
        $ph['addafter']      = _TOOLBARBUTTONS_ADDAFTER;
        $maker = $this->parseText($tpl,$ph);
        $maker=str_replace(array("\r","\n"),'',$maker);

        $data['options'][$oid]['extra'] .= '
<script type="text/javascript">
//<![CDATA[
function inserButtons(){
  var tag="";
  var caution = document.getElementById("so");
  if(document.getElementById("buttoncode").value == ""){
    caution.innerHTML = "'._TOOLBARBUTTONS_ERROR_NOCAPTION.'";
    return;
  }
  caution.innerHTML = "";
  if(document.getElementById("btn_type_a").checked){
    tag = tag + "\\t\\t\\t<span class=\"jsbutton\" \\n\\t\\t\\tonmouseover=\"BtnHighlight(this);\" \\n\\t\\t\\tonmouseout=\"BtnNormal(this);\" \\n\\t\\t\\tonclick=\"insertAroundCaret(\'";
    tag = tag + document.getElementById("preadd").value;
    tag = tag + "\',\'";
    tag = tag + document.getElementById("postadd").value;
  }
  if(document.getElementById("btn_type_b").checked){
    tag = tag + "\\t\\t\\t<span class=\"jsbutton\" \\n\\t\\t\\tonmouseover=\"BtnHighlight(this);\" \\n\\t\\t\\tonmouseout=\"BtnNormal(this);\" \\n\\t\\t\\tonclick=\"insertAtCaret(\'";
    tag = tag + document.getElementById("preadd").value;
  }
    tag = tag + "\')\" \\n\\t\\t\\ttitle=\"";
    tag = tag + document.getElementById("inputtitle").value;
    tag = tag + "\">\\n\\t\\t\\t";
    tag = tag + document.getElementById("buttoncode").value;
    tag = tag + "\\n\\t\\t\\t</span>\\n";
  document.getElementById("inputcodes").value += tag;
}
function reflectButtons(lr) {
  elName = ["'.$lbtns.'","'.$rbtns.'"];
  data = document.getElementById("inputcodes").value;
  ElementsList = document.getElementsByName(elName[lr]);
  for (i = 0; i < ElementsList.length; i++) {
    ElementsList[i].value += data;
  }
  document.getElementById("inputcodes").value = "";
}
function helperinit() {
  var htitle = document.getElementsByTagName("h2");
  subhtitle=document.createElement("div");
  subhtitle.style.fontWeight="normal";
  subhtitle.innerHTML = \''.$maker.'\';
  htitle[0].appendChild(subhtitle);
  htitle[0].style.styleFloat = "left";
  htitle[1].style.clear = "left";
  var tables = document.getElementsByTagName("table");
  for (i = 0; i < tables.length; i++) {
    tables[i].style.width = "auto";
  }
}
window.onload = helperinit;
//]]>
</script>';
    }
    function event_PrepareItemForEdit(&$data){ $this->before(); }
    function event_PreAddItemForm(&$data){ $this->before(); }
    var $usefoot=false;
    function event_AdminPrePageHead(&$data){ $this->usefoot=true; }
    function event_AdminPrePageFoot(&$data){ $this->after(); }
    function event_AddItemFormExtras(&$data){ if (!$this->usefoot) $this->after(); }
    function event_EditItemFormExtras(&$data){ if (!$this->usefoot) $this->after(); }
    var $ob_ok=false;
    function before() { $this->ob_ok=ob_start(); }
    function after() {
        global $manager;
        if (!$this->ob_ok) return;
        $buff=ob_get_contents();
        ob_end_clean();
        $lbutton='';
        $rbutton='';
        $script='';
        $pattern='/<div([^>]*?)class="jsbuttonbar"([^>]*?)>/';
        if (preg_match($pattern,$buff,$matches)){
            $params = array('lbutton' => &$lbutton, 'rbutton' => &$rbutton, 'script' => &$script);
            $manager->notify('PreToolbarParse',$params);
            $buff=str_replace($matches[0],$matches[0].$lbutton,$buff);
            $pattern=array('/<\/div>([^<]*?)<textarea([^>]*?)id="inputbody"([^>]*?)>/',
                '/<\/div>([^<]*?)<textarea([^>]*?)id="inputmore"([^>]*?)>/');
            $replace=array('</div><textarea$2id="inputbody"$3>',
                '</div><textarea$2id="inputmore"$3>');
            $buff=preg_replace($pattern,$replace,$buff);
            $pattern='/<\/div><textarea([^>]*?)id="inputbody"([^>]*?)>/';
            if (preg_match($pattern,$buff,$matches)){
                $buff=str_replace($matches[0],$rbutton.$matches[0],$buff);
            }
            $pattern='/<\/div><textarea([^>]*?)id="inputmore"([^>]*?)>/';
            if (preg_match($pattern,$buff,$matches)){
                $buff=str_replace($matches[0],$rbutton.$matches[0],$buff);
            }
        }
        echo $buff.$script;
    }
    function event_PreToolbarParse(&$data) {
        global $CONF;
        $lbutton=&$data['lbutton'];
        $rbutton=&$data['rbutton'];
        $script=&$data['script'];
        
        $setOptionURL = $CONF['AdminURL'] . 'index.php?action=pluginoptions&amp;plugid=' . $this->getID();

        // Left buttons
        $lbutton.='
<div style="padding-top:4px;padding-bottom:4px;margin-bottom:1px;">
'.$this->getOption('lbtns').'
</div>
<div style="padding-top:4px;padding-bottom:4px;">'."\n";

        // Right buttons
        $rbutton.='
</div>
<div style="padding-top:4px;padding-bottom:4px;margin-top:1px;">
'.$this->getOption('rbtns')."
\t\t    <span class=\"jsbutton\"
\t\t    onmouseover=\"BtnHighlight(this);\"
\t\t    onmouseout=\"BtnNormal(this);\"
\t\t    onclick=\"entitiesCaret()\"
\t\t    title=\"toEntities\" >
\t\t    &amp;lt;
\t\t    </span>".'
<a href="'.$setOptionURL.'">Edit Buttons</a>
</div>'."\n";

        // Additional scripts
        $script.= '
<script type="text/javascript">
//<![CDATA[
'.$this->getOption('addscripts').'
function entitiesCaret () {
  var textEl = lastSelected;
  if (textEl && textEl.createTextRange && lastCaretPos) {
    var caretPos = lastCaretPos;
    caretPos.text = caretPos.text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\\"/g, "&quot;");
  } else if (!document.all && document.getElementById) {
    newText = mozSelectedText().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\\"/g, "&quot;");
    mozReplace(document.getElementById("input" + nonie_FormType), newText);
  }
  updAllPreviews();
}
//]]>
</script>';
    }
    function init(){
        // include language file for this plugin
        $language = $this->getDirectory().str_replace( array('\\','/'), '', getLanguageName()).'.php';
        if (file_exists($language)) include_once($language);
        else include_once($this->getDirectory().'english.php');
    }
    
    function parseText($tpl, $ph) {
        foreach($ph as $k=>$v) {
            $tpl = str_replace("<%{$k}%>", $v, $tpl);
        }
        return $tpl;
    }
}
