<?php

class majaxMediaFileHelperStub extends majaxMediaFileHelper
{
  public $locks = null;
  public $files = null;

  public function __construct($user_settings = array())
  {
    parent::__construct($user_settings);
    $this->locks = array();
    $this->files = array();
  }

  public function write($file, $contents, $wait = null)
  {
    $this->files[$file] = $contents;
    return true;
  }

  public function read($file, $wait = null)
  {
    if (isset($this->files[$file]))
      return $this->files[$file];
    return false;
  }

  public function hasFileLock($file, $wait = null)
  {
    if (isset($this->locks[$file]))
      return true;
    return false;
  }

  public function getFileLock($file, $wait = null)
  {
    if ($this->hasFileLock($file, $wait))
      return false;
    $this->locks[$file] = true;
    return true;
  }

  public function removeFileLock($file)
  {
    unset($this->locks[$file]);
  }

  public function exists($file)
  {
    if (isset($this->files[$file]))
      return true;
    return false;
  }
}
