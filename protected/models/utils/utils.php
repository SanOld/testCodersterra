<?php

/**
 * Project:     Generic PHP framework
 * Author:      Sergiy Lavryk (jagermesh@gmail.com)
 *
 * @version 1.1.0.0
 * @package Generic
 */

function array_change_case($params,$case=CASE_UPPER) {
  $params = array_change_key_case($params, $case);
  foreach ( $params as &$value ) {
    if (is_scalar($value)) {
      if ($case == CASE_UPPER){
        $value = strtoupper($value);
      } else {
        $value = strtolower($value);
      }
    } elseif (is_array($value)) {
        if ($case == CASE_UPPER) {
          $value = array_map("strtoupper", $value);
        } else {
          $value = array_map("strtolower", $value); 
        }
    }
  }
  unset($value);
  return $params;
}

function tokenGenerate($length)
{
  $max = ceil($length / 32);
  $random = '';
  for ($i = 0; $i < $max; $i ++) {
    $random .= md5(microtime(true).mt_rand(10000,90000));
  }
  return substr($random, 0, $length);
}

function get($name, $default = null) { 
  
  if (isset($_GET[$name])) {
    if (is_array($_GET[$name])) {
      $res_array = array();
      foreach($_GET[$name] as $key => $value) {
        $res_array[$key] = trim($value);
      }
      return $res_array;
    } else {
      return trim($_GET[$name]);
      //return urldecode($_GET[$name]);
    }
  } else {
    return $default;
  }
  
}
function set_get($name, $value = null) { $_GET[$name] = $value; }
function session($name, $default = null) { return isset($_SESSION[$name]) ? $_SESSION[$name] : $default; }
function cookie($name, $default = null) { return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default; }
function post($name, $default = null) { return isset($_POST[$name]) ? $_POST[$name] : $default; }
function set_post($name, $value = null) { $_POST[$name] = $value; }
function get_const($name, $default = null) { return defined($name) ? constant($name) : $default; }

function request($name, $default = null) {
  if(isset($_GET[$name])) return get($name, $default);
  return post($name, $default);
}

function is_post_exists($tag) {
  
  foreach($_POST as $key => $value)
    if (preg_match('~'.$tag.'~i', $key)) 
      return true;
  return false;
  
}

function safe($array, $name, $default = null) { 

  return (is_array($array) && strlen($name) && array_key_exists($name, $array) && ($array[$name] || (is_scalar($array[$name]) and strlen($array[$name])))) ? $array[$name] : $default;
  
}

function mk_dir($path, $mode = 0) {

  if (is_dir($path))
    return true;

  $npath = dirname($path);
  if (!mk_dir($npath, $mode))
    return false;

  return @mkdir($path);

}

?>