<?php

require_once dirname(__FILE__).'/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaCommandExecuterTest extends sfPHPUnitBaseTestCase
{
  protected $exec = null;

  protected function setUp()
  {
    $this->exec = new majaxMediaCommandExecuterMock('');
  }

  /**
   * @expectedException InvalidArgumentException
   * @dataProvider test_ExecuterExceptionsWhenItShouldProvider
   */
  public function test_ExecuterExceptionsWhenItShould($file, $arguments)
  {
    $this->exec->setExecutable($file);
    $this->exec->setArguments($arguments);
  }
  public function test_ExecuterExceptionsWhenItShouldProvider()
  {
    return array(
      array(__FILE__.'.probably_doesnt_exist', array()),
      array(__FILE__, array()),
      array('/bin/echo', ''),
    );
  }

  /**
   * @dataProvider test_ExecuterBuildsCorrectlyProvider
   */
  public function test_ExecuterBuildsCorrectly($exec, $arguments, $result)
  {
    $this->exec->clearArguments();
    $this->exec->setExecutable($exec);
    $this->exec->setArguments($arguments);
    $this->assertEquals($this->exec->execute(), $result);
  }
  public function test_ExecuterBuildsCorrectlyProvider()
  {
    return array(
      array('/bin/echo', array(), '/bin/echo'),
      array('/bin/echo', array('1'), '/bin/echo \'1\''),
      array('/bin/echo', array('1 1'), '/bin/echo \'1 1\''),
      array('/bin/echo', array('\''), '/bin/echo \'\'\\\'\'\''),
    );
  }
}

class majaxMediaCommandExecuterMock extends majaxMediaCommandExecuter
{
  protected function doExecute($line)
  {
    return $line;
  }
}
