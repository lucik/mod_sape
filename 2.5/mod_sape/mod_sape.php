<?php
/*
(c) Andrew J. Lutsenko, http://blog.yousoft.ru, http://soft.vomske.ru
*/

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$sp_sape_user         = trim($params->get( 'sp_sape_user' ));
$sp_fetch_remote_type = trim($params->get( 'sp_fetch_remote_type' ));
$sp_charset           = trim($params->get( 'sp_charset' ));
$sp_redirect_url      = trim($params->get( 'sp_redirect_url' ));
$sp_show_header       = trim($params->get( 'sp_show_header' ));
$sp_debug_mode        = trim($params->get( 'sp_debug_mode' ));
$sp_link_count        = trim($params->get( 'sp_link_count' ));

$sp_force_show_code   = trim($params->get( 'sp_force_show_code' ));
$sp_verbose           = trim($params->get( 'sp_verbose' ));
$sp_debug             = trim($params->get( 'sp_debug' ));
$sp_show_nolinks      = trim($params->get( 'sp_show_nolinks' ));
$sp_nolinks           = trim($params->get( 'sp_nolinks' ));

$moduleclass_sfx      = $params->get( 'moduleclass_sfx' );
$sp_hide_empty	      = trim($params->get( 'sp_hide_empty' ));
$sp_show_host         = trim($params->get( 'sp_show_host' ));
$sp_show_host_text    = trim($params->get( 'sp_show_host_text' ));
$sp_show_document_root= trim($params->get( 'sp_show_document_root' ));

if (!empty($sp_show_document_root)){
  $_SERVER['DOCUMENT_ROOT']=$sp_show_document_root;
}

//$sp_server= $params->get( 'sp_server' );

$out_text='';

if (!defined('MOD_SAPE_USER')){
  unset($sape_option);
  switch ($sp_fetch_remote_type) {
    case "1" :
      $sape_option['fetch_remote_type']= 'file_get_contents'; 
    break;
    case "2" :
      $sape_option['fetch_remote_type']= 'curl'; 
    break;
    case "3" :
      $sape_option['fetch_remote_type']= 'socket'; 
    break;
  }
  
  switch ($sp_charset) {
    case "1" :
      $sape_option['charset']= 'CP1251'; 
    break;
    case "2" :
      $sape_option['charset']= 'UTF-8'; 
    break;
  }

  if ($sp_force_show_code==='2'){
    $sape_option['force_show_code'] = true;
  }
  if ($sp_verbose==='2'){
    $sape_option['verbose'] = true;
  }
  if ($sp_debug==='2'){
    $sape_option['debug'] = true;
  }

  if ($sp_show_host==='1' and isset($sp_show_host_text)){
    $sape_option['host'] = $sp_show_host_text;
  }

  if ($sp_redirect_url==='1' and isset($_SERVER['REDIRECT_URL'])){
    $sape_option['request_uri'] = $_SERVER['REDIRECT_URL'];
  }

  if ($sp_redirect_url==='2' and isset($_SERVER['SCRIPT_URL'])){
    $sape_option['request_uri'] = $_SERVER['SCRIPT_URL'];
  }

  //getenv('REQUEST_URI')
  if ($sp_redirect_url==='3' and !is_null(getenv('REQUEST_URI'))){
    $sape_option['request_uri'] = getenv('REQUEST_URI');
  }

  if ($sp_redirect_url==='4' and isset($_SERVER['HTTP_X_REWRITE_URL'])){
    $sape_option['request_uri'] = $_SERVER['HTTP_X_REWRITE_URL'];
  }

  
  global $mod_sape_return_links;
  global $mod_sape_debug_count;
  $mod_sape_debug_count=1;
  
  if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$sp_sape_user.'/sape.php')){
    $out_text.="<h1>Внимание!!! Файл sape.php не обнаружен. Проверьте правильность установки кода Sape.</h1>";
  } else {

	define('MOD_SAPE_USER', $sp_sape_user);
	if (!defined('_SAPE_USER')){
		define('_SAPE_USER', $sp_sape_user);
	}

    require_once($_SERVER['DOCUMENT_ROOT'].'/'._SAPE_USER.'/sape.php');

    if (isset($sape_option)) {
      $mod_sape_return_links = new SAPE_client($sape_option);
    } else{
      $mod_sape_return_links = new SAPE_client();
    }
  }
}else{
  global $mod_sape_return_links;
  global $mod_sape_debug_count;
  $mod_sape_debug_count++;
}

if ($sp_debug_mode==='1' or $sp_debug_mode==='2' or $sp_debug_mode==='3'){
  if ($sp_link_count==='0'){
    $sp_link_count_st='Все';
  } else{
    $sp_link_count_st=$sp_link_count;
  }
  $out_text.="<h1>Отладка Модуль №$mod_sape_debug_count Ссылок: $sp_link_count_st</h1>";
  $out_text.='<p align="left">Проверка серверных путей: </p>';
  $out_text.='<hr />';
  if (isset($sape_option['request_uri'])) {
    $out_text.='<p align="left">Request_uri сформирован и передается в модуль sape: <b>'.$sape_option['request_uri'].'</b></p><hr />';
  }
  if (isset($_SERVER['REQUEST_URI'])) {
    $out_text.='<p align="left">$_SERVER[\'REQUEST_URI\'] сформирован: <b>'.$_SERVER['REQUEST_URI'].'</b></p><hr />';
  }
  if (isset($_SERVER['REDIRECT_URL'])) {
    $out_text.='<p align="left">$_SERVER[\'REDIRECT_URL\'] сформирован: <b>'.$_SERVER['REDIRECT_URL'].'</b></p><hr />';
  }
  if (isset($_SERVER['SCRIPT_URL'])) {
    $out_text.='<p align="left">$_SERVER[\'SCRIPT_URL\'] сформирован: <b>'.$_SERVER['SCRIPT_URL'].'</b></p><hr />';
  }
  if (!is_null(getenv('REQUEST_URI'))) {
    $out_text.='<p align="left">getenv(\'REQUEST_URI\') сформирован: <b>'.getenv('REQUEST_URI').'</b></p><hr />';
  }
  if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
    $out_text.='<p align="left">$_SERVER[\'HTTP_X_REWRITE_URL\'] сформирован: <b>'.$_SERVER['HTTP_X_REWRITE_URL'].'</b></p><hr />';
  }

  if ($sp_debug_mode==='2' or $sp_debug_mode==='3'){
    $sape_host='dispenser-01.sape.ru';
    $path='/code.php?user='._SAPE_USER.'&host=' . $_SERVER['HTTP_HOST'];
    $user_agent='mod_sape Testing (http://soft.vomske.ru)';
    $out_text.='<p align="left">Функция file_get_contents: <b>'.(function_exists('file_get_contents')==1?'Да':'Нет').'</b></p>';
    $out_text.='<p align="left">Функция file_get_contents allow_url_fopen: <b>'.(ini_get('allow_url_fopen')==1?'Да':'Нет').'</b></p>';
    if (function_exists('file_get_contents')==1){
      $data = @file_get_contents('http://'.$sape_host.$path);
      $out_text.='<p align="left">Проверка загрузки через file_get_contents: <b>'.($data?'Да':'Нет').' - '.strlen($data).'</b></p>';
    }
    $out_text.='<hr />';

    $out_text.='<p align="left">Функция curl_init: <b>'.(function_exists('curl_init')==1?'Да':'Нет').'</b></p>';
    if (function_exists('curl_init')==1){
      $data='';
      if ($ch = @curl_init()) {
        @curl_setopt($ch, CURLOPT_URL,              'http://' . $sape_host . $path);
        @curl_setopt($ch, CURLOPT_HEADER,           false);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER,   true);
        @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,   30);
        @curl_setopt($ch, CURLOPT_USERAGENT,        $user_agent);
        $data = @curl_exec($ch);
        @curl_close($ch);
      }
      $out_text.='<p align="left">Проверка загрузки через curl_init: <b>'.($data?'Да':'Нет').' - '.strlen($data).'</b></p>';
    }
    $out_text.='<hr />';

    $out_text.='<p align="left">Функция fsockopen: <b>'.(function_exists('fsockopen')==1?'Да':'Нет').'</b></p>';
    if (function_exists('fsockopen')==1){
      $data='';
      $buff = '';
      $fp = @fsockopen($sape_host, 80, $errno, $errstr, 30);
      if ($fp) {
        @fputs($fp, "GET {$path} HTTP/1.0\r\nHost: {$sape_host}\r\n");
        @fputs($fp, "User-Agent: {$user_agent}\r\n\r\n");
        while (!@feof($fp)) {
          $buff .= @fgets($fp, 128);
        }
        @fclose($fp);
        $page = explode("\r\n\r\n", $buff);
        $data=$page[1];
      }
      $out_text.='<p align="left">Проверка загрузки через fsockopen: <b>'.($data?'Да':'Нет').' - '.strlen($data).'</b></p>';
    }
    $out_text.='<hr />';
  }
  if ($sp_debug_mode==='3'){
    $out_text.='<pre>';
    $out_text.='_ENV '.print_r($_ENV,true);
    $out_text.='_SERVER '.print_r($_SERVER,true);
    $out_text.='</pre>';
  }

}
  unset($sape_option);

if (defined('MOD_SAPE_USER')){
	if ($sp_link_count==='0'){
		$echo_link=$mod_sape_return_links->return_links(); 
	} else{
		$echo_link=$mod_sape_return_links->return_links($sp_link_count); 
	}
}

if (($sp_hide_empty==='1') and preg_match("/^<!--.+-->$/",$echo_link)){
  $echo_link='';
}

if ($sp_show_nolinks==='1' and ($echo_link=='' or preg_match("/^<!--.+-->$/",$echo_link))){
  $echo_link.=$sp_nolinks;
}

if ($sp_charset===1){
  $out_text=iconv('utf-8', 'cp1251', $out_text);
}

if ($sp_show_header==='1'){
  if (preg_match("/^<!--.+-->$/",$echo_link) or trim($echo_link)===''){
    $out_text.=$echo_link;	
  } else{
    $out_text.='<table cellpadding="0" cellspacing="0" class="moduletable'.$moduleclass_sfx.'"><tr><th valign="top">'.$module->title.'</th></tr>';
    $out_text.='<tr><td>'.$echo_link.'</tr></td></table>';	
  }
}
else
{
  $out_text.=$echo_link;	
}

echo $out_text;
?>
