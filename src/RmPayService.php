<?php

namespace RmTop\Rmpay;

use RmTop\Rmpay\command\PayFilePublish;
use think\Service;

/**
 */
class RmPayService extends Service
{
    /**
     * Register service.
     *
     * @return void
     */
    public function register()
    {
        // 注册数据迁移服务
        $this->app->register(\think\migration\Service::class);
    }

    /**
     * Boot function.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands(['rmtop:publish_pay' => PayFilePublish::class,]);
    }


}
