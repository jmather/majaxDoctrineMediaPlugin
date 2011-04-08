<?php

require_once dirname(__FILE__) . '/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaDimensionCalculatorTest extends sfPHPUnitBaseTestCase
{
  protected $calc = null;

  protected function setUp()
  {
    $this->calc = new majaxMediaDimensionCalculator();
  }

  /**
   * @dataProvider CalculatorProvider
   */
  public function testCalculator($src_width, $src_height, $new_width, $new_height, $aspect_ratio, $result)
  {
    $fn = $this->calc->calculate($src_width, $src_height, $new_width, $new_height, $aspect_ratio);
    $this->assertEquals($fn, $result);
  }

  public function CalculatorProvider()
  {
    return array(
      array(100, 100, 10, 10, '4:3', array(10, 10)),
      array(100, 100, 10, null, '4:3', array(10, 8)),
      array(100, 100, null, 10, '4:3', array(14, 10)),
      array(100, 100, null, null, '4:3', array(100, 76)),
      array(400, 300, 100, null, 'auto', array(100, 76)),
      array(1920, 1080, 608, null, '16:9', array(608, 342)),
      array(1920, 1080, 608, null, 'auto', array(608, 342)),
    );
  }
}
