<?php


namespace App\Messages;


use App\Utils\MathUtils;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * This holds the data that will be posted and consumed to RabbitMQ, and also generates the format of the key
 * and the body of the message. It will also deconstruct from the key or body the initial fields received from the API.
 * @package App\Messages
 */
class CustomApiMessage
{

    private $key;

    private $body;

    /**
     * Creates a new CustomApiMessage from a Json response retrieved from the API.
     * @param array $jsonArray
     * @return CustomApiMessage
     */
    public static function createFromApiResponse(array $jsonArray) : CustomApiMessage {
        $instance = new self();
        $instance->setKey($jsonArray);
        $instance->setBody(($jsonArray));

        return $instance;
    }

    /**
     * Creates a new CustomApiMessage from a message consumed from RabbitMQ.
     * @param AMQPMessage $message
     * @return CustomApiMessage
     */
    public static function createFromMessage(AMQPMessage $message) : CustomApiMessage {
        $instance = new self();
        $instance->key = $message->delivery_info['routing_key'];
        $instance->body = $message->getBody();

        return $instance;
    }

    public function getKey() : string {
        return $this->key;
    }

    public function getBody() : string {
        return $this->body;
    }

    /**
     * Creates the key that will be used in the message.
     * The key is created via the decimal representations of the input values, separated by '.'
     * @param array $jsonArray
     */
    private function setKey(array $jsonArray) {
        $this->key =  join('.', [
            MathUtils::bcHexDec($jsonArray['gatewayEui']),
            hexdec($jsonArray['profileId']),
            hexdec($jsonArray['endpointId']),
            hexdec($jsonArray['clusterId']),
            hexdec($jsonArray['attributeId'])
        ]);
    }

    /**
     * Creates the Json body that will be send with the message.
     * @param array $jsonArray
     */
    private function setBody(array $jsonArray) {
        $this->body = json_encode([
            'value' => $jsonArray['value'],
            'timestamp' => $jsonArray['timestamp']
        ]);
    }

    public function getGatewayEui() {
        return MathUtils::bcDecHex(explode('.', $this->key)[0]);
    }

    public function getProfileId() {
        return explode('.', $this->key)[1];
    }

    public function getEndpointId() {
        return explode('.', $this->key)[2];
    }

    public function getClusterId() {
        return explode('.', $this->key)[3];
    }

    public function getAttributeId() {
        return explode('.', $this->key)[4];
    }

    public function getValue() {
        return json_decode($this->body, true)['value'];
    }

    public function getTimestamp() {
        return json_decode($this->body, true)['timestamp'];
    }

}