<?php

namespace LukeZbihlyj\SilexORM;

use LukeZbihlyj\SilexPlus\Application;
use LukeZbihlyj\SilexPlus\ModuleInterface;
use Spot\Locator;
use Spot\Config;
use Pimple;

/**
 * @package LukeZbihlyj\SilexORM\Module
 */
class Module implements ModuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigFile()
    {
        return __DIR__ . '/../config/module.php';
    }

    /**
     * {@inheritDoc}
     */
    public function init(Application $app)
    {
        $app['spot.connections.default'] = null;

        $app['spot.connections'] = [
            'main' => $app['database.dsn']
        ];

        $app['spot'] = $app->share(function() use ($app) {
            return new Locator($app['spot.config']);
        });

        $app['spot.config'] = $app->share(function() use ($app) {
            $config = new Config();
            $connections = $app['spot.connections'];

            if ($connections instanceof Pimple) {
                $keys = $connections->keys();
            } else {
                $keys = array_keys($connections);
            }

            foreach ($keys as $key) {
                if (isset($app['spot.connections.default']) && $key === $app['spot.connections.default']) {
                    $default = true;
                } else {
                    $default = false;
                }

                $config->addConnection($key, $connections[$key], $default);
            }

            return $config;
        });
    }
}
