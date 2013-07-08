<?php

namespace Gaufrette\Adapter;
use Gaufrette\Adapter;

/**
 * Gaufrette Memcached Adapter
 */
class Memcached implements Adapter
{

    /**
     * @var \Memcached
     */
    protected	$memCached = null;


    /**
     * @param \Memcached $memCached
     */
	public function __construct(\Memcached $memCached)
	{
        $this->memCached = $memCached;
	}


    /**
     * @param string $key
     * @param null $cache_cb
     * @param float|\Gaufrette\Adapter\float $cas_token
     * @return bool|mixed|string
     */
	public function read($key, $cache_cb = NULL, float &$cas_token = NULL)
	{
		$result = $this->memCached->get($key, $cache_cb, $cas_token);

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

		$result = $this->memCached->set($key, $value, $expiration);

		return $result;
	}


	/**
	 * Indicates whether the file exists
	 * @param string $key
	 * @return boolean
	 */
	public function exists($key)
	{
		$result = $this->memCached->get($key);

		if($result === false) {
			return false;
		}

		return true;
	}


	/**
	 * Returns an array of all keys (files and directories)
	 * @return array
	 */
	public function keys()
	{
		$result = $this->memCached->getAllKeys();
		return $result;
	}


	/**
	 * Returns the last modified time
	 * @param string $key
	 * @return integer|boolean An UNIX like timestamp or false
	 */
	public function mtime($key)
	{
		return time();
	}


	/**
	 * Deletes the file
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key)
	{
		return $this->memCached->delete($key);
	}


	/**
	 * Renames a file
	 * @param string $sourceKey
	 * @param string $targetKey
	 * @return boolean
	 */
	public function rename($sourceKey, $targetKey)
	{
		$var = $this->memCached->get($sourceKey);
		$this->memCached->set($targetKey, $var);
		$result = $this->memCached->delete($sourceKey);
        return $result;
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

}
