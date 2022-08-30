<?php

namespace Godforheart\Canal\Rabbitmq;

use Com\Alibaba\Otter\Canal\Protocol\Entry;
use Com\Alibaba\Otter\Canal\Protocol\EventType;
use Com\Alibaba\Otter\Canal\Protocol\Messages;
use Com\Alibaba\Otter\Canal\Protocol\Packet;
use Com\Alibaba\Otter\Canal\Protocol\PacketType;
use Godforheart\Canal\Kernel\CanalUtil;
use Godforheart\Canal\Kernel\Config;
use Godforheart\Canal\Kernel\Contracts\Service as ServerInterface;
use Godforheart\Canal\Kernel\RowData;
use Godforheart\Canal\Kernel\Support\Arr;
use Godforheart\Canal\Kernel\Traits\WithHandler;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use xingwenge\canal_php\Message;

class Service implements ServerInterface
{
    use WithHandler;

    /**
     * @var \Godforheart\Canal\Rabbitmq\Config
     */
    private $config;

    /**
     * @var CanalUtil
     */
    private $canalUtil;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->canalUtil = new CanalUtil();
    }

    public function run()
    {
        $connection = $this->createConnection();
        while (true) {
            $payload = $connection->basic_get();
            if ($payload) {
                $this->config->isAutoAck() && $payload->ack();

                if ($this->config->isFlatMessage()) {
                    $this->handleJson($payload->body);
                } else {
                    $this->handleProtobuf($payload->body);
                }

                !$this->config->isAutoAck() && $payload->ack();

                if ($this->config->getDebugSingle()) {
                    exit;
                }
            }
        }
    }

    public function handleJson($body)
    {
        $listenData = json_decode($body, true);

        $eventType = EventType::value(Arr::get($listenData, 'type'));
        $database = Arr::get($listenData, 'database');
        $table = Arr::get($listenData, 'table');

        switch ($eventType) {
            case EventType::INSERT:
                foreach (Arr::get($listenData, 'data') ?: [] as $afterColumnArray) {
                    $this->handle(
                        $this->canalUtil->convertToRowData(
                            $eventType,
                            $database,
                            $table,
                            [],
                            $afterColumnArray
                        )
                    );
                }
                break;
            case EventType::UPDATE:
                foreach (Arr::get($listenData, 'data') ?: [] as $key => $afterColumnArray) {
                    $this->handle(
                        $this->canalUtil->convertToRowData(
                            $eventType,
                            $database,
                            $table,
                            array_merge($afterColumnArray, Arr::get($listenData, "old.$key")),
                            $afterColumnArray
                        )
                    );
                }
                break;
            case EventType::DELETE:
                foreach (Arr::get($listenData, 'old') ?: [] as $beforeColumnArray) {
                    $this->handle(
                        $this->canalUtil->convertToRowData(
                            $eventType,
                            $database,
                            $table,
                            $beforeColumnArray,
                            []
                        )
                    );
                }
                break;
        }

    }

    public function handleProtobuf(string $body)
    {
        $packet = new Packet();
        $packet->mergeFromString($body);

        $message = new Message();

        switch ($packet->getType()) {
            case PacketType::MESSAGES:
                $messages = new Messages();
                $messages->mergeFromString($packet->getBody());

                $message->setId($messages->getBatchId());

                foreach ($messages->getMessages()->getIterator() as $v) {
                    $entry = new Entry();
                    $entry->mergeFromString($v);
                    $message->addEntries($entry);
                }

                break;
            case PacketType::ACK:
            default:
                break;
        }

        if ($entries = $message->getEntries()) {
            foreach ($this->canalUtil->handleEntry($entries) as $rowDataArray) {
                /** @var RowData $rowData */
                foreach ($rowDataArray as $rowData) {
                    $this->handle($rowData);
                }
            };
        }
    }

    /**
     * @return \PhpAmqpLib\Channel\AbstractChannel|\PhpAmqpLib\Channel\AMQPChannel
     * @author 潘琪焕
     */
    public function createConnection()
    {
        $connection = new AMQPStreamConnection(
            $this->config->getHost(),
            $this->config->getPort(),
            $this->config->getUser(),
            $this->config->getPassword()
        );
        $channel = $connection->channel();

        $channel->exchange_declare(
            $this->config->getExchange(), //交换机名称
            $this->config->getType(), //路由类型
            false, //don't check if a queue with the same name exists 是否检测同名队列
            true, //the queue will not survive server restarts 是否开启队列持久化
            false //the queue will be deleted once the channel is closed. 通道关闭后是否删除队列
        );
        $channel->queue_declare(
            $this->config->getQueue(),
            false,
            true,
            false,
            false
        );

        return $channel;
    }
}