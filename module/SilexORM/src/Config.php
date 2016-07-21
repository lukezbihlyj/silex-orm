<?php

namespace LukeZbihlyj\SilexORM;

use PDO;
use Spot\Config as SpotConfig;
use Doctrine\DBAL;

/**
 * @package LukeZbihlyj\SilexORM\Config
 */
class Config extends SpotConfig
{
    /**
     * {@inheritDoc}
     */
    public function addConnection($name, $dsn, $default = false)
    {
        // Connection name must be unique
        if (isset($this->_connections[$name])) {
            throw new Exception('Connection for \'' . $name . '\' already exists. Connection name must be unique.');
        }

        if ($dsn instanceof DBAL\Connection) {
            $connection = $dsn;
        } else {
            if (is_array($dsn)) {
                $connectionParams = $dsn;
            } else {
                $connectionParams = $this->parseDsn($dsn);

                if ($connectionParams === false) {
                    throw new Exception('Unable to parse given DSN string');
                }

                if ($connectionParams['driver'] == 'pdo_mysql' && isset($connectionParams['persistent'])) {
                    $host = $connectionParams['host'] ?: 'localhost';

                    if ($connectionParams['port']) {
                        $host .= ':' . $connectionParams['port'];
                    }

                    $connectionParams['pdo'] = new PDO(
                        'mysql:host=' . $host . ';dbname=' . $connectionParams['dbname'],
                        $connectionParams['user'],
                        $connectionParams['password'],
                        [
                            PDO::ATTR_PERSISTENT => true
                        ]
                    );
                }
            }

            $config = new DBAL\Configuration();
            $connection = DBAL\DriverManager::getConnection($connectionParams, $config);
        }

        // Set as default connection?
        if (true === $default || null === $this->_defaultConnection) {
            $this->_defaultConnection = $name;
        }

        // Store connection and return adapter instance
        $this->_connections[$name] = $connection;

        return $connection;
    }
}
