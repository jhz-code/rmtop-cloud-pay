<?php


namespace RmTop\RmPay\core;
use RmTop\RmPay\model\TopPayConfigModel;

/**
 * Class TopPayConfig
 * @package RmTop\RmPay\core
 * 支付配置项
 */
class TopPayConfig
{

    /**
     * 创建配置
     * @param array $data
     * @return TopPayConfigModel|\think\Model
     */
    static  function addConfig(array $data)
    {
        return TopPayConfigModel::create([
            'config_text' => serialize($data)
        ]);
    }

    /**
     * 更新配置
     * @param int $id
     * @param array $data
     * @return TopPayConfigModel
     */
    static function editConfig(int $id, array $data)
    {
        return TopPayConfigModel::where(['id' => $id])->update([
            'config_text' => serialize($data)
        ]);
    }


    /**
     * @param int $id
     * @return bool
     * 删除配置
     */
    static function deleteConfig(int $id)
    {
        return TopPayConfigModel::where(['id' => $id])->delete();

    }


    /**
     * 输出配置
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
  static function getConfig(int $id){
       $result =  TopPayConfigModel::where(['id' => $id])->find();
       return unserialize($result['config_text']);
    }

}