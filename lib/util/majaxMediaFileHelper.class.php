<?php

class majaxMediaFileHelper
{
  protected $read_blocking = true;
  protected $read_lock_wait = true;
  protected $write_lock_wait = true;
  protected $lock_wait_time = 30;

  public function __construct($user_settings = array())
  {
    $settings = array(
      'read_blocking' => true,
      'read_lock_wait' => true,
      'write_lock_wait' => true,
      'lock_wait_time' => 30,
    );
    $settings = array_merge($settings, $user_settings);
    $this->read_blocking = $settings['read_blocking'];
    $this->read_lock_wait = $settings['read_lock_wait'];
    $this->write_lock_wait = $settings['write_lock_wait'];
    $this->lock_wait_time = $settings['lock_wait_time'];
  }

  public function write($file, $contents, $wait = null)
  {
    $dir = dirname($file);
    $this->ensurePathExists($dir);

    if (!$this->getFileLock($file, $wait))
    {
      return false;
    }
    file_put_contents($file, $contents);
    $this->removeFileLock($file);
    return true;
  }

  public function read($file, $wait = null)
  {
    if ($this->read_blocking)
    {
      if ($this->hasFileLock($file, $wait))
      {
        return false;
      }
    }
    return file_get_contents($file);
  }

  public function getLockFile($file)
  {
    return $file.'.lock';
  }

  public function getFileLockTimeout()
  {
    return $this->lock_wait_time;
  }

  public function hasFileLock($file, $wait = null)
  {
    error_log('hasFileLock '.$file.' '.var_export($wait, true));
    $wait = ($wait === null) ? $this->write_lock_wait : $wait;
    $lock = $this->getLockFile($file);

    if (file_exists($lock))
    {
      $mtime = filemtime($lock);
      $elapse = $this->getFileLockTimeout();
      $wait_limit = time() + $elapse;

      while($wait === true && time() < $wait_limit)
      {
        $limit = time() - $elapse;
        if (!file_exists($lock))
          $wait = false;
      }
      if (file_exists($lock))
      {
        return false;
      }
      unlink($lock);
      return true;
    }
    return false;
  }

  public function getFileLock($file, $wait = null)
  {
    $lock = $this->getLockFile($file);

    if ($this->hasFileLock($file, $wait))
    {
      return false;
    }

    touch($lock);
    return true;
  }

  public function removeFileLock($file)
  {
    $lock = $this->getLockFile($file);
    unlink($lock);
  }

  public function exists($path)
  {
    return file_exists($path);
  }

  public function is_file($path)
  {
    if (file_exists($path) && !is_dir($path))
      return true;
    return false;
  }

  public function is_dir($path)
  {
    if (file_exists($path) && is_dir($path))
      return true;
    return false;
  }

  /**
   * @param $path
   * @return void
   */

  protected function ensurePathExists($path)
  {
    if (file_exists($path) && is_dir($path))
    {
      return true;
    }

    $oldumask = umask(0);
    @mkdir($path, 01777, true);
    umask($oldumask);

    if (file_exists($path) && is_dir($path))
    {
      return true;
    }
    return false;
  }
}
