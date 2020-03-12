<?php


namespace App\Utils;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * This class contains some static helper methods for communicating with RabbitMQ.
 * @package App\Utils
 */
class QueueUtils
{
    private function __construct() {}

    /**
     * Posts a message to a queue with the given key.
     * @param AMQPChannel $channel The channel through which to publish.
     * @param string $exchange The exchange the message will be published to.
     * @param string $key The key of the message.
     * @param string $body The body of the message.
     */
    public static function sendJsonMessage(AMQPChannel $channel, string $exchange, string $key, string $body) {
        $message = new AMQPMessage($body, [
            'content_type' => 'application/json',
            'timestamp' => microtime(true) * 1000
        ]);
        $channel->basic_publish($message, $exchange, $key);
    }

    /**
     * Consumes a message from a queue.
     * @param AMQPChannel $channel The channel through which to consume.
     * @param string $queue The queue to consume from.
     * @return mixed The message consumed from the queue, or null if no message is in the queue.
     */
    public static function consumeMessage(AMQPChannel $channel, string $queue) {
        return $channel->basic_get($queue, true);
    }
}