<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/10/12 19:01
 */
use xin\container\Container;
use xin\container\ProviderContainer;
use xin\container\ProviderInterface;

require_once '../vendor/autoload.php';

$app = new ProviderContainer();
$app->addProvider(new class implements ProviderInterface{

	/**
	 * 注册服务
	 *
	 * @param \xin\container\Container $container
	 */
	public function register(Container $container){
		// TODO: Implement register() method.

		$container->singleton('config', function(){
			return [
				'title' => 'hello world',
			];
		});
	}
});

$app->registerProviders();

var_dump($app->config);
