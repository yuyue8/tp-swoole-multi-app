<?php

namespace Yuyue8\TpSwooleMultiApp;

class Service extends \think\Service
{

    /**
     * 服务启动
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('think\swoole\Http', Http::class);
    }

}
