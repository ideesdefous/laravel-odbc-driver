<?php

namespace IdeesDeFous\Acomba\Connectors;

use Exception;
use Illuminate\Database\Connectors\Connector as BaceConnector;
use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Support\Arr;
use PDO;

class AcombaConnector extends BaceConnector implements ConnectorInterface
{
    /**
     * @param array $config
     *
     * @return PDO
     */
    public function connect(array $config)
    {
        $dsn = $this->getDsn($config);
        $options = $this->getOptions($config);

        $connection = $this->createConnection($dsn, $config, $options);

        return $connection;
    }

    /**
     * @param array $config
     *
     * @return string
     */
    protected function getDsn(array $config)
    {
        /*
         * @var string $host
         * @var string $port
         * @var string $database
         */
        extract($config);

        return "odbc:{$config['dsn']}";
    }

    /**
     * Create a new PDO connection.
     *
     * @param string $dsn
     * @param array  $config
     *
     * @return \PDO
     */
    public function createConnection($dsn, array $config, array $options)
    {
        $username = Arr::get($config, 'username');
        $password = Arr::get($config, 'password');

        try {
            $pdo = new PDO($dsn, $username, $password, $options);
        } catch (Exception $e) {
            $pdo = $this->tryAgainIfCausedByLostConnection(
                $e, $dsn, $username, $password, $options
            );
        }

        return $pdo;
    }
}
