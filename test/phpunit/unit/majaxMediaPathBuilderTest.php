<?php

require_once dirname(__FILE__).'/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaPathBuilderTest extends sfPHPUnitBaseTestCase
{
  protected $builder = null;

  protected function setUp()
  {
    $this->builder = new majaxMediaPathBuilder();
  }

  /**
   * @dataProvider testPathGenerationProvider
   */
  public function testPathGeneration($hash, $result)
  {
    $fn = $this->builder->render($hash);
    $this->assertEquals($fn, $result);
  }

  public function testPathGenerationProvider()
  {
    return array(
      array('aabbccdd', '/aa/bb/cc/dd'),
      array('abcd', '/ab/cd'),
    );
  }  
}
