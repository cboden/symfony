<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\SessionStorage;

use Symfony\Component\HttpFoundation\AttributeBagInterface;
use Symfony\Component\HttpFoundation\FlashBagInterface;

/**
 * NativeMemcachedSessionStorage.
 *
 * Session based on native PHP memcached database handler.
 *
 * @author Drak <drak@zikula.org>
 */
class NativeMemcachedSessionStorage extends AbstractSessionStorage
{
    /**
     * @var string
     */
    private $savePath;

    /**
     * Constructor.
     *
     * @param string                $savePath   Comma separated list of servers: e.g. memcache1.example.com:11211,memcache2.example.com:11211
     * @param array                 $options    Session configuration options.
     * @param AttributeBagInterface $attributes An AttributeBagInterface instance, (defaults null for default AttributeBag)
     * @param FlashBagInterface     $flashes    A FlashBagInterface instance (defaults null for defaul FlashBag)
     *
     * @see AbstractSessionStorage::__construct()
     */
    public function __construct($savePath = '127.0.0.1:11211', array $options = array(), AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
    {
        if (!session_module_name('memcached')) {
            throw new \RuntimeException('PHP does not have "memcached" session module registered');
        }

        $this->savePath = $savePath;
        parent::__construct($attributes, $flashes, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function registerSaveHandlers()
    {
        ini_set('session.save_handlers', 'memcached');
        ini_set('session.save_path', $this->savePath);
    }

    /**
     * {@inheritdoc}
     *
     * Sets any values memcached ini values.
     *
     * @see https://github.com/php-memcached-dev/php-memcached/blob/master/memcached.ini
     */
    protected function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (in_array($key, array(
                'memcached.sess_locking', 'memcached.sess_lock_wait',
                'memcached.sess_prefix', 'memcached.compression_type',
                'memcached.compression_factor', 'memcached.compression_threshold',
                'memcached.serializer'))) {
                ini_set($key, $value);
            }
        }

        parent::setOptions($options);
    }
}
