<?php   
include "app_config.php";
date_default_timezone_set('PRC');
$str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        echo $str;
$times = date("Y-m-d H:i:s",time());
    //获取接口数据，如果$_REQUEST拿不到数据，则使用file_get_contents函数获取
    $post = $_REQUEST;
    if ($post == null) {
        $post = file_get_contents("php://input");
    }
 
    if ($post == null) {
        $post = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
    }
 
    if (empty($post) || $post == null || $post == '') {
        //阻止微信接口反复回调接口 
        $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        echo $str;
        exit('Notify 非法回调');
    }  
 
    libxml_disable_entity_loader(true); //禁止引用外部xml实体
 
    $xml = simplexml_load_string($post, 'SimpleXMLElement', LIBXML_NOCDATA);//XML转数组
    
    $post_data = (array)$xml;
    
    /** 解析出来的数组
        *Array
        * (
        * [appid] => wx1c870c0145984d30
        * [bank_type] => CFT
        * [cash_fee] => 100
        * [fee_type] => CNY
        * [is_subscribe] => N
        * [mch_id] => 1297210301
        * [nonce_str] => gkq1x5fxejqo5lz5eua50gg4c4la18vy
        * [openid] => olSGW5BBvfep9UhlU40VFIQlcvZ0
        * [out_trade_no] => fangchan_588796
        * [result_code] => SUCCESS
        * [return_code] => SUCCESS
        * [sign] => F6890323B0A6A3765510D152D9420EAC
        * [time_end] => 20180626170839
        * [total_fee] => 100
        * [trade_type] => JSAPI
        * [transaction_id] => 4200000134201806265483331660
        * )
    **/
    //订单号
    $out_trade_no = isset($post_data['out_trade_no']) && !empty($post_data['out_trade_no']) ? $post_data['out_trade_no'] : 0;



    $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    $txt = "商户订单号:".$out_trade_no."\n交易时间:".$post_data['time_end']
    ."\n微信订单号:".$post_data['transaction_id']."\n订单金额:".($post_data['total_fee']/100).
    "$\n用户ID:".$post_data['openid']."用户数据:".$post_data['attach'];
    fwrite($myfile, $txt);
    fclose($myfile);

        $dataArray = explode("(LINE)", $post_data['attach']);
        // 查询商户订单号是否存在，如果存在则进行插入

        // 若商户订单号不存在进行插入
        $sel_sql = "SELECT * FROM lx_xiasongform WHERE `data_order`='$out_trade_no'";
        $sel_res = mysql_query($sel_sql);
        if($sel_res){
            // 执行成功
            if(mysql_num_rows($sel_res) > 0){
                // 订单号存在，什么都不执行
            }else{
                $item01 = $dataArray[0];
                $item02 = $dataArray[1];
                $item03 = $dataArray[2];
                $item04 = $dataArray[3];
                $item05 = $dataArray[4];
                $item06 = $dataArray[5];
                $item07 = $dataArray[6];
                if(strlen($item01) > 1 && strlen($item02) > 1 && strlen($item03) > 1 && strlen($item04) > 1 && strlen($item05) > 1 && strlen($item06) > 1 && strlen($item07) > 10){
                    $dingdan = $post_data['out_trade_no'];
                    $insert_sql = "INSERT INTO lx_xiasongform (data_lou,data_lou_num,data_kuaidi_type,data_kuaidi_num,data_name,data_phone,data_order,data_time,data_uid) VALUES ('$item01','$item02','$item03','$item04','$item05','$item06','$dingdan','$times','$item07')";
                    mysql_query($insert_sql);
                    mysql_close($con);
                    $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    echo $str;
                }
                    
            }
        }
    //阻止微信接口反复回调接口  文档地址 https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_7&index=7，下面这句非常重要!!!
    $str='<xml>O<return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[K]]></return_msg></xml>';  
    echo $str;
    function MakeSign($params,$key){
        //签名步骤一：按字典序排序数组参数
        ksort($params);
        $string = ToUrlParams($params);  //参数进行拼接key=value&k=v
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
 
    function ToUrlParams( $params ){
        $string = '';
        if( !empty($params) ){
            $array = array();
            foreach( $params as $key => $value ){
                $array[] = $key.'='.$value;
            }
            $string = implode("&",$array);
        }
        return $string;
}