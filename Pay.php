<?php
header("Content-Type:application/json; charset=utf-8");
    include 'WeixinPay.php';  
    $appid='';  //小程序appid
    $openid= $_GET['openid'];  
$attach = $_GET['attach'];
    $mch_id='';  //商户id
    $key='';  //商户key
    $out_trade_no = $mch_id. time();  
    $total_fee = $_GET['total_fee'];  
    $body= $_GET['body'];
    if(empty($total_fee)){ 
        $body = $body;  
        $total_fee = floatval(99*100);  
    }else{  
        $body = $body;  
        $total_fee = floatval($total_fee*100);  
    }  
    $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$attach);  
    $return=$weixinpay->pay();  
    echo json_encode($return);  