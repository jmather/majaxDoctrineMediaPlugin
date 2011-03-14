<?php

require_once dirname(__FILE__).'/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaFFMpegVideoTransformationFitBuilderTest extends sfPHPUnitBaseTestCase
{
  protected $builder = null;

  protected function setUp()
  {
    $this->builder = new majaxMediaFFMpegVideoTransformationFitBuilder();
  }

  /**
   * @dataProvider buildRenderProvider
   * @depends testBuildRatio
   */
  public function testRender($source_width, $source_height, $new_width, $new_height, $crop_method, $result)
  {
    $this->assertEquals($this->builder->render($source_width, $source_height, $new_width, $new_height, $crop_method), $result);
  }

  public function buildRenderProvider()
  {
    return array(
      array(3, 1, 1, 1, 'fit', array(1, 1)),
      array(1, 1, 3, 1, 'fit', array(1, 1)),
      array(200, 100, 10, 10, 'fit', array(10, 5)),
      array(100, 200, 10, 10, 'fit', array(5, 10)),
    );
  }

}
