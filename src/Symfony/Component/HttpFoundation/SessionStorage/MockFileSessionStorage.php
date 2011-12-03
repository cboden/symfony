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
 * MockFileSessionStorage is used to mock sessions for
 * functional testing when done in a single PHP process.
 *
 * No PHP session is actually started since a session can be initialized
 * and shutdown only once per PHP execution cycle.
 *
 * @author Drak <drak@zikula.org>
 */
class MockFileSessionStorage extends ArraySessionStorage
{
    /**
     * @var array
     */
    private $sessionData = array();

    /**
     * @var string
     */
    private $savePath;

    /**
     * Constructor.
     *
     * @param string                $savePath   Path of directory to save session files.
     * @param array                 $options    Session options.
     * @param AttributeBagInterface $attributes An AttributeBagInterface instance, (defaults null for default AttributeBag)
     * @param FlashBagInterface     $flashes    A FlashBagInterface instance (defaults null for default FlashBag)
     *
     * @see AbstractSessionStorage::__construct()
     */
    public function __construct($savePath = null, array $options = array(), AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null)
    {
        if (is_null($savePath)) {
            $savePath = sys_get_temp_dir();
        }

        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }

        $this->savePath = $savePath;

        parent::__construct($attributes, $flashes, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->started) {
            return true;
        }

        if (!session_id()) {
            session_id($this->generateSessionId());
        }

        $this->sessionId = session_id();

        $this->read();

        $this->started = true;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($destroy = false)
    {
        if ($destroy) {
            $this->destroy();
        }

        session_id($this->generateSessionId());
        $this->sessionId = session_id();

        $this->save();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        if (!$this->started) {
            return '';
        }

        return $this->sessionId;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        file_put_contents($this->getFilePath(), serialize($this->sessionData));
    }

    private function destroy()
    {
        if (is_file($this->getFilePath())) {
            unlink($this->getFilePath());
        }
    }

    /**
     * Calculate path to file.
     *
     * @return string File path
     */
    public function getFilePath()
    {
        return $this->savePath . '/' . $this->sessionId . '.sess';
    }

    private function read()
    {
        $filePath = $this->getFilePath();
        $this->sessionData = is_readable($filePath) && is_file($filePath) ? unserialize(file_get_contents($filePath)) : array();

        $key = $this->attributeBag->getStorageKey();
        $this->sessionData[$key] = isset($this->sessionData[$key]) ? $this->sessionData[$key] : array();
        $this->attributeBag->initialize($this->sessionData[$key]);

        $key = $this->flashBag->getStorageKey();
        $this->sessionData[$key] = isset($this->sessionData[$key]) ? $this->sessionData[$key] : array();
        $this->flashBag->initialize($this->sessionData[$key]);
    }

}
