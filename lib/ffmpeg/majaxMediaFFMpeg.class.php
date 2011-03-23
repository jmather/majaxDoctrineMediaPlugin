<?php

class majaxMediaFFMpeg
{
  protected $file_info = null;

  protected $filename_builder = null;
  protected $path_builder = null;
  protected $cmd_line_builder = null;
  protected $executer = null;
  protected $file_helper = null;
  protected $ffmpeg_path = null;

  public function __construct($ffmpeg_path = null)
  {
    $fb_class = sfConfig::get('app_majax_media_filename_builder', 'majaxMediaFilenameBuilder');
    $this->filename_builder = new $fb_class();

    $pb_class = sfConfig::get('app_majax_media_path_builder', 'majaxMediaPathBuilder');
    $this->path_builder = new $pb_class(sfConfig::get('app_majax_media_cache_dir'));

    $clb_class = sfConfig::get('app_majax_media_cmd_line_builder', 'majaxMediaFFMpegVideoTransformationBuilder');
    $this->cmd_line_builder = new $clb_class();

    $executer_class = sfConfig::get('app_majax_media_executer', 'majaxMediaCommandExecuter');
    $this->executer = new $executer_class('');

    $file_helper_class = sfConfig::get('app_majax_media_file_helper', 'majaxMediaFileHelper');
    $this->file_helper = new $file_helper_class();

    if ($ffmpeg_path == null)
    {
      $this->ffmpeg_path = sfConfig::get('app_majax_media_ffmpeg_path', '/usr/local/bin/ffmpeg');
    } else {
      $this->ffmpeg_path = $ffmpeg_path;
    }
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

  public function ensureSourceFileIsCached($file_info)
  {
    $src_path = $this->path_builder->render($file_info);

    $this->file_helper->write($src_path, $file_info->getData());

    return $src_path;
  }

  public function process(majaxMediaFileInfo $file_info, $new_width = null, $new_height = null, $crop_method = 'fit', $aspect_ratio = '16:9')
  {
    $src_path = $this->ensureSourceFileIsCached($file_info);

    $src_width = $file_info->getWidth();
    $src_height = $file_info->getHeight();

    list($new_width, $new_height) = $this->calculateDimensions($src_width, $src_height, $new_width, $new_height, $aspect_ratio);

    $file_path = $this->executeFFMpeg($src_path, $src_width, $src_height, $new_width, $new_height, $crop_method);

    return $file_path;
  }

  public function calculateDimensions($src_width, $src_height, $new_width, $new_height, $aspect_ratio)
  {
    // If width or height is omitted, we need to calculate them.
    if ($new_width !== null || $new_height !== null)
    {
      list($new_width, $new_height) = $this->getRatioDimensions($src_width, $src_height, $new_width, $new_height, $aspect_ratio);
    } else {
      list($new_width, $new_height) = array($src_width, $src_height);
    }


    // FFMpeg likes things to be even.
    $new_width = (ceil($new_width / 2) * 2);
    $new_height = (ceil($new_height / 2) * 2);

    return array($new_width, $new_height);
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

  protected function executeFFMpeg($src_path, $src_width, $src_height, $new_width, $new_height, $crop_method)
  {
    // Every incantation is going to need these.
    // TODO: Make the audio ratio and bitrate configurable.
    $args = array('-i', $src_path, '-ar', '22050', '-b', '409600');


    // start the transformation code...

    // Let's tell it about the size we want it to be...
    $args[] = '-s';
    $args[] = $new_width.'x'.$new_height;


    // Now let's get our translation commands.
    $new_args = $this->cmd_line_builder->render($src_width, $src_height, $new_width, $new_height, $crop_method);
    $args = array_merge($args, $new_args);


    // Our file name and path
    $new_path = $this->filename_builder->render($src_path, $new_width, $new_height, $crop_method, 'flv');

    $args[] = $new_path;


    if ($this->ffmpeg_path == false || !$this->file_helper->exists($this->ffmpeg_path))
    {
      trigger_error('FFMPEG Not installed. Video source will not be resized', E_USER_WARNING);
      $new_path = $src_path;
    }


    if (($this->ffmpeg_path != false && $this->file_helper->exists($this->ffmpeg_path)) && !$this->file_helper->exists($new_path))
    {
      // Let's make sure we have a lock on our destination file, and that there is no lock on our source file
      $count = 0;
      while ($this->file_helper->hasFileLock($src_path, false) || $this->file_helper->hasFileLock($new_path, false))
      {
        usleep(500);
        $count++;
        if ($count == 10)
          break;
      }

      if (!$this->file_helper->hasFileLock($src_path) && $this->file_helper->getFileLock($new_path))
      {
        $this->executor->setExecutable($this->ffmpeg_path);
        $this->executor->setArguments($args);
        $this->executor->execute();
        $this->file_helper->removeFileLock($new_path);
      }
    }
    return $new_path;
  }
}
