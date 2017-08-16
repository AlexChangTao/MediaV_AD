<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 接口调用控制器
 */

class Curl extends CI_Controller {
//echo "<pre>"; 
function curl_request($url, $data) {
   $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $response       = curl_exec($ch);
    //var_dump(curl_error($ch), curl_errno($ch), curl_getinfo($ch));die;
    curl_close($ch);
    return $response;
}

public function index() {
    $data = array(
            'activity_id' => 1,
            'ip'          => '2.2.2.2',
            'user_id'     => 'name'

    );

    $entry          = $this->curl_request('http://dev.cindy.com/open_api/get_lottery?activity_id=1', $data);
    var_dump($entry);die;
}

}