<?php

class majaxMediaFileHelper
{
  protected $read_blocking = true;
  protected $read_lock_wait = true;
  protected $write_lock_wait = true;

  public function __construct($user_settings = array())
  {
    $settings = array(
      'read_blocking' => true,
      'read_lock_wait' => true,
      'write_lock_wait' => true,
    );
    $settings = array_merge($settings, $user_settings);
    $this->read_blocking = $settings['read_blocking'];
    $this->read_lock_wait = $settings['read_lock_wait'];
    $this->write_lock_wait = $settings['write_lock_wait'];
  }

  public function write($file, $contents, $wait = null)
  {
    if ($this->hasFileLock($file))
    {
      $wait = ($wait == null) ? $this->write_lock_wait : $wait;

      if (!$wait)
      {
        return false;
      }
      $elapse = $this->getFileLockTimeout();
      $limit = time() + $elapse;
      while (time() < $limit)
      {
        if (!$this->hasFileLock($file))
        {
          break;
        }
        sleep(1);
      }
      if ($this->hasFileLock($file))
      {
        return false;
      }
    }
    if (!$this->getFileLock($file))
    {
      return false;
    }
    file_put_contents($file, $contents);
    $this->removeFileLock();
  }

  public function read($file, $wait = null)
  {
    if ($this->read_blocking && $this->hasFileLock($file))
    {
      $wait = ($wait == null) ? $this->read_lock_wait : $wait;

      if (!$wait)
      {
        return false;
      }
      $elapse = $this->getFileLockTimeout();
      $limit = time() + $elapse;
      while (time() < $limit)
      {
        if (!$this->hasFileLock($file))
        {
          break;
        }
        sleep(1);
      }
      if ($this->hasFileLock($file))
      {
        return false;
      }
    }
    return file_get_contents($file);
  }

  public static function getLockFile($file)
  {
    return $file.'.lock';
  }

  public static fucntion getFileLockTimeout()
  {
    return sfConfig::get('app_majax_media_lockfile_expiration', 30);
  }

  public static function hasFileLock($file)
  {
    $lock = static::getLockFile($file);
    if (file_exists($lock))
    {
      $mtime = filemtime($lock);
      $elapse = static::getFileLockTimeout();
      $limit = time() - $elapse;
      if ($mtime > $limit)
        return true;
      unlink($lock);
      return false;
    }
    return false;
  }

  public static function getFileLock($file)
  {
    $lock = static::getLockFile($file);
    if (static::hasFileLock($file))
      return false;
    touch($lock);
    return true;
  }

  public static function removeFileLock($file)
  {
    $lock = static::getLockFile($file);
    unlink($lock);
  }
}
