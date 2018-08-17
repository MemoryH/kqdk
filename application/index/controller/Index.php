<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $this->assign('name', '123');
        return $this->fetch();
    }

    public function getUser(){

        $code = $_GET['code'];
        $url = 'https://beta.skylarkly.com/oauth/token';//接收地址
        $data = [
            'client_id'=>'128e3b3439f7c4743e8f198ee876c1ab3f641143a091d6942f8026865ce64134',
            'client_secret'=>'68ab66e02dd030c51892ecbfcc4d711a0db191539f18db86a5f0d69d8ed44e57',
            'code'=>$code,
            'grant_type'=>'authorization_code',
            'redirect_uri'=>'http://kaoqin.webuildus.com/index/index/getuser'
        ];
//        $header = 'Authorization:3d06df76b958483cb6e36d59acfde7ef12a0b3a24587cc1476a776158145043d:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lc3BhY2VfaWQiOjF9.sBNW1M4e1SMYVq4oJhS6qu3rkk7FgzBgkryVK-L5dXA';//定义content-type为xml
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头
        curl_setopt($ch, CURLOPT_POST, true);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据

        $response = curl_exec($ch);//接收返回信息
        if(curl_errno($ch)){//出错则显示错误信息
            print curl_error($ch);
        }
        curl_close($ch); //关闭curl链接
        $response = json_decode($response);
        $token = $response->access_token;
//        echo $token;exit;
        //获取用户信息
        $url = 'https://beta.skylarkly.com/api/v1/user?access_token='.$token;//接收地址

//        $header = 'Authorization:3d06df76b958483cb6e36d59acfde7ef12a0b3a24587cc1476a776158145043d:eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lc3BhY2VfaWQiOjF9.sBNW1M4e1SMYVq4oJhS6qu3rkk7FgzBgkryVK-L5dXA';//定义content-type为xml
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头
        curl_setopt($ch, CURLOPT_POST, false);//设置为POST方式


        $response1 = curl_exec($ch);//接收返回信息
        if(curl_errno($ch)){//出错则显示错误信息
            print curl_error($ch);
        }
        curl_close($ch); //关闭curl链接
        $response1 =  json_decode($response1,true);
        $id =$response1['id'];
        $date = date('Y-m-d',time());
        $res = Db::table('users')->alias('u')->where(['u.id'=>$id])->join('attendance a','u.id = a.user_id')->where(['a.time_day'=>$date])->find();
        if ($res){

        }else{
            $res = $response1;

//            var_dump($res);exit;
            $this->assign('res',$res);

            return $this->fetch('index');
        }
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
        return $this->fetch();
    }
}
