<?php

require_once dirname(__FILE__).'/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaPathBuilderTest extends sfPHPUnitBaseTestCase
{
  protected $builder = null;

  protected function setUp()
  {
    $this->builder = new majaxMediaPathBuilder('/media');
  }

  /**
   * @dataProvider PathGenerationProvider
   */
  public function testPathGeneration($file_info, $media_path, $result)
  {
    $this->builder->setMediaPath($media_path);
    $fn = $this->builder->render($file_info);
    $this->assertEquals($fn, $result);
  }

  public function PathGenerationProvider()
  {
    $file_info_1 = $this->PathGenerationProviderMockBuilder('test.m4v', 'aabb');
    $file_info_2 = $this->PathGenerationProviderMockBuilder('blah.flv', 'ccdd11');
    return array(
      array($file_info_1, '/media', '/media/aa/bb/test.m4v'),
      array($file_info_2, '/media2', '/media2/cc/dd/11/blah.flv'),
    );
  }

  protected function PathGenerationProviderMockBuilder($name, $sha1)
  {
    $file_info = $this->getMockBuilder('majaxMediaFileInfo')
                      ->disableOriginalConstructor()
                      ->setMethods(array('getName', 'getSha1'))
                      ->getMock();

    $file_info->expects($this->any())
                ->method('getName')
                ->will($this->returnValue($name));
    $file_info->expects($this->any())
                ->method('getSha1')
                ->will($this->returnValue($sha1));
    return $file_info;
  }
}
