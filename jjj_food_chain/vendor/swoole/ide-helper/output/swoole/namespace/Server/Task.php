<?php

namespace Swoole\Server;

class Task
{

    public $data;

    public $id = -1;

    public $worker_id = -1;

    public $flags = 0;

    /**
     * @return mixed
     */
    public function finish($data)
    {
    }

    /**
     * @return mixed
     */
    public static function pack($data)
    {
    }


}
