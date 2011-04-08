<?php

class majaxMediaFFMpeg extends majaxMediaProcessor
{
  protected $file_info = null;

  protected $cmd_line_builder = null;
  protected $executor = null;
  protected $dimension_calculator = null;
  protected $ffmpeg_path = null;

  public function __construct($ffmpeg_path = null)
  {
    parent::__construct();

    $clb_class = sfConfig::get('app_majax_media_cmd_line_builder', 'majaxMediaFFMpegVideoTransformationBuilder');
    $this->cmd_line_builder = new $clb_class();

    $executor_class = sfConfig::get('app_majax_media_executer', 'majaxMediaCommandExecutor');
    $this->executor = new $executor_class('');

    $dimension_calculator_class = sfConfig::get('app_majax_media_dimension_calculator', 'majaxMediaDimensionCalculator');
    $this->dimension_calculator = new $dimension_calculator_class();

    if ($ffmpeg_path == null) {
      $this->ffmpeg_path = sfConfig::get('app_majax_media_ffmpeg_path', '/usr/local/bin/ffmpeg');
    } else {
      $this->ffmpeg_path = $ffmpeg_path;
    }
  }

  public function setCMDLineBuilder(majaxMediaPathBuilder $clb)
  {
    $this->cmd_line_builder = $clb;
  }

  public function setExecutor(majaxMediaCommandExecutor $e)
  {
    $this->executor = $e;
  }

  public function setDimensionCalculator(majaxMediaDimensionCalculator $dc)
  {
    $this->dimension_calculator = $dc;
  }

  public function setFFMpegPath($path)
  {
    $this->ffmpeg_path = $path;
  }

  protected function execute($src_path, $src_width, $src_height, $new_width, $new_height, $crop_method)
  {
    // Every incantation is going to need these.
    // TODO: Make the audio ratio and bitrate configurable.
    $args = array('-i', $src_path, '-ar', '22050', '-b', '409600');


    // start the transformation code...

    // Let's tell it about the size we want it to be...
    $args[] = '-s';
    $args[] = $new_width . 'x' . $new_height;


    // Now let's get our translation commands.
    $new_args = $this->cmd_line_builder->render($src_width, $src_height, $new_width, $new_height, $crop_method);
    $args = array_merge($args, $new_args);


    // Our file name and path
    $new_path = $this->filename_builder->render($src_path, $new_width, $new_height, $crop_method, 'flv');

    $args[] = $new_path;


    if ($this->ffmpeg_path == false || !$this->file_helper->exists($this->ffmpeg_path)) {
      trigger_error('FFMPEG Not installed. Video source will not be resized', E_USER_WARNING);
      $new_path = $src_path;
    }


    if (($this->ffmpeg_path != false && $this->file_helper->exists($this->ffmpeg_path)) && !$this->file_helper->exists($new_path)) {
      // Let's make sure we have a lock on our destination file, and that there is no lock on our source file
      $count = 0;
      while ($this->file_helper->hasFileLock($src_path, false) || $this->file_helper->hasFileLock($new_path, false))
      {
        usleep(500);
        $count++;
        if ($count == 10)
          break;
      }

      if (!$this->file_helper->hasFileLock($src_path) && $this->file_helper->getFileLock($new_path)) {
        $this->executor->setExecutable($this->ffmpeg_path);
        $this->executor->setArguments($args);
        $this->executor->execute();
        $this->file_helper->removeFileLock($new_path);
      } else {
        $new_path = $src_path;
      }
    }
    return $new_path;
  }
}
