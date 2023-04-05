<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/10/12 18:48
 */

namespace xin\container;

use xin\container\traits\ProviderContainer as ProviderContainerTrait;

class ProviderContainer extends Container{

	use ProviderContainerTrait;

	/**
	 * 服务提供者
	 *
	 * @var array
	 */
	protected $providers = [];

	/**
	 * ProviderContainer constructor.
	 *
	 * @param array $providers
	 */
	public function __construct(array $providers = []){
		if(!empty($providers)){
			$this->setProviders($providers);
		}
	}

}
