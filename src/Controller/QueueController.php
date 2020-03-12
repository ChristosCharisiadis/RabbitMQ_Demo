<?php

namespace App\Controller;

use App\Entity\Attribute;
use App\Entity\Cluster;
use App\Entity\Consumption;
use App\Entity\Endpoint;
use App\Entity\Gateway;
use App\Entity\Profile;
use App\Messages\CustomApiMessage;
use App\Utils\DbUtils;
use App\Utils\QueueUtils;
use PhpAmqpLib\Connection\AMQPConnection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QueueController
 * @package App\Controller
 * @Route("/queue", name="queue.")
 */
class QueueController extends AbstractController
{

    /**
     * Fetch data from the remote API and post them message to the RabbitMQ.
     * The query parameter 'messages' can optionally be given with int values [1-10] to select how many messages to send.
     * @Route("/post", name="post")
     * @param Request $request
     * @return Response View with the posted messages.
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Exception
     */
    public function post(Request $request)
    {
        // Check if messages query parameter in an integer in range [1-10]
        $messagesValue = $request->query->get('messages');
        // If no messages query was given, use the default value of 1
        $numberOfMessages = $messagesValue === null ? 1 : filter_var($messagesValue, FILTER_VALIDATE_INT);
        if ($numberOfMessages <= 0 || $numberOfMessages > 10)
            return $this->render('queue/messages.html.twig', [
            'title' => 'messages value must be an integer in the range [1-10]'
            ]);

        // Open a connection and channel to RabbitMQ
        $queueConnection = $this->getQueueConnection();
        $queueChannel = $queueConnection->channel();

        $messages = array();
        while ($numberOfMessages-- > 0) {
            // Get the data from the remote API
            $client = HttpClient::create();
            $response = $client->request('GET', $this->getParameter('api_endpoint'))->getContent();
            $jsonObject = json_decode($response, true);

            $message = CustomApiMessage::createFromApiResponse($jsonObject);

            // Send the message to RabbitMq
            QueueUtils::sendJsonMessage($queueChannel,
                $this->getExchange(),
                $message->getKey(),
                $message->getBody());

            $messages[] = $message;
        }

        // Close RabbitMQ channel and connection
        $queueChannel->close();
        $queueConnection->close();

        $messagesPosted = count($messages);
        return $this->render('queue/messages.html.twig', [
            'title' => $messagesPosted > 1 ? $messagesPosted . ' Messages Posted' : 'Message Posted',
            'messages' => $messages
        ]);
    }

    /**
     * Consume a message from the queue, if it exists, and write the data to the database.
     * @Route("/consume", name="consume")
     * @return Response A view with the consumed message if it exists.
     * @throws \Exception
     */
    public function consume() {
        // Open a connection and channel to RabbitMQ
        $queueConnection = $this->getQueueConnection();
        $queueChannel = $queueConnection->channel();

        // Consume the message from the queue
        $queueMessage = QueueUtils::consumeMessage($queueChannel, $this->getQueue());

        $message = null;
        if ($queueMessage != null) {
            $message = CustomApiMessage::createFromMessage($queueMessage);
            $this->writeDataToDb($message);
        }

        // Close RabbitMQ channel and connection
        $queueChannel->close();
        $queueConnection->close();

        return $this->render('queue/messages.html.twig', [
            'title' => $message === null ? 'No Message in Queue' : 'Message Consumed',
            'messages' => $message === null ? null : [$message]
        ]);
    }

    /**
     * Consumes all messages from the queue, if any exist, and writes the data to the database.
     * @Route("/consumeAll", name="consumeAll")
     * @return Response A view with the consumed messages if any exist.
     * @throws \Exception
     */
    public function consumeAll() {
        // Open a connection and channel to RabbitMQ
        $queueConnection = $this->getQueueConnection();
        $queueChannel = $queueConnection->channel();

        $messages = array();
        // While there are messages keep consuming and writing to the database
        while ($queueMessage = QueueUtils::consumeMessage($queueChannel, $this->getQueue())) {
            $message = CustomApiMessage::createFromMessage($queueMessage);
            $messages[] = $message;
            $this->writeDataToDb($message);
        }

        // Close RabbitMQ channel and connection
        $queueChannel->close();
        $queueConnection->close();

        return $this->render('queue/messages.html.twig', [
            'title' => empty($messages) ? 'No Messages in Queue' : 'All Messages Consumed',
            'messages' => $messages
        ]);
    }

    private function getQueueConnection() {
        return new AMQPConnection($this->getParameter('app.rabbitmq.hostname'),
            $this->getParameter('app.rabbitmq.port'),
            $this->getParameter('app.rabbitmq.username'),
            $this->getParameter('app.rabbitmq.password'));
    }

    private function getExchange() {
        return $this->getParameter('app.rabbitmq.exchange');
    }

    private function getQueue() {
        return $this->getParameter('app.rabbitmq.queue');
    }

    private function writeDataToDb(CustomApiMessage $message) {
        $em = $this->getDoctrine()->getManager();

        // Fetch the objects from the database. If they don't exist, write them to the database
        $gateway = DbUtils::findOneObjectBy($em, Gateway::class, 'eui', $message->getGatewayEui());
        if ($gateway === null) {
            $gateway = new Gateway($message->getGatewayEui());
            DbUtils::writeObject($em, $gateway);
        }
        $profile = DbUtils::writeObjectIfNotExist($em, new Profile($message->getProfileId()), $message->getProfileId());
        $endpoint = DbUtils::writeObjectIfNotExist($em, new Endpoint($message->getEndpointId()), $message->getEndpointId());
        $cluster = DbUtils::writeObjectIfNotExist($em, new Cluster($message->getClusterId()), $message->getClusterId());
        $attribute = DbUtils::writeObjectIfNotExist($em, new Attribute($message->getAttributeId()), $message->getAttributeId());

        // Create new consumption with the fetched DB objects and write the entry to the database
        $consumption = new Consumption();
        $consumption->setGateway($gateway)
                    ->setProfile($profile)
                    ->setEndpoint($endpoint)
                    ->setCluster($cluster)
                    ->setAttribute($attribute)
                    ->setValue($message->getValue())
                    ->setTimestamp($message->getTimestamp());
        DbUtils::writeObject($em, $consumption);
    }
}