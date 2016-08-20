<?php

namespace Mongo;

use MongoDB\Driver;

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'autoload.php');

class Client {

	protected $db;
	protected $config;
	protected $loader;
	protected $objects;
	private static $_instance = false;

	public function __construct($db, $config) {

		$this->db = $db;
		$this->config = $config;

		$this->loader = new \Mongo\Autoload();
		$this->loader->init();
	}

	public static function instance() {
		if(self::$_instance !== false)
			return self::$_instance;

		$config = false;
		$file = APPPATH . 'Config' . DIRECTORY_SEPARATOR . 'mongo.json';

		try {

			$config = file_get_contents($file);
		}catch(\Exception $e) {

			throw new Exception('No config');
			
		}catch(\Error $e) {

			throw new Exception('No config');
		}

		$config = json_decode($config);

		if(is_null($config))
			throw new Exception('No valid json');

		$dsn = self::getDsn($config);
		$manager = self::createConnection($dsn, $config);

		self::$_instance = new \Mongo\Client($manager, $config);

		return self::$_instance;
	}

	protected static function getDsn($config) {

        return 'mongodb://' . $config->host . ':' . $config->port;
    }

    protected static function createConnection($dsn, $config) {

        return $manager = new Driver\Manager($dsn);;
    }

    public function __call($name, $args) {

    	$class = $this->loader->findClass($name);

    	if($class === false)
    		return false;

    	return $this->loader->execute($class, $name, $args);
    }
}