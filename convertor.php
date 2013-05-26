<?php
class HTML
{
    function generic($name=NULL,$class=NULL,$delete_name=FALSE){
        return ((isset($class))?(' id="'.$class.'"'):'').
                ((isset($class))?(' class="'.$class.'"'):'').
                (
                    (isset($name) and isset($class))
                    ?(
                        ' name="'.$name.
                        ((!$delete_name)?'['.$class.']"':'"')
                    )
                    :''
                );
    }
    function html($close=TRUE){
        return (($close)?'<html>':'</html>').PHP_EOL;
    }
    function head($close=TRUE){
        return (($close)?'<head>':'</head>').PHP_EOL;
    }
    function body($close=TRUE){
        return (($close)?'<body>':'</body>').PHP_EOL;
    }
    function p($close=TRUE,$class=NULL){
        return (($close)?'<p'.
                ((isset($class))?(' class="'.$class.'"'):'').
                ((isset($class))?(' id="'.$class.'"'):'').
                '>':'</p>').PHP_EOL;
    }
    function inputText($input=NULL,$name=NULL,$class=NULL){
        return '<input type="text" '.
                $this->generic($name,'text-'.$class).
                ((isset($input))?' value="'.$input.'"':'').
                '>'.PHP_EOL;
    }
    function inputSubmit($name=NULL,$class=NULL, $value='Submit'){
        return '<input onclick="start(); return false;" type="submit" value="'.$value.'"'.
                $this->generic($name,'submit-'.$class).
                '>'.PHP_EOL;
    }
    function select($options=array(),$selected='', $name=NULL, $class=NULL){
        $return = '<select'.
                    $this->generic($name,$class).
                    '>'.PHP_EOL;
        foreach($options as $key=>$item){
            if($key==$selected){
                $return .= '<option value="'.$key.'" selected="selected">'.$key.'</option>'.PHP_EOL;
            }else{
                $return .= '<option value="'.$key.'">'.$key.'</option>'.PHP_EOL;
            }
        }
        $return .= '</select>'.PHP_EOL;
        return $return;
    }
    function table($close=TRUE){
        return (($close)?'<table>':'</table>').PHP_EOL;
    }
    function tableTR($close=TRUE){
        return (($close)?'<tr>':'</tr>').PHP_EOL;
    }
    function tableTD($close=TRUE,$colspan=NULL){
        return (($close)?'<td'.(($colspan!==NULL && is_int($colspan))?' colspan='.$colspan:'').'>':'</td>').PHP_EOL;
    }
    function formOpen($name=NULL,$class=NULL,$action='',$method='post',$enctype='application/x-www-form-urlencoded'){
        return '<form'.
                $this->generic($name,$class,TRUE).
                '>'.PHP_EOL;
    }
    function formClose(){
        return '</form>'.PHP_EOL;
    }
    function getJS(){
        return <<<END_J
<script type="text/javascript">
//<![CDATA[
var req;
function createRequestObject(){
  if (typeof XMLHttpRequest === 'undefined') {
    XMLHttpRequest = function() {
      try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); }
        catch(e) {}
      try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); }
        catch(e) {}
      try { return new ActiveXObject("Msxml2.XMLHTTP"); }
        catch(e) {}
      try { return new ActiveXObject("Microsoft.XMLHTTP"); }
        catch(e) {}
      throw new Error("This browser does not support XMLHttpRequest.");
    };
  }
  return new XMLHttpRequest();
}
function start(){
    req = createRequestObject();
    if (req){
        var params = 'text-test=' + encodeURIComponent(document.getElementById('text-test').value)
                +'&select_1=' + encodeURIComponent(document.getElementById('select_1').value)
                +'&select_2=' + encodeURIComponent(document.getElementById('select_2').value);
        req.open("POST", '/convertor.php', true);
        req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        req.onreadystatechange = processReqChange;
        req.send(params);
    }
}
function processReqChange(){
  try {
    if (req.readyState == 4) {
        if (req.status == 200) {
            document.getElementById('test').innerHTML = req.responseText;
        }else{}
    }
  }
  catch( e ) {}
}
//]]>
</script>
END_J;
    }
}
class XML
{
    var $href="http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml";
    
    var $xml=array('EUR'=>'1');
    
    function get(){
        $lines=$this->openUrl();
        $vals=$this->parseXML($lines);
        $array=array(''=>'','EUR'=>'1');
        foreach($vals as $item){
            if(isset($item['attributes']['CURRENCY']) and isset($item['attributes']['RATE'])){
                $this->xml[$item['attributes']['CURRENCY']]=$item['attributes']['RATE'];
            }
        }
        return $this->xml;
    }
    function openUrl($href=NULL){
        if($href==NULL){
            if(NULL!==$this->href){
                $href=$this->href;
            }else{
                return NULL;
            }
        }
        $handle = fopen($href, "r");
        $openUrl = fread($handle,4000);
        fclose($handle);
        return $openUrl;
    }
    function parseXML($lines=NULL){
        if($lines==NULL){$lines=$this->openUrl();}
        $p = xml_parser_create();
        xml_parse_into_struct($p, $lines, $vals, $index);
        xml_parser_free($p);
        return $vals;
    }
}
class Render
{
    function basicform($input=NULL,$array=NULL,$select_1='EUR',$select_2='USD',$text=NULL){
        if(!isset($array)){
            $xml=new XML;
            $array=$xml->get();
        }
        $html=new HTML;
        $echo = $html->formOpen('qwerty','test');
        $echo .= $html->table();
        $echo .= $html->tableTR();
        $echo .= $html->tableTD();
        $echo .= $html->inputText($input,'qwerty','test');
        $echo .= $html->tableTD(false);
        $echo .= $html->tableTD();
        $echo .= $html->select($array,$select_1,'qwerty','select_1');
        $echo .= $html->tableTD(false);
        $echo .= $html->tableTD();
        $echo .= 'to';
        $echo .= $html->tableTD(false);
        $echo .= $html->tableTD();
        $echo .= $html->select($array,$select_2,'qwerty','select_2');
        $echo .= $html->tableTD(false);
        $echo .= $html->tableTD();
        $echo .= $html->inputSubmit('qwerty','test','Convert');
        $echo .= $html->tableTD(false);
        $echo .= $html->tableTR(false);
        $echo .= $html->tableTR();
        $echo .= $html->tableTD(TRUE,5);
        $echo .= $html->p(TRUE,'qwerty_p');
        $echo .= ((isset($text))?$text:'').PHP_EOL;
        $echo .= $html->p(false);
        $echo .= $html->tableTD(false);
        $echo .= $html->tableTR(false);
        $echo .= $html->table(false);
        $echo .= $html->formClose();
        return $echo;
    }
}

$xml=new XML;
$array=$xml->get();
$render=new Render;
if($_SERVER["REQUEST_METHOD"]=='POST'){
    if(is_numeric($_POST["text-test"])){
        $text=round($_POST["text-test"],2).' '.$_POST["select_1"].' = '.
                round(($_POST["text-test"]*$array[$_POST["select_2"]]/$array[$_POST["select_1"]]),2).
                ' '.$_POST["select_2"];
    }else{
        $text='Invalid amount. Please enter a number.';
    }
    echo $render->basicform($_POST["text-test"], $array, $_POST["select_1"],$_POST["select_2"],$text);
}else{
    $html=new HTML;
    echo $html->html();
    echo $html->head();
    echo $html->getJS();
    echo $html->head(false);
    echo $html->body();
    echo $render->basicform('0',$array,'EUR','USD');
    echo $html->body(false);
    echo $html->html(false);
}