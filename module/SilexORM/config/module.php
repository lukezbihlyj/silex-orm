<?php

/**
 * Specify application-specific configuration. These settings can be over-ridden
 * by the local environmental settings, so it's safe to specify default values
 * here.
 */
return [
    /**
     * Define how we should connect to the database. The DSN contains everything
     * that Spot needs to know in order to determine which driver to use and
     * what database to load.
     */
    'database.dsn' => null,

    /**
     * Define the entities that should be loaded by Spot during the database
     * migrations.
     */
    'database.entities' => [],

    /**
     * Define a list of commands that should be added to the console on initialisation.
     */
    'console.commands' => [
        'LukeZbihlyj\SilexORM\Console\DatabaseMigrateCommand'
    ],
];
