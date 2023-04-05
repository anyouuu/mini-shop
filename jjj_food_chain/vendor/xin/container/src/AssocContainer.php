<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/10/12 18:45
 */

namespace xin\container;

use xin\container\traits\AssocContainer as AssocContainerTrait;

/**
 * Class AssocContainer
 *
 * @package xin\container
 */
class AssocContainer implements \ArrayAccess{

	use AssocContainerTrait;
}
