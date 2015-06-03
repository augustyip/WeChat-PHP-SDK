<?php
/**
 * WeChat PHP SDK
 *
 * @author     August Yip <augustyip@gmail.com>
 * @link       https://github.com/augustyip/WeChat-PHP-SDK
 * @license    MIT License
 */

class WeChat {
  static $token = 'token';

  static function check_signature() {

    if ( ! (isset($_GET['signature']) && isset($_GET['timestamp']) && isset($_GET['nonce']))) {
      return FALSE;
    }

    $signature = $_GET['signature'];
    $timestamp = $_GET['timestamp'];
    $nonce     = $_GET['nonce'];
    $token     = self::$token;

    $signature_array = array($token, $timestamp, $nonce);
    sort($signature_array);

    return sha1(implode($signature_array)) == $signature;
  }

  static function valid() {
    if (self::check_signature()) {
      if (isset($_GET['echostr'])) {
        exit($_GET['echostr']);
      }
      return TRUE;
    }
    return FALSE;
  }

  static function get_request() {

    if (self::valid() && isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
      return (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    return FALSE;
  }

  static function send_response() {
    
  }

}
