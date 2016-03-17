<?php

namespace LukeZbihlyj\SilexORM;

use LukeZbihlyj\SilexPlus\Application;
use LukeZbihlyj\SilexPlus\ModuleInterface;
use Dijky\Silex\Provider\SpotServiceProvider;

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
        $app->register(new SpotServiceProvider(), [
            'spot.connections' => [
                'main' => $app['database.dsn']
            ]
        ]);
    }
}
