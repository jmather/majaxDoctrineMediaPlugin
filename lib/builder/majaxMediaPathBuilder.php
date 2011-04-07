<?php

class majaxMediaPathBuilder
{
  protected $media_path = null;
  public function __construct($media_path = null)
  {
    if ($media_path == null) 
      $this->media_path = sfConfig::get('app_majax_media_cache_dir');
    else
      $this->media_path = $media_path;
  }

  public function setMediaPath($media_path)
  {
    $this->media_path = $media_path;
  }

  public function render($file_info)
  {
    $name = $file_info->getName();
    $sha1 = $file_info->getSha1();

    $partial_path = $this->getPartialPathFromSHA1($sha1);
    $path = $this->media_path.$partial_path.DIRECTORY_SEPARATOR.$name;
    return $path;
  }

  protected function getPartialPathFromSHA1($sha1)
  {
    return DIRECTORY_SEPARATOR.wordwrap($sha1, 2, DIRECTORY_SEPARATOR, true);
  }
}
