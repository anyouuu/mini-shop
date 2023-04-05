<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/10/12 18:47
 */

namespace xin\container\traits;

use xin\container\ProviderInterface;

/**
 * Trait ProviderContainer
 *
 * @property array $providers
 * @package xin\container\traits
 */
trait ProviderContainer{

	/**
	 * ProviderContainer constructor.
	 *
	 * @param array $providers
	 */
	public function __construct(array $providers = []){
		$this->setProviders($providers);
	}

	/**
	 * 添加提供者
	 *
	 * @param ProviderInterface $provider
	 * @return static
	 */
	public function addProvider(ProviderInterface $provider){
		array_push($this->providers, $provider);

		return $this;
	}

	/**
	 * 注册服务
	 *
	 * @param ProviderInterface $provider
	 * @param array             $values
	 * @return $this
	 */
	public function registerProvider(ProviderInterface $provider, array $values = []){
		$provider->register($this);

		foreach($values as $key => $value){
			$this[$key] = $value;
		}

		return $this;
	}

	/**
	 * 注册服务提供者
	 */
	public function registerProviders(){
		foreach($this->providers as $provider){
			$this->registerProvider(new $provider());
		}
	}

	/**
	 * 获取服务提供者列表
	 *
	 * @return array
	 */
	public function getProviders(){
		return $this->providers;
	}

	/**
	 * 设置服务提供者列表
	 *
	 * @param array $providers
	 */
	public function setProviders($providers){
		$this->providers = [];

		foreach($providers as $provider){
			$this->addProvider($provider);
		}
	}
}
