<?php

namespace Godforheart\Canal\Tests;

use Godforheart\Canal\CanalFactory;
use Godforheart\Canal\Kernel\Handler;
use Godforheart\Canal\Kernel\RowData;
use PHPUnit\Framework\TestCase;
use xingwenge\canal_php\CanalClient;

class BuilderTest extends TestCase
{
    public function testCanalClient()
    {
        $client = CanalFactory::getClient([
            'type' => CanalClient::TYPE_SOCKET_CLUE,
            'host' => 'xxx.xxx.xxx.xxx',
            'port' => 11111,
            'clientId' => 1001,
            'destination' => 'TCP',
            'filter' => ".*\\..*",
        ]);

        $client
            //  覆盖默认listen
//            ->setConfig('default_canal_listen', function (RowData $rowData) {
//                var_dump("I'm default_canal_listen111111");
//            })
            //  设置调试单条消费
            ->setConfig('debug_single', true)
            //  设置客户端每次获取数量
            ->setConfig('num', 1)
            ->getService()
            //  取消默认的处理
            ->withoutDefaultHandler()
            //  添加处理功能
            ->addHandler(
                (new Handler())
                    //  设置处理内容，function 或 具体类，具体类必须含有【handle】方法
                    ->setHandler(
                        function (RowData $rowData) {
                            var_dump(['addHandler' => 'ok']);
                        }
                    )
                    //  设置通过的数据库名
                    ->setDatabase('.*')
                    //  只处理指定规则表（正则）
                    ->setTable('proj.*')
            )
            ->run();
    }

    public function testCanalRabbitmq()
    {
        //  Json 格式单条数据（$flatMessage模式）
//        $rabbitmq = $this->getRabbitmq(true, 'xxx');
        //  序列化批量
        $rabbitmq = $this->getRabbitmq(false, 'xxx');

        $rabbitmq
//            ->setConfig('default_canal_listen', function () {
//                var_dump("I'm default_canal_listen111111");
//            })
            ->setConfig('debug_single', true)
            //  自动ack，debug时使用
            ->setConfig('auto_ack', true)
            ->getService()
            ->withoutDefaultHandler()
            ->addHandler(
                (new Handler())
                    ->setHandler(
                        function (RowData $rowData) {
                            var_dump('addHandler：OK');
                        }
                    )
                    //  只处理指定规则表（正则）
                    ->setTable('proj.*')
            )
            ->run();
    }

    private function getRabbitmq(bool $flatMessage, string $queue)
    {
        return CanalFactory::getRabbitmq([
            'flatMessage' => $flatMessage,
            'host' => 'xxx.xxx.xxx.xxx',
            'port' => 5672,
            'user' => 'xxx',
            'password' => 'xxx',
            'exchange' => 'xxx',
            'type' => 'xxx',
            'queue' => $queue
        ]);
    }
}
