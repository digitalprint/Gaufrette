<?php

namespace Gaufrette\Adapter;
use Gaufrette\Adapter;

/**
 * Die Konfiguration sollte woanders liegen.
 * Die AMAZON_CACHE_CLUSTER Adresse ist der Endpoint des Clusters, nicht der Nodes
 */
//define(AMAZON_CACHE_CLUSTER, "ppapicache.uuv8rv.0001.euw1.cache.amazonaws.com");
define(AMAZON_CACHE_CLUSTER, "ppapicache.uuv8rv.cfg.euw1.cache.amazonaws.com");
define(AMAZON_CACHE_PORT, 11211);


/**
 * Gaufrette Memcached Adapter
 */
class Memcached implements Adapter, MetadataSupporter
{
  protected	$memcached;

	/**
	 * Konstruktion
	 * @param $server
	 * @param $port
	 */

	public function __construct($persitent_id = "")
	{
		if(strlen($persitent_id) == 0)
		{
			$this->memcached = new \Memcached();
		}
		else
		{
			$this->memcached = new \Memcached($persistent_id);
		}

		/**
		 * DYNAMIC_CLIENT_MODE findet die Amazon Nodes automatisch
		 */
		$this->memcached->setOption(Memcached::OPT_CLIENT_MODE, Memcached::DYNAMIC_CLIENT_MODE);

		/**
		 * Es wird nur der Endpoint des Cluster hinzugefügt
		 */
		$this->memcached->addServer(AMAZON_CACHE_CLUSTER, AMAZON_CACHE_PORT);
	}

	/**
	 * @param string $key
	 * @param null $cache_cb
	 * @param float $cas_token
	 * @return bool|mixed|string
	 */
	public function read($key, $cache_cb = NULL, float &$cas_token = NULL)
	{
		$result = $this->memcached->get($key, $cache_cb, $cas_token);

		return $result;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @param int $expiration
	 * @return bool|int
	 */
	public function write($key, $value, $expiration = 0)
	{
		$result = $this->memcached->set($key, $value, $expiration);

		return $result;
	}

	/**
	 * Indicates whether the file exists
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key)
	{
		$result = $this->memcached->get($key);

		if($result === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Returns an array of all keys (files and directories)
	 * @return array
	 */
	public function keys()
	{
		$result = array();
		return $result;
	}

	/**
	 * Returns the last modified time
	 * @param string $key
	 * @return integer|boolean An UNIX like timestamp or false
	 */
	public function mtime($key)
	{
		return false;
	}

	/**
	 * Deletes the file
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key)
	{
		return $this->memcached->delete($key);
	}

	/**
	 * Renames a file
	 * @param string $sourceKey
	 * @param string $targetKey
	 * @return boolean
	 */
	public function rename($sourceKey, $targetKey)
	{
		$var = $this->memcached->get($sourceKey);
		$this->memcached->set($targetKey, $var);
		$this->memcached->delete($sourceKey);
	}

	/**
	 * Check if key is directory
	 * @param string $key
	 * @return boolean
	 */
	public function isDirectory($key)
	{
		return false;
	}

	/**
	 * @param string $key
	 * @param array $content
	 */
	public function setMetadata($key, $content)
	{
		// TODO: Implement setMetadata() method.
	}

	/**
	 * @param  string $key
	 * @return array
	 */
	public function getMetadata($key)
	{
		// TODO: Implement getMetadata() method.
	}
}
