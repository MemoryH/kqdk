<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Env;
use think\Session;

class Index extends Controller
{

    static $name;

    public function tencent(){
        $data = file_get_contents('php://input');
//        echo json_encode($data);exit;
        $newData = json_decode($data,true);
        $latitude = $newData["latitude"];
        $longitude = $newData["longitude"];


        //腾讯地图坐标转换官网：http://lbs.qq.com/webservice_v1/guide-convert.html

        $q = "http://apis.map.qq.com/ws/coord/v1/translate?locations=".$latitude.",".$longitude."&type=1&key=CUPBZ-YJO66-EGMSD-EBBR4-KIGRS-5JFZL";
        $resultQ = json_decode(file_get_contents($q),true);
//        echo $resultQ["locations"][0]["lat"];exit;

        $latitudeNew = $resultQ["locations"][0]["lat"];
        $longitudeNew = $resultQ["locations"][0]["lng"];


        $address = "https://apis.map.qq.com/ws/geocoder/v1/?location=".$latitudeNew.",".$longitudeNew."&key=CUPBZ-YJO66-EGMSD-EBBR4-KIGRS-5JFZL&get_poi=1";
        $address = json_decode(file_get_contents($address),true);
        $address_true = $address['result']['formatted_addresses']['recommend'];
        $status = $address['status'];
        $returnDataArray = array("address"=>$address_true,"status"=>$status);
        $returnData = json_encode($returnDataArray);
        echo $returnData;
    }
    public function index()
    {
        $url =Env::get('GER_URL').'/oauth/authorize?client_id='.Env::get('GET_USER_CLIENT_ID').'&redirect_uri='.Env::get('GET_USER_REDIRECT_URL').'/index/index/getuser&response_type=code';
        $this->redirect($url);
    }

    public function getUser(){

        $code = $_GET['code'];
        $url =Env::get('GER_URL').'/oauth/token';
//        var_dump($url);exit;
        $data = [
            'client_id'=>Env::get('GET_USER_CLIENT_ID'),
            'client_secret'=>Env::get('GET_USER_CLIENT_SECRET'),
            'code'=>$code,
            'grant_type'=>'authorization_code',
            'redirect_uri'=>Env::get('GET_USER_REDIRECT_URL').'/index/index/getuser'
        ];

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
        //获取用户信息
        $url = Env::get('GER_URL').'/api/v1/user?access_token='.$token;//接收地址
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
        return $this->redirect('attendance/index',['user_id'=>$id]);

    }

    public function import(){
        $url = Env::get('GER_URL').'/api/v4/organizations/387/members?id=387&with_descendants=45';
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Authorization:'.Env::get('INTERFACE_SIGNATURE'),
            )
        );
        $context = stream_context_create($options);

        $result = file_get_contents($url,false,$context);

        $results = json_decode($result,true);
        $res= Db::table('users')->field('id')->select();
        $ids = [];
        foreach ($res as $re){
            $ids[] = $re['id'];
        }

        if ($ids){

            Db::startTrans();
            try{

                $res = Db::table('users')->where("id","in",$ids)->delete();

                $res = Db::name('users')->insertAll($results);

                // 提交事务

                Db::commit();
                echo 'success';
            } catch (\Exception $e) {
                // 回滚事务
                var_dump(222);
                Db::rollback();
            }

        }


    }

    public function ceshi(){

        define('CESHI','ceshi');

    }
    public function ceshi1(){
        echo CESHI;
    }
}
