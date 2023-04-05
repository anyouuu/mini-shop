<?php

namespace think\swoole\websocket\socketio;

use think\swoole\contract\websocket\ParserInterface;

class Parser implements ParserInterface
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
        $packet       = Packet::MESSAGE . Packet::EVENT;
        $shouldEncode = is_array($data) || is_object($data);
        $data         = $shouldEncode ? json_encode($data) : $data;
        $format       = $shouldEncode ? '["%s",%s]' : '["%s","%s"]';

        return $packet . sprintf($format, $event, $data);
    }

    /**
     * Decode message from websocket client.
     * Define and return payload here.
     *
     * @param \Swoole\Websocket\Frame $frame
     *
     * @return array
     */
    public function decode($frame)
    {
        $payload = Packet::getPayload($frame->data);

        return [
            'event' => $payload['event'] ?? null,
            'data'  => $payload['data'] ?? null,
        ];
    }
}
