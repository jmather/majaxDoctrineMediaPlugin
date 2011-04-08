<?php
/**
 * User: jmather
 * Date: 4/7/11
 * Time: 7:28 AM
 */

interface majaxMediaFileInfoInterface
{
  public function getName();

  public function getData();

  public function getSize();

  public function getWidth();

  public function getHeight();

  public function getLength();

  public function getMime();

  public function getSha1();

  public function getType();
}
