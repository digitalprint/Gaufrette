<?php

$memCached = new \Memcached();

//$memCached->setOption(\Memcached::OPT_CLIENT_MODE, \Memcached::DYNAMIC_CLIENT_MODE);
$memCached->addServer("127.0.0.1", 11211);

$keys = $memCached->getAllKeys();

$memCached->deleteMulti($keys);

return new Gaufrette\Adapter\Memcached($memCached);
