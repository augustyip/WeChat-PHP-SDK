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

  /**
   * Validate signature.
   * 
   * @return boolean
   */
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

  /**
   * Frist time access validate().
   * 
   * @return boolean
   */
  static function valid() {
    if (self::check_signature()) {
      if (isset($_GET['echostr'])) {
        exit($_GET['echostr']);
      }
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get request message.
   * 
   * @return array
   */
  static function get_request() {

    if (self::valid() && isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
      return (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    return FALSE;
  }

  /**
   * Generate the xml for response.
   * 
   * @param  array $msg
   * @return string
   */
  static function generate_response_xml($msg) {

    $response = '<xml>';
    self::generate_response_xml_items($msg, $response);
    return $response .= '</xml>';
  }

  /**
   * Generate the xml items.
   * 
   * @param  array $items
   * @param  string &$response
   * @return string
   */
  static function generate_response_xml_items($items, &$response) {

    foreach ($items as $key => $value) {
      if (is_array($value)) {
        $response .= '<' . $key . '>';
        self::generate_response_xml_items($value, $response);
        $response .= '</' . $key . '>';
      }
      else {
        $response .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
      }
    }
  }

  /**
   * Send response.
   * @param  array $msg
   * @param  array $request
   */
  static function send_response($msg, $request = array()) {

    $request = empty($request) ? self::get_request() : $request;

    $default = array(
      'ToUserName'   => $request['FromUserName'],
      'FromUserName' => $request['ToUserName'],
      'CreateTime' => time(),
      'MsgType' => 'text',
    );

    $msg += $default;

    print self::generate_response_xml($msg);
  }

}
