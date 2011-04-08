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

  /**
   * @param majaxMediaFilenameBuilder $fnb
   * @return void
   */
  public function setFilenameBuilder(majaxMediaFilenameBuilder $fnb)
  {
    $this->filename_builder = $fnb;
  }

  /**
   * @param majaxMediaPathBuilder $pb
   * @return void
   */
  public function setPathBuilder(majaxMediaPathBuilder $pb)
  {
    $this->path_builder = $pb;
  }

  /**
   * @param majaxMediaFileHelper $fh
   * @return void
   */
  public function setFileHelper(majaxMediaFileHelper $fh)
  {
    $this->file_helper = $fh;
  }

  /**
   * @param majaxMediaFileInfoInterface $file_info
   * @param int $new_width
   * @param int $new_height
   * @param string $crop_method
   * @param string $aspect_ratio
   * @param int $extra_width
   * @param int $extra_height
   * @return string
   */
  public function process(majaxMediaFileInfoInterface $file_info, $new_width, $new_height, $crop_method, $aspect_ratio, $extra_width = 0, $extra_height = 0)
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

  /**
   * @abstract
   * @param string $src_path
   * @param int $src_width
   * @param int $src_height
   * @param int $new_width
   * @param int $new_height
   * @param string $crop_method
   * @return string
   */
  abstract protected function execute($src_path, $src_width, $src_height, $new_width, $new_height, $crop_method);

  /**
   * @param majaxMediaFileInfoInterface $file_info
   * @return string
   */
  protected function ensureSourceFileIsCached(majaxMediaFileInfoInterface $file_info)
  {
    $src_path = $this->path_builder->render($file_info);

    $this->file_helper->write($src_path, $file_info->getData());

    return $src_path;
  }
}
