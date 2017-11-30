<?php

namespace Vinosa\Repo ;

use Vinosa\Repo\Adapters\PdoAdapter ;
use Vinosa\Repo\Adapters\PdoConfiguration ;
use Vinosa\Repo\Schema\DbTable ;
use Vinosa\Repo\Model\GenericEntity ;
use Vinosa\Repo\Tools\Logger ;

require 'vendor/autoload.php';

$config = ["host"  =>  "",
           "user"  =>  "",
           "password"  =>  "",
           "database"  =>  ""
           ];

$configuration = new PdoConfiguration($config) ;

$pdo = new \PDO("mysql:host=" . $configuration->getHost() . ";dbname=" . $configuration->getDatabase(),
                                $configuration->getUser(),
                                $configuration->getPassword()
                                );


$repo = new DbRepository( new PdoAdapter( $pdo ), $configuration->getDatabase(), new Logger(true) );

