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
      array($file_info_1, DIRECTORY_SEPARATOR.'media', DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'aa'.DIRECTORY_SEPARATOR.'bb'.DIRECTORY_SEPARATOR.'test.m4v'),
      array($file_info_2, DIRECTORY_SEPARATOR.'media2', DIRECTORY_SEPARATOR.'media2'.DIRECTORY_SEPARATOR.'cc'.DIRECTORY_SEPARATOR.'dd'.DIRECTORY_SEPARATOR.'11'.DIRECTORY_SEPARATOR.'blah.flv'),
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
