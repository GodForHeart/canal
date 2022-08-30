<?php

namespace Godforheart\Canal\Client;

use Godforheart\Canal\Kernel\CanalUtil;
use Godforheart\Canal\Kernel\Contracts\Service as ServerInterface;
use Godforheart\Canal\Kernel\RowData;
use Godforheart\Canal\Kernel\Traits\WithHandler;
use xingwenge\canal_php\CanalConnectorFactory;
use Godforheart\Canal\Kernel\Config;

class Service implements ServerInterface
{
    use WithHandler;

    /**
     * @var \Godforheart\Canal\Client\Config
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
        $client = CanalConnectorFactory::createClient($this->config->getType());
        $client->connect($this->config->getHost(), $this->config->getPort());
        $client->checkValid();
        $client->subscribe($this->config->getClientId(), $this->config->getDestination(), $this->config->getFilter());

        while (true) {
            $message = $client->get($this->config->getNum());
            if ($entries = $message->getEntries()) {
                foreach ($this->canalUtil->handleEntry($entries) as $rowDataArray) {
                    /** @var RowData $rowData */
                    foreach ($rowDataArray as $rowData) {
                        $this->handle($rowData);
                    }
                };

                if ($this->config->getDebugSingle()) {
                    exit;
                }
            }
            sleep(1);
        }

        $client->disConnect();
    }
}