<?php
namespace app\index\controller;

use think\Db;

class Index
{
    public function index()
    {
        $res = Db::name('users')->select();
        var_dump($res);
    }
    public function import(){
        $url = 'https://beta.skylarkly.com/api/v4/organizations/387/members?id=387&with_descendants=45';
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Authorization: 3d06df76b958483cb6e36d59acfde7ef12a0b3a24587cc1476a776158145043d:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lc3BhY2VfaWQiOjF9.sBNW1M4e1SMYVq4oJhS6qu3rkk7FgzBgkryVK-L5dXA',

                //'timeout' => 60 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);

        $result = file_get_contents($url,false,$context);

        $results = json_decode($result,true);
        $res = Db::name('users')->insertAll($results);
        var_dump($res);
    }
    public function getCode(){
        $url = 'https://beta.skylarkly.com/oauth/authorize?client_id=128e3b3439f7c4743e8f198ee876c1ab3f641143a091d6942f8026865ce64134&redirect_uri=urn:ietf:wg:oauth:2.0:oob&response_type=code';
        $options = array(
            'http' => array(
                'method' => 'GET',
//                'header' => '',

                //'timeout' => 60 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);

        $result = file_get_contents($url,false,$context);

        $results = json_decode($result);
        echo $result;
    }
    public function ceshi(){
        $url = 'https://beta.skylarkly.com/oauth/authorize';//接收地址
        $data = ['client_id'=>'128e3b3439f7c4743e8f198ee876c1ab3f641143a091d6942f8026865ce64134','redirect_uri'=>'urn:ietf:wg:oauth:2.0:oob','response_type'=>'code'];
//        $header = 'Authorization:3d06df76b958483cb6e36d59acfde7ef12a0b3a24587cc1476a776158145043d:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lc3BhY2VfaWQiOjF9.sBNW1M4e1SMYVq4oJhS6qu3rkk7FgzBgkryVK-L5dXA';//定义content-type为xml
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头
        curl_setopt($ch, CURLOPT_POST, false);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL => $url,
                CURLOPT_REFERER => $url,
                CURLOPT_AUTOREFERER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_CONNECTTIMEOUT => 1,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36',
            )
        );
        $response = curl_exec($ch);//接收返回信息
        if(curl_errno($ch)){//出错则显示错误信息
            print curl_error($ch);
        }
        curl_close($ch); //关闭curl链接
        echo $response;//显示返回信息
    }
}
