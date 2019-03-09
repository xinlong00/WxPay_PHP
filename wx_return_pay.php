<?php
include "wechatAppPay.php";
$wechatAppPay = new wechatAppPay("小程序ID", "商户号码", "回调", 

"商户KEY");

//接收订单号码
@$order = $_GET['order'];
//传入退款金额
@$fee = $_GET['fee'];

//小程序的appid
$param['appid'] = "小程序ID";
//商户号
$param['mch_id'] = "商户号码";
//随机字符串
$nonce_str = createNoncestr(15);//随机数生成
$param['nonce_str'] = $nonce_str;
//商户订单号
$param['out_trade_no'] = $order;
//商户退款单号
$out_refund_no = createNoncestr(15);//生成随机数
$param['out_refund_no'] = $out_refund_no;
//订单金额
$param['total_fee'] = $fee * 100;
//退款金额
$param['refund_fee'] =$fee * 100;
//退款原因
$param['refund_desc'] = '这是退款的时候的原因';
$stringSignTemp = $wechatAppPay->MakeSign($param);
$param['sign'] = $stringSignTemp;
$xml_data = $wechatAppPay->data_to_xml($param);
//在这配置证书文件
$data = curl_post_ssl('https://api.mch.weixin.qq.com/secapi/pay/refund', $xml_data, 'D:\phpstudy\PHPTutorial\WWW\consonant\lingxischool\wxpaysdk\cert\apiclient_cert.pem', 'D:\phpstudy\PHPTutorial\WWW\consonant\lingxischool\wxpaysdk\cert\apiclient_key.pem');
//            '../../wxcertificate/apiclient_cert.pem','../../wxcertificate/apiclient_key.pem'
$res = $wechatAppPay->xml_to_data($data);
if (@$res['result_code'] == 'SUCCESS') {//退款成功
    echo "succ";
}else{
    echo "error";
}

//file_cert_pem,file_key_pem两个退款必须的文件
 function curl_post_ssl($url, $vars,$file_cert_pem,$file_key_pem, $second = 30, $aHeader = array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //以下两种方式需选择一种

        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
//        curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/cert.pem');
        curl_setopt($ch,CURLOPT_SSLCERT,$file_cert_pem);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
//        curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/private.pem');
        curl_setopt($ch,CURLOPT_SSLKEY,$file_key_pem);

        //第二种方式，两个文件合成一个.pem文件
//        curl_setopt($ch, CURLOPT_SSLCERT, getcwd() . '/all.pem');

        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
//            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }

 function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

?>