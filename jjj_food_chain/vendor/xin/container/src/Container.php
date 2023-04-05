<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/10/12 17:03
 */

namespace xin\container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use xin\helper\Str;

abstract class Container implements ContainerInterface{

	/**
	 * 绑定列表
	 *
	 * @var array
	 */
	protected $binds = [];

	/**
	 * 实现器
	 *
	 * @var array
	 */
	protected $implementers = [];

	/**
	 * 实例列表
	 *
	 * @var array
	 */
	protected $instances = [];

	/**
	 * 获取对象类型的参数值
	 *
	 * @access protected
	 * @param string $className 类名
	 * @param array  $vars 参数
	 * @return mixed
	 */
	protected function getObjectParam($className, &$vars){
		$array = $vars;
		$value = array_shift($array);

		if($value instanceof $className){
			$result = $value;
			array_shift($vars);
		}else{
			$result = $this->get($className);
		}

		return $result;
	}

	/**
	 * 绑定参数
	 *
	 * @access protected
	 * @param \ReflectionMethod|\ReflectionFunction $reflect 反射类
	 * @param array                                 $vars 参数
	 * @return array
	 * @throws \ReflectionException
	 */
	protected function bindParams($reflect, $vars = []){
		if($reflect->getNumberOfParameters() == 0){
			return [];
		}

		// 判断数组类型 数字数组时按顺序绑定参数
		reset($vars);
		$type = key($vars) === 0 ? 1 : 0;

		// 获取函数和类方法的参数列表
		$params = $reflect->getParameters();

		$args = [];
		foreach($params as $param){
			$class = $param->getClass();

			$name = $param->getName();
			$lowerName = Str::snake($name, '_', false);

			if($class){
				$args[] = $this->getObjectParam($class->getName(), $vars);
			}elseif(1 == $type && !empty($vars)){
				$args[] = array_shift($vars);
			}elseif(0 == $type && isset($vars[$name])){
				$args[] = $vars[$name];
			}elseif(0 == $type && isset($vars[$lowerName])){
				$args[] = $vars[$lowerName];
			}elseif($param->isDefaultValueAvailable()){
				$args[] = $param->getDefaultValue();
			}else{
				throw new \InvalidArgumentException('method param miss:'.$name);
			}
		}

		return $args;
	}

	/**
	 * 调用反射执行类的实例化 支持依赖注入
	 *
	 * @access public
	 * @param string $class 类名
	 * @param array  $vars 参数
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function invokeClass($class, $vars = []){
		$reflect = new \ReflectionClass($class);

		if($reflect->hasMethod('__make')){
			$method = new \ReflectionMethod($class, '__make');

			if($method->isPublic() && $method->isStatic()){
				$args = $this->bindParams($method, $vars);
				return $method->invokeArgs(null, $args);
			}
		}

		$constructor = $reflect->getConstructor();

		$args = $constructor ? $this->bindParams($constructor, $vars) : [];

		return $reflect->newInstanceArgs($args);
	}

	/**
	 * 调用函数
	 *
	 * @param callable $function
	 * @param array    $vars
	 * @return mixed
	 * @throws \ReflectionException
	 */
	protected function invokeFunction($function, $vars = []){
		$reflect = new \ReflectionFunction($function);

		$args = $this->bindParams($reflect, $vars);

		return call_user_func_array($function, $args);
	}

	/**
	 * 调用反射执行类的方法 支持参数绑定
	 *
	 * @access public
	 * @param mixed $method 方法
	 * @param array $vars 参数
	 * @return mixed
	 * @throws \ReflectionException
	 */
	protected function invokeMethod($method, $vars = []){
		if(is_array($method)){
			$class = is_object($method[0]) ? $method[0] : $this->invokeClass($method[0]);
			$reflect = new \ReflectionMethod($class, $method[1]);
		}else{
			// 静态方法
			$reflect = new \ReflectionMethod($method);
		}

		$args = $this->bindParams($reflect, $vars);

		return $reflect->invokeArgs(isset($class) ? $class : null, $args);
	}

	/**
	 * 调用反射执行callable 支持参数绑定
	 *
	 * @access public
	 * @param mixed $callable
	 * @param array $vars 参数
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function invoke($callable, $vars = []){
		if($callable instanceof \Closure){
			return $this->invokeFunction($callable, $vars);
		}

		return $this->invokeMethod($callable, $vars);
	}

	/**
	 * 调用反射执行类的方法 支持参数绑定
	 *
	 * @access public
	 * @param object $instance 对象实例
	 * @param mixed  $reflect 反射类
	 * @param array  $vars 参数
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function invokeReflectMethod($instance, $reflect, $vars = []){
		$args = $this->bindParams($reflect, $vars);
		return $reflect->invokeArgs($instance, $args);
	}

	/**
	 * 多实例绑定
	 *
	 * @param string $id
	 * @param mixed  $implementer
	 * @return $this
	 */
	public function bind($id, $implementer){
		$this->binds[$id] = 0;
		$this->implementers[$id] = $implementer;

		return $this;
	}

	/**
	 * 单实例绑定
	 *
	 * @param string $id
	 * @param mixed  $implementer
	 * @return $this
	 */
	public function singleton($id, $implementer){
		$this->binds[$id] = 1;
		$this->implementers[$id] = $implementer;

		return $this;
	}

	/**
	 * 创建实例
	 *
	 * @param string $id
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function make($id){
		$bind = $this->binds[$id];
		$impl = &$this->implementers[$id];

		if(is_array($impl) || is_callable($impl)){
			$instance = $this->invoke($impl);
		}else{
			$instance = $this->invokeClass($impl);
		}

		if($bind === 1){
			$this->instances[$id] = $instance;
		}

		return $instance;
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 * @return mixed Entry.
	 * @throws ContainerExceptionInterface Error while retrieving the entry.
	 * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
	 */
	public function get($id){
		if(!isset($this->binds[$id])){
			throw new NotFoundException("未注册的依赖：{$id}");
		}

		$bind = &$this->binds[$id];
		if($bind === 0){
			try{
				return $this->make($id);
			}catch(\ReflectionException $e){
				throw new ContainerException($e->getMessage(), $e->getCode(), $e);
			}
		}

		if(!isset($this->instances[$id])){
			try{
				$this->make($id);
			}catch(\ReflectionException $e){
				throw new ContainerException($e->getMessage(), $e->getCode(), $e);
			}
		}

		return $this->instances[$id];
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param string $id Identifier of the entry to look for.
	 * @return bool
	 */
	public function has($id){
		return isset($this->binds[$id]);
	}

	/**
	 * 获取一个属性
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function __get($id){
		return $this->get($id);
	}

	/**
	 * 设置一个属性
	 *
	 * @param string $id
	 * @param mixed  $value
	 */
	public function __set($id, $value){
		$this->singleton($id, $value);
	}

}
