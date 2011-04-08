<?php
abstract class majaxMediaWrapperManager
{
  protected $properties;

  protected $file_info = null;

  public function __construct()
  {
    $this->properties = array();
    $this->properties['aspect_ratio'] = 'auto';
    $this->properties['controller_height'] = 20;
  }

  protected function init()
  {
  }

  public function get($property, $default = null)
  {
    if (isset($this->properties[$property]))
      return $this->properties[$property];
    return $default;
  }

  public function set($property, $value)
  {
    $this->properties[$property] = $value;
    return $this;
  }

  public function width($width = null)
  {
    if ($width == null)
      return $this->get('width');
    else
      $this->set('width', $width);
    return $this;
  }

  public function height($height = null)
  {
    if ($height == null)
      return $this->get('height');
    else
      $this->set('height', $height);
    return $this;
  }

  public function aspect_ratio($ratio = null)
  {
    if ($ratio == null)
      return $this->get('aspect_ratio');
    $this->set('aspect_ratio', $ratio);
    return $this;
  }

  public function crop_method($method = null)
  {
    if ($method == null)
      return $this->get('crop_method', 'fit');
    $allowed = array('fit', 'scale', 'inflate', 'deflate', 'left', 'right', 'top', 'bottom', 'center');
    if (!in_array($method, $allowed))
      throw new InvalidArgumentException('Crop method "' . $method . '" is invalid. Only fit, scale, inflate, deflate, left, right, top, bottom, or center');
    $this->set('crop_method', $method);
    return $this;
  }

  public function attributes($params = null)
  {
    if ($params == null)
      return $this->get('attributes', array());
    if (!is_array($params))
      throw new InvalidArgumentException('Attributes must be called with an array or null parameter.');

    $p = $this->get('attributes', array());
    $this->set('attributes', array_merge($params, $p));
    return $this;
  }

  public function ignore_type($value = null)
  {
    if ($value == null)
      return $this->get('ignore_type', false);
    $v = (intval($value) != 0) ? true : false;
    $this->set('ignore_type', $v);
    return $this;
  }

  public function __toString()
  {
    if ($this->getType() == 'Photo')
      return $this->photoToString();
    if ($this->getType() == 'Audio')
      return $this->audioToString();
    if ($this->getType() == 'Video')
      return $this->videoToString();
    return 'Unknown';
  }

  public function videoToString($path_only = false)
  {
    $ffmpeg = new majaxMediaFFMpeg();

    $new_path = $ffmpeg->process($this->getVideoFile(), $this->width(), $this->height(), $this->crop_method(), $this->aspect_ratio());
    $web_path = preg_replace('|^' . preg_quote(sfConfig::get('sf_web_dir')) . '|', $new_path);

    if ($path_only)
      return $web_path;


    $render_class = sfConfig::get('app_majax_media_video_render', 'majaxMediaVideoRender');
    $render = new $render_class();
    return $render->render($this, $web_path);
  }

  public function audioToString($path_only = false)
  {
    $processor = new majaxMediaAudioProcessor();

    $new_path = $processor->process($this->getPhotoFile(), null, null, null, null);

    $web_path = preg_replace('|^' . preg_quote(sfConfig::get('sf_web_dir')) . '|', $new_path);

    if ($path_only)
      return $web_path;


    $render_class = sfConfig::get('app_majax_media_audio_render', 'majaxMediaAudioRender');
    $render = new $render_class();
    return $render->render($this, $web_path);
  }

  public function photoToString($path_only = false, $ignore_type = false)
  {
    $processor = new majaxMediaImageProcessor();

    $extra_height = 0;
    if ($this->getType() == 'Photo' && $ignore_type == false && $this->ignore_type() == false) {
      $extra_height = $this->get('controller_height');
    }

    $new_path = $processor->process($this->getPhotoFile(), $this->width(), $this->height(), $this->crop_method(), $this->aspect_ratio(), 0, $extra_height);

    $web_path = preg_replace('|^' . preg_quote(sfConfig::get('sf_web_dir')) . '|', $new_path);

    if ($path_only)
      return $web_path;

    $out = '<img src="' . $web_path . '"';
    foreach ($this->get('attributes', array()) as $name => $value)
      $out .= ' ' . $name . '="' . $value . '"';
    $out .= ' />';
    return $out;
  }

  public function getLength()
  {
    switch ($this->getType())
    {
      case 'Audio':
        return $this->getAudioLength();
        break;
      case 'Video':
        return $this->getVideoLength();
        break;
    }
    return null;
  }

  public function getName()
  {
    switch ($this->getType())
    {
      case 'Video':
        return $this->getVideoName();
      case 'Photo':
        return $this->getPhotoName();
      case 'Audio':
        return $this->getAudioName();
    }
  }

  public function getSize()
  {
    switch ($this->getType())
    {
      case 'Video':
        return $this->getVideoSize();
      case 'Photo':
        return $this->getPhotoSize();
      case 'Audio':
        return $this->getAudioSize();
    }
  }

  public function isMedia()
  {
    return false;
  }

  abstract public function getType();

  abstract public function getPhotoName();

  abstract public function getPhotoSize();

  abstract public function getPhotoMime();

  abstract public function getPhotoData();

  abstract public function getPhotoSha1();

  abstract public function getPhotoFileInfo();

  abstract public function getPhotoWidth();

  abstract public function getPhotoHeight();

  abstract public function getVideoName();

  abstract public function getVideoSize();

  abstract public function getVideoMime();

  abstract public function getVideoData();

  abstract public function getVideoSha1();

  abstract public function getVideoLength();

  abstract public function getVideoWidth();

  abstract public function getVideoHeight();

  abstract public function getVideoFileInfo();

  abstract public function getAudioName();

  abstract public function getAudioSize();

  abstract public function getAudioMime();

  abstract public function getAudioData();

  abstract public function getAudioSha1();

  abstract public function getAudioLength();

  abstract public function getAudioFileInfo();
}
