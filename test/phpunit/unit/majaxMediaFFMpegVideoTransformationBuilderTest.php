<?php

require_once dirname(__FILE__).'/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaFFMpegVideoTransformationBuilderTest extends sfPHPUnitBaseTestCase
{
  protected $builder = null;

  protected function setUp()
  {
    $this->builder = new majaxMediaFFMpegVideoTransformationBuilder();
  }

  /**
   * @dataProvider buildRatioProvider
   */
  public function testBuildRatio($source_width, $source_height, $new_width, $new_height, $result)
  {
    $this->assertEquals($this->builder->buildRatio($source_width, $source_height, $new_width, $new_height), $result);
  }

  public function buildRatioProvider()
  {
    return array(
      array(3, 1, 1, 1, 1/3),
      array(1, 1, 3, 1, 1),
      array(200, 100, 10, 10, 0.05),
      array(100, 200, 10, 10, 0.05),
    );
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
      array(3, 1, 1, 1, 'center', array('-cropright', '1', '-cropleft', '1')),
      array(1, 1, 3, 1, 'center', array('-croptop', '1', '-cropbottom', '1')),
      array(200, 100, 10, 10, 'center', array('-cropright', '5', '-cropleft', '5')),
      array(100, 200, 10, 10, 'center', array('-croptop', '5', '-cropbottom', '5')),
      array(3, 1, 1, 1, 'top', array('-cropright', '1', '-cropleft', '1')),
      array(1, 1, 3, 1, 'top', array('-cropbottom', '2')),
      array(200, 100, 10, 10, 'top', array('-cropright', '5', '-cropleft', '5')),
      array(100, 200, 10, 10, 'top', array('-cropbottom', '10')),
      array(3, 1, 1, 1, 'bottom', array('-cropright', '1', '-cropleft', '1')),
      array(1, 1, 3, 1, 'bottom', array('-croptop', '2')),
      array(200, 100, 10, 10, 'bottom', array('-cropright', '5', '-cropleft', '5')),
      array(100, 200, 10, 10, 'bottom', array('-croptop', '10')),
      array(3, 1, 1, 1, 'left', array('-cropright', '2')),
      array(1, 1, 3, 1, 'left', array('-croptop', '1', '-cropbottom', '1')),
      array(200, 100, 10, 10, 'left', array('-cropright', '10')),
      array(100, 200, 10, 10, 'left', array('-croptop', '5', '-cropbottom', '5')),
      array(3, 1, 1, 1, 'right', array('-cropleft', '2')),
      array(1, 1, 3, 1, 'right', array('-croptop', '1', '-cropbottom', '1')),
      array(200, 100, 10, 10, 'right', array('-cropleft', '10')),
      array(100, 200, 10, 10, 'right', array('-croptop', '5', '-cropbottom', '5')),
    );
  }

}
