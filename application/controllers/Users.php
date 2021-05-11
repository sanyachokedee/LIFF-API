<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('LINE_ACCESS_TOKEN', 'J8CWaFI9cFH5bRHYr7g8mDH4efVjh73l3heihWuGCYmptVVZLAc53lzZEjYK2mK/XqtTmvQF4Dp0ZDxYG454IYZZQnLqqdLbxf1sXWFVgeDiL+JsMqobpIdiiW0dj4FVL/mdGlYo+6nvJJU9W1kQ8wdB04t89/1O/w1cDnyilFU=');

class Users extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('users_model');
  }

  // เอาไว้ทดสอบถ้าเรียกมาแล้วให้แสดง welcome_message
  public function index()
  {
    $this->load->view('welcome_message');
  }

  //เมื่อ user เข้ามาจได้ส่งผ่าน $uid 
  public function verify($uid)
  {
    // echo $uid;
    $query = $this->users_model->get_user_from_uid($uid); //ทำการเช็คในฐานข้อมูลว่ามี user นี้หรือเปล่า
    // print_r($query->num_rows());
    if ($query->num_rows() > 0) {
      $result = $query->result();
      $response = array(
        'status' => 'success',
        'data' => array(
          'users_id' => $result[0]->users_id,
          'fullname' => $result[0]->fullname,
          'email' => $result[0]->email,
          'mobile' => $result[0]->mobile,
          'uid' => $result[0]->uid
        )
      );
    } else {
      $response = array(
        'status' => 'fail',
        'message' => 'no data'
      );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
  }

  public function register()
  {
    $content = file_get_contents('php://input');
    $data = json_decode($content, true);
    if (!is_null($data)) {
      //ดูว่ามีข้อมูลมาด้วยหรือเปล่าถ้ามีให้บันทึก เสร็จคือค่า success ถ้าไม่ได้ ก็ คืน false
      //โดยทั้งหมดจะส่งแบบ json 
      if (isset($data['name']) && isset($data['email']) && isset($data['mobile']) && isset($data['uid'])) {
        $query = $this->users_model->add_user($data['name'], $data['email'], $data['mobile'], $data['uid']);
        if ($query) {
          $response = array(
            'status' => 'success',
            'message' => 'Add data success'
          );
          $this->change_richmenu($data['uid']);
        } else {
          $response = array(
            'status' => 'fail',
            'message' => 'Invalid data'
          );
        }
      } else {
        $response = array(
          'status' => 'fail',
          'message' => 'Invalid data'
        );
      }
    } else {
      $response = array(
        'status' => 'fail',
        'message' => 'Invalid data'
      );
    }
    // echo $body['name'];
    //add the header here
    header('Content-Type: application/json');
    echo json_encode($response);
  }

  private function change_richmenu($uid)
  {
    // $richMenuId = "richmenu-438da3f79f80b5d729af408908b2fd55";
    $richMenuId = "richmenu-12f8f9d4ba63a3c11095741745b25102";
    $url = 'https://api.line.me/v2/bot/user/' . $uid . '/richmenu/' . $richMenuId;

    $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . LINE_ACCESS_TOKEN
    ];

    $body = [];

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body,
      CURLOPT_SSL_VERIFYHOST,
      CURLOPT_SSL_VERIFYPEER
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $res = curl_exec($curl);
    curl_close($curl);

    // header('Content-Type: application/json');
    // print_r($res);
  }

  public function unlink_richmenu($uid)
  {
    $url = 'https://api.line.me/v2/bot/user/' . $uid . '/richmenu/';

    $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . LINE_ACCESS_TOKEN
    ];

    $body = [];

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_CUSTOMREQUEST => 'DELETE',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body,
      CURLOPT_SSL_VERIFYHOST,
      CURLOPT_SSL_VERIFYPEER
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $res = curl_exec($curl);
    curl_close($curl);

    // header('Content-Type: application/json');
    // print_r($res);
  }
}
