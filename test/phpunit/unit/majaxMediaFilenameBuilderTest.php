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
  public function testPlainFilenameGeneration($width, $height, $crop_method, $name, $extension, $result)
  {
    $fn = $this->builder->render($width, $height, $crop_method, $name, $extension);
    $this->assertEquals($fn, $result);
  }

  public function testPlainFilenameGenerationProvider()
  {
    return array(
      array('100', '100', 'center', 'blah.gif', null, '100x100_center_blah.gif'),
      array('29', '320934', 'fit', 'blah.flv', null, '29x320934_fit_blah.flv'),
      array('100', '100', 'center', 'blah.gif', 'jpg', '100x100_center_blah.jpg'),
      array('29', '320934', 'fit', 'blah.mov', 'flv', '29x320934_fit_blah.flv'),
    );
  }  
}
