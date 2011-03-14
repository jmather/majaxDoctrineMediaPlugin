<?php

require_once dirname(__FILE__).'/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaFilenameBuilderTest extends sfPHPUnitBaseTestCase
{
  protected $builder = null;

  protected function setUp()
  {
    $this->builder = new majaxMediaFilenameBuilder();
  }

  /**
   * @dataProvider testPlainFilenameGenerationProvider
   */
  public function testPlainFilenameGeneration($width, $height, $crop_method, $extension, $result)
  {
    $fn = $this->builder->render($width, $height, $crop_method, $extension);
    $this->assertEquals($fn, $result);
  }

  public function testPlainFilenameGenerationProvider()
  {
    return array(
      array('100', '100', 'center', 'blah.gif', '100x100_center_blah.gif'),
      array('29', '320934', 'fit', 'blah.flv', '29x320934_fit_blah.flv'),
    );
  }  
}
