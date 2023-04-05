<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/10/12 18:36
 */

use xin\container\Container;

require_once '../vendor/autoload.php';

/**
 * Class Application
 *
 * @property-read array $config
 * @property-read B     $b
 */
class Application extends Container{

	/**
	 * Application constructor.
	 */
	public function __construct(){
		$this->singleton('config', function(){
			return [
				'title' => 'hello world',
			];
		});

		$this->bind('b', 'B');
	}
}

class B{

	public $name = "小明";

	public $age = 18;
}

$app = new \Application();
var_dump($app->config);
var_dump($app->config);
var_dump($app->config);
var_dump($app->b);
var_dump($app->b);
var_dump($app->b);
var_dump($app->b);
