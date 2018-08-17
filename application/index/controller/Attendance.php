<?php
namespace app\index\controller;

use think\Db;

class Attendance
{
    public function index(){
//        $user_id = request()->get('id');
        $user_id = 291;

        $res = Db::table('users')->where(['id'=>$user_id])->find();
//        var_dump($res);exit;
        if ($res){
            return json_encode([
                'code'=>200,
                'msg'=>'',
                'data'=>$res
            ]);
        }else{
            return json_encode([
                'code'=>500,
                'msg'=>'未找到相关用户信息',
                'data'=>[]
            ]);
        }
    }
    public function punchClock(){
        $user_id = request()->post('user_id');

    }
}