<?php

require_once dirname(__FILE__).'/../../../../../test/phpunit/bootstrap/unit.php';

class unit_majaxMediaFFMpegTest extends sfPHPUnitBaseTestCase
{
  protected $builder = null;

  protected function setUp()
  {
    $this->ffmpeg = new majaxMediaFFMpeg();
  }
}
