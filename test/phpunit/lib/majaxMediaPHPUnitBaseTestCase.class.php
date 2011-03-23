<?php

class majaxMediaPHPUnitBaseTestCase extends sfPHPUnitBaseTestCase
{
  protected function getFileHelper()
  {
    return new majaxMediaPHPUnitFileHelper();
  }

  protected function getMockCommandExecuter()
  {
    $exec = $this->getMockBuilder('majaxMediaCommandExecuter')
                  ->setMethods(array('setExecutable', 'setArguments', 'execute'))
                  ->setConstructorArgs(array(''))
                  ->getMock();

    $exec->expects($this->any())
          ->method('setExecutable')
          ->with($this->anything());

    $exec->expects($this->any())
          ->method('setArguments')
          ->with($this->anything());

    $exec->expects($this->any())
          ->method('execute')
          ->will($this->returnValue(true));

    return $exec;
  }
}

class majaxMediaPHPUnitFileHelper extends majaxMediaFileHelper
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
