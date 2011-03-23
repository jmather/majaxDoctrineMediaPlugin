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
   * @dataProvider PlainFilenameGenerationProvider
   */
  public function testPlainFilenameGeneration($width, $height, $crop_method, $name, $extension, $result)
  {
    $fn = $this->builder->render($width, $height, $crop_method, $name, $extension);
    $this->assertEquals($fn, $result);
  }

  public function PlainFilenameGenerationProvider()
  {
    return array(
      array('/media/blah.gif', '100', '100', 'center', null, '/media/100x100_center_blah.gif'),
      array('/media2/blah.flv', '29', '320934', 'fit', null, '/media2/29x320934_fit_blah.flv'),
      array('/media3/blah.gif', '100', '100', 'center', 'jpg', '/media3/100x100_center_blah.jpg'),
      array('/media4/blah.mov', '29', '320934', 'fit', 'flv', '/media4/29x320934_fit_blah.flv'),
    );
  }  
}
