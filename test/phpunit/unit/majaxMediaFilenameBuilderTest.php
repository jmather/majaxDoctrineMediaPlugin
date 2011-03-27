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
      array(DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'blah.gif', '100', '100', 'center', null, DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'100x100_center_blah.gif'),
      array(DIRECTORY_SEPARATOR.'media2'.DIRECTORY_SEPARATOR.'blah.flv', '29', '320934', 'fit', null, DIRECTORY_SEPARATOR.'media2'.DIRECTORY_SEPARATOR.'29x320934_fit_blah.flv'),
      array(DIRECTORY_SEPARATOR.'media3'.DIRECTORY_SEPARATOR.'blah.gif', '100', '100', 'center', 'jpg', DIRECTORY_SEPARATOR.'media3'.DIRECTORY_SEPARATOR.'100x100_center_blah.jpg'),
      array(DIRECTORY_SEPARATOR.'media4'.DIRECTORY_SEPARATOR.'blah.mov', '29', '320934', 'fit', 'flv', DIRECTORY_SEPARATOR.'media4'.DIRECTORY_SEPARATOR.'29x320934_fit_blah.flv'),
    );
  }  
}
