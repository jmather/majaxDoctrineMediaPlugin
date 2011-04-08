<?php

require_once dirname(__FILE__) . '/../../../../../test/phpunit/bootstrap/unit.php';
require_once dirname(__FILE__) . '/../lib/majaxMediaPHPUnitBaseTestCase.php';

class unit_majaxMediaFFMpegTest extends majaxMediaPHPUnitBaseTestCase
{
  protected $builder = null;

  protected function setUp()
  {
    $this->ffmpeg = new majaxMediaFFMpeg();
  }

  public function test_FFMpegCommandExecution()
  {
    $exec = $this->getMockCommandExecutor();

    $exec->expects($this->once())
      ->method('setExecutable')
      ->with($this->stringContains('/ffmpeg'));

    $arg_array = array(
      '-i',
      DIRECTORY_SEPARATOR . 'aa' . DIRECTORY_SEPARATOR . 'bb' . DIRECTORY_SEPARATOR . 'file.mov',
      '-ar',
      '22050',
      '-b',
      '409600',
      '-s',
      '20x10',
      DIRECTORY_SEPARATOR . 'aa' . DIRECTORY_SEPARATOR . 'bb' . DIRECTORY_SEPARATOR . '20x10_fit_file.flv'
    );


    $exec->expects($this->once())
      ->method('setArguments')
      ->with($arg_array);

    $exec->expects($this->once())
      ->method('execute')
      ->will($this->returnValue(true));

    $file_helper = $this->getFileHelper();

    // this is so the file helper believes ffmpeg exists...
    $file_helper->write('/usr/local/bin/ffmpeg', 'execute me');

    $this->ffmpeg->setExecutor($exec);

    $this->ffmpeg->setFileHelper($file_helper);

    $file_info = $this->getMockBuilder('majaxMediaFileInfoInterface')
      ->setMethods(array('getData', 'getName', 'getSha1', 'getHeight', 'getWidth', 'getType', 'getMime', 'getLength', 'getSize'))
      ->disableOriginalConstructor()
      ->getMock();

    $file_info->expects($this->any())
      ->method('getData')
      ->will($this->returnValue('data'));

    $file_info->expects($this->any())
      ->method('getName')
      ->will($this->returnValue('file.mov'));

    $file_info->expects($this->any())
      ->method('getSha1')
      ->will($this->returnValue('aabb'));

    $file_info->expects($this->any())
      ->method('getHeight')
      ->will($this->returnValue(100));

    $file_info->expects($this->any())
      ->method('getWidth')
      ->will($this->returnValue(200));

    $file = $this->ffmpeg->process($file_info, 20, 10, 'fit', '16:9');

    $this->assertEquals($file_helper->locks, array());

    $this->assertEquals($file, DIRECTORY_SEPARATOR . 'aa' . DIRECTORY_SEPARATOR . 'bb' . DIRECTORY_SEPARATOR . '20x10_fit_file.flv');
  }
}
