<?php

namespace think\swoole\websocket;

use Swoole\Websocket\Frame;
use think\swoole\contract\websocket\ParserInterface;

class SimpleParser implements ParserInterface
{

    /**
     * Encode output payload for websocket push.
     *
     * @param string $event
     * @param mixed  $data
     *
     * @return mixed
     */
    public function encode(string $event, $data)
    {
        return json_encode(
            [
                'event' => $event,
                'data'  => $data,
            ]
        );
    }

    /**
     * Input message on websocket connected.
     * Define and return event name and payload data here.
     *
     * @param Frame $frame
     *
     * @return array
     */
    public function decode($frame)
    {
        $data = json_decode($frame->data, true);

        return [
            'event' => $data['event'] ?? null,
            'data'  => $data['data'] ?? null,
        ];
    }
}
