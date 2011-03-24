<?php

abstract class majaxMediaProcessor
{
  protected $path_builder = null;
  protected $file_helper = null;
  protected $filename_builder = null;

  public function __construct()
  {
    $pb_class = sfConfig::get('app_majax_media_path_builder', 'majaxMediaPathBuilder');
    $this->path_builder = new $pb_class(sfConfig::get('app_majax_media_cache_dir'));

    $file_helper_class = sfConfig::get('app_majax_media_file_helper', 'majaxMediaFileHelper');
    $this->file_helper = new $file_helper_class();

    $fb_class = sfConfig::get('app_majax_media_filename_builder', 'majaxMediaFilenameBuilder');
    $this->filename_builder = new $fb_class();
  }

  public function setFilenameBuilder(majaxMediaFilenameBuilder $fnb)
  {
    $this->filename_builder = $fnb;
  }

  public function setPathBuilder(majaxMediaPathBuilder $pb)
  {
    $this->path_builder = $pb;
  }

  public function setFileHelper(majaxMediaFileHelper $fh)
  {
    $this->file_helper = $fh;
  }

  public function process(majaxMediaFileInfo $file_info, $new_width, $new_height, $crop_method, $aspect_ratio, $extra_width = 0, $extra_height = 0)
  {
    $src_path = $this->ensureSourceFileIsCached($file_info);

    $src_width = $file_info->getWidth();
    $src_height = $file_info->getHeight();

    list($new_width, $new_height) = $this->dimension_calculator->calculate($src_width, $src_height, $new_width, $new_height, $aspect_ratio);

    // we have this mainly so we can pad images to replace video/audio + flash controller perfectly
    $new_width += $extra_width;
    $new_height += $extra_height;

    $file_path = $this->execute($src_path, $src_width, $src_height, $new_width, $new_height, $crop_method);

    return $file_path;
  }

  abstract protected function execute($src_path, $src_width, $src_height, $new_width, $new_height, $crop_method);

  protected function ensureSourceFileIsCached($file_info)
  {
    $src_path = $this->path_builder->render($file_info);

    $this->file_helper->write($src_path, $file_info->getData());

    return $src_path;
  }
}
