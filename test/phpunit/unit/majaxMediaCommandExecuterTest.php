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
   * @dataProvider ExecuterBuildsCorrectlyProvider
   */
  public function test_ExecuterBuildsCorrectly($exec, $arguments, $result)
  {
    if (!file_exists($exec))
    {
      $this->markTestSkipped($exec.' does not exist, therefor the test will fail.');
    } else {
      $this->exec->clearArguments();
      $this->exec->setExecutable($exec);
      $this->exec->setArguments($arguments);
      $this->assertEquals($this->exec->execute(), $result);
    }
  }
  public function ExecuterBuildsCorrectlyProvider()
  {
    // one for windows, one for linux
    $execs = array(
      '*nix' => '/bin/echo',
      'Windows' => 'C:\\Windows\\Cmd.exe',
    );
    foreach($execs as $exec)
    {
      if (file_exists($exec))
      {
        return array(
          array($exec, array(), $exec),
          array($exec, array('1'), $exec.' \'1\''),
          array($exec, array('1 1'), $exec.' \'1 1\''),
          array($exec, array('\''), $exec.' \'\'\\\'\'\''),
        );
      }
    }
    
    // No os that the test supports exists.
    return array(array('', array(), ''));
  }
}

class majaxMediaCommandExecuterMock extends majaxMediaCommandExecuter
{
  protected function doExecute($line)
  {
    return $line;
  }
}
