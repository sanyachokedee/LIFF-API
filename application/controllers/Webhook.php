<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('LINE_ACCESS_TOKEN', 'J8CWaFI9cFH5bRHYr7g8mDH4efVjh73l3heihWuGCYmptVVZLAc53lzZEjYK2mK/XqtTmvQF4Dp0ZDxYG454IYZZQnLqqdLbxf1sXWFVgeDiL+JsMqobpIdiiW0dj4FVL/mdGlYo+6nvJJU9W1kQ8wdB04t89/1O/w1cDnyilFU=');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Webhook extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->helper('url');
  }

  public function index()
  {
    // echo "start";
    $content = file_get_contents('php://input');
    $events = json_decode($content, true);
    if(!is_null($events)){
      $reply_token = $events['events'][0]['replyToken'];
      $messages[] = [
        'type' => 'text',
        'text' => $content
      ];
      $this->reply($reply_token, $messages);
    }
  }

  public function reply($reply_token, $messages)
  {
    $url = 'https://api.line.me/v2/bot/message/reply';

    $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . LINE_ACCESS_TOKEN
    ];

    $body = json_encode([
      'replyToken' => $reply_token,
      'messages' => $messages,
    ]);
    // post json with curl
    $options = [
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body
    ];

    $res = 'No run curl';
    if (!empty($messages)) {
      $curl = curl_init();
      curl_setopt_array($curl, $options);
      $res = curl_exec($curl);
      curl_close($curl);
    }
  }

  public function push($reply_token, $messages)
  {
    $url = 'https://api.line.me/v2/bot/message/push';

    $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . LINE_ACCESS_TOKEN
    ];

    $body = json_encode([
      'replyToken' => $reply_token,
      'messages' => $messages,
    ]);
    // post json with curl
    $options = [
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body,
      CURLOPT_SSL_VERIFYHOST,
      CURLOPT_SSL_VERIFYPEER
    ];

    $res = 'No run curl';
    if (!empty($messages)) {
      $curl = curl_init();
      curl_setopt_array($curl, $options);
      $res = curl_exec($curl);
      curl_close($curl);
    }
  }
}
