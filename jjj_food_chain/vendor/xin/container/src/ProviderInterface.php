<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/10/12 17:09
 */
namespace xin\container;

interface ProviderInterface{

	/**
	 * 注册服务
	 *
	 * @param \xin\container\Container $container
	 */
	public function register(Container $container);
}
