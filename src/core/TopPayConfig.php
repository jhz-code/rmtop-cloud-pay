<?php


namespace RmTop\RmPay\core;
use RmTop\RmPay\lib\wxpay\v3\CertificateDownloader;
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
     * 更新证书编号
     * @param int $id
     * @param $serial_no
     * @return TopPayConfigModel
     */
    static function editConfigSerial_no(int $id, $serial_no)
    {
        return TopPayConfigModel::where(['id' => $id])->update([
            'serial_no' => $serial_no
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
        $res = unserialize($result['config_text']);
        $res['serial_no'] = $result['serial_no'];
        if(!$res['serial_no']){
            $res['pay_config_id'] = $id;
            (new CertificateDownloader())->checkCertificates($res);
            return  self::getConfig($id);
        }else{
            return $res;
        }
    }


}