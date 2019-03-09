<?php

$openid = $_POST["openid"];
$form_id = $_POST["prepay_id"];
 
 //配置自动获取你懂的值,在规定时间内,直接调用即可
$json_token=http_request("getAccessToken.php");
//echo $access_token;
//模板消息,自行配置
$name = $_POST['name'];
$phone = $_POST['phone'];
$money = $_POST['money'];
$address = $_POST['address'];
$template=array(
        'touser'=>$openid,
        'form_id'=>$form_id,
        'template_id'=>"Em1WImhQYeDi5o5QOoTWRYMJEw71tDg7OQ2POb3NyBI",
        'data'=>array(
                'keyword1'=>array('value'=>urlencode($name),'color'=>'#FF0000'),
                'keyword2'=>array('value'=>urlencode($phone),'color'=>'#FF0000'),
                'keyword3'=>array('value'=>urlencode($money),'color'=>'#FF0000'),
                'keyword4'=>array('value'=>urlencode($address),'color'=>'#FF0000')
                )
        );
$json_template=json_encode($template);
$url="https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$json_token;
$res=http_request($url,urldecode($json_template));
$json_obj = json_decode($res,true);
$json_obj['form_id'] = $form_id;
echo json_encode($json_obj);
 
 
function http_request($url,$data=array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // POST数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // 把post的变量加上
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}