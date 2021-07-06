<?php


namespace WenRuns\Service;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * 注册应用服务
     *
     * @return void
     */
    public function register()
    {
//        $this->mergeConfigFrom(
//            __DIR__.'/path/to/config/courier.php', 'courier'
//        );
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'wenruns');
    }

    /**
     * 启动应用服务
     *
     * @return void
     */
    public function boot()
    {
//        $this->publishes([
//            __DIR__.'/path/to/config/courier.php' => config_path('courier.php'),
//        ]);
    }
}
