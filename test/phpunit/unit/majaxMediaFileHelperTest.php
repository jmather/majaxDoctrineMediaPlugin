<?php

require_once dirname(__FILE__) . '/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaFileHelperTest extends sfPHPUnitBaseTestCase
{
  protected $file_helper = null;
  protected $file = null;
  protected $file_lock = null;

  protected function setUp()
  {
    $this->file_helper = new majaxMediaFileHelper(array('lock_wait_time' => 2));
    $this->file = tempnam('/tmp', 'majaxMediaFileHelperTest');
    $this->file_lock = $this->file . '.lock';
  }

  public function test_LockFileWait()
  {
    $before_time = time();
    touch($this->file_lock);
    $write_result = $this->file_helper->write($this->file, 'test');
    $after_time = time();
    $this->assertEquals($write_result, true, '->write returned true');
    $this->assertNotEquals($before_time, $after_time, '->write waited for lock to release');
    $cont = $this->file_helper->read($this->file);
    $this->assertEquals($cont, 'test');
  }

  public function tearDown()
  {
    if (file_exists($this->file))
      unlink($this->file);
    if (file_exists($this->file_lock))
      unlink($this->file_lock);
  }
}
