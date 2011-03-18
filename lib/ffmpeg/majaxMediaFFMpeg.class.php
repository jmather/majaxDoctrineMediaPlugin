<?php

class majaxMediaFFMpeg
{
  protected $file_info = null;

  protected $filename_builder = null;
  protected $path_builder = null;
  protected $cmd_line_builder = null;
  protected $executer = null;
  protected $file_helper = null;

  public function __construct()
  {
    $fb_class = sfConfig::get('app_majax_media_filename_builder', 'majaxMediaFilenameBuilder');
    $this->filename_builder = new $fb_class();
    $pb_class = sfConfig::get('app_majax_media_path_builder', 'majaxMediaPathBuilder');
    $this->path_builder = new $pb_class();
    $clb_class = sfConfig::get('app_majax_media_cmd_line_builder', 'majaxMediaFFMpegVideoTransformationBuilder');
    $this->cmd_line_builder = new $clb_class();
    $executer_class = sfConfig::get('app_majax_media_executer', 'majaxMediaCommandExecuter');
    $this->executer = new $executer_class('');
    $file_helper_class = sfConfig::get('app_majax_media_file_helper', 'majaxMediaFileHelper');
    $this->file_helper = new $file_helper_class();
  }
  
  public function setFilenameBuilder(majaxMediaFilenameBuilder $fnb)
  {
    $this->filename_builder = $fnb;
  }
  
  public function setPathBuilder(majaxMediaPathBuilder $pb)
  {
    $this->path_builder = $pb;
  }
  public function setCMDLineBuilder(majaxMediaPathBuilder $clb)
  {
    $this->cmd_line_builder = $clb;
  }
  public function setExecuter(majaxMediaCommandExecuter $e)
  {
    $this->executor = $e;
  }
  public function setFileHelper(majaxMediaFileHelper $fh)
  {
    $this->file_helper = $fh;
  }
  public function process(majaxMediaFileInfo $file_info, $new_width = null, $new_height = null, $crop_method = 'fit', $aspect_ratio = '16:9')
  {
    $name = $file_info->getName();
    $sha1 = $file_info->getSha1();
    $path = $this->path_builder->render($sha1);
    $full_path = sfConfig::get('app_majax_media_cache_dir').DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$name;
    if (!file_exists(sfConfig::get('app_majax_media_cache_dir').DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$name))
    {
      $this->ensurePath($path, sfConfig::get('app_majax_media_cache_dir'));
      $this->file_helper->write($full_path, $file_info->getData());
    }

    $src_width = $file_info->getVideoWidth();
    $src_height = $file_info->getVideoHeight();

    if ($new_width !== null || $new_height !== null)
    {
      list($new_width, $new_height) = $this->getRatioDimensions($src_width, $src_height, $new_width, $new_height, $aspect_ratio);
    } else {
      list($new_width, $new_height) = array($src_width, $src_height);
    }


    $args = array('-i', $full_path, '-ar', '22050', '-b', '409600');


    $translator_class = sfConfig::get('app_majax_media_video_transformation_builder', 'majaxMediaFFMpegVideoTransformationBuilder');
    $translator_fit_class = sfConfig::get('app_majax_media_video_transformation_fit_builder', 'majaxMediaFFMpegVideoTransformationFitBuilder');

    if ($c_m == 'fit')
    {
      $translator = new $translator_fit_class();
      list($new_width, $new_height) = $translator->render($s_w, $s_h, $new_width, $new_height, $c_m);
    } else {
      $translator = new $translator_class();
      $new_args = $translator->render($s_w, $s_h, $new_width, $new_height, $c_m);
      $args = array_merge($args, $new_args);
    }


    $new_width = (ceil($new_width / 2) * 2);
    $new_height = (ceil($new_height / 2) * 2);

    $new_filename = $this->filename_builder->render($new_width, $new_height, $crop_method, $name, 'flv');

    $new_partial_path = $path.DIRECTORY_SEPARATOR.$new_filename;


    // start the transformation code...

    $args[] = '-s';
    $args[] = $new_width.'x'.$new_height;

    $ffmpeg = sfConfig::get('app_majax_media_ffmpeg_path', '/usr/bin/ffmpeg');
    // now we need to figure out the cropping/padding


    $new_full_path = sfConfig::get('app_majax_media_cache_dir').DIRECTORY_SEPARATOR.$new_partial_path;
    $args[] = $new_full_path;


    if ($ffmpeg == false || !file_exists($ffmpeg))
    {
      trigger_error('FFMPEG Not installed. Video source will not be resized', E_WARNING);
      $new_partial_path = $path.DIRECTORY_SEPARATOR.$name;
    }


    if (($ffmpeg != false && file_exists($ffmpeg)) && !file_exists($new_full_path))
    {
      foreach ($args as $i => $arg)
        $args[$i] = escapeshellarg ($arg);
    
      //echo($ffmpeg.' '.join(' ', $args));
      $count = 0;
      while ($this->file_helper->hasFileLock($full_path, false) || $this->file_helper->hasFileLock($new_full_path, false) == false)
      {
        usleep(500);
        $count++;
        if ($count == 10)
          break;
      }

      if (!$this->file_helper->hasFileLock($full_path) && $this->file_helper->getFileLock($new_full_path))
      {
        $this->executor->setExecutable($ffmpeg);
        $this->executor->setArguments($args);
        $this->executor->execute();
        $this->file_helper->removeFileLock($new_full_path);
      }
    }


    $new_partial_path = '/'.sfConfig::get('majax_media_dir_name').'/'.$new_partial_path;



    if ($path_only)
      return $new_partial_path;


    $render_class = sfConfig::get('app_majaxMedia_video_render', 'majaxMediaVideoRender');
    $render = new $render_class();
    return $render->render($this, $new_partial_path);
  }

  /**
   * @param $path (presumed non-existant)
   * @param $base (presumed existant)
   * @return void
   */

  protected function ensurePath($path, $base = '')
  {
    $dirs = explode(DIRECTORY_SEPARATOR, $path);
    $dir = $base;
    foreach($dirs as $c_dir)
    {
      $dir .= '/'.$c_dir;
      if (!file_exists($dir))
      {
        @mkdir($dir);
      }
    }
    if (file_exists($base.$path) && is_dir($base.$path))
    {
      return true;
    }
    return false;
  }

  public function getRatioDimensions($source_width = 16, $source_height = 9, $new_width = null, $new_height = null, $aspect_ratio = 'auto')
  {
    if ($new_width == null)
    {
      if ($aspect_ratio == 'auto')
      {
        $new_width = round($new_height * $source_width / $source_height);
      } else {
        list($aw, $ah) = explode(':', $aspect_ratio, 2);
        $new_width = round($new_height * $aw / $ah);
      }
    }
    if ($new_height == null)
    {
      if ($aspect_ratio == 'auto')
      {
        $new_height = round($new_width * $source_height / $source_width);
      } else {
        list($aw, $ah) = explode(':', $aspect_ratio, 2);
        $new_height = round($new_width * $ah / $aw);
      }
    }
    return array($new_width, $new_height);
  }
}