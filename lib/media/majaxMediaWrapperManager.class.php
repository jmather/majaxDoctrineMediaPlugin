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
    $allowed = array('fit', 'scale', 'inflate','deflate', 'left' ,'right', 'top', 'bottom', 'center');
    if (!in_array($method, $allowed))
      throw new InvalidArgumentException('Crop method "'.$method.'" is invalid. Only fit, scale, inflate, deflate, left, right, top, bottom, or center');
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
    $web_path = preg_replace('|^'.preg_quote(sfConfig::get('sf_web_dir')).'|', $new_path);

    if ($path_only)
      return $new_partial_path;


    $render_class = sfConfig::get('app_majax_media_video_render', 'majaxMediaVideoRender');
    $render = new $render_class();
    return $render->render($this, $web_path);
  }

  public function audioToString($path_only = false)
  {
    $name = $this->getAudioName();
    $sha1 = $this->getAudioSha1();
    $path = self::sha1ToPath($sha1);
    $full_path = sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$name;
    if (!file_exists(sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$name))
    {
      self::ensurePath($path, sfConfig::get('majax_media_dir'));
      if (majaxMediaToolbox::getFileLock($full_path))
      {
        $data = $this->getAudioData();
        file_put_contents($full_path, $data);
        majaxMediaToolbox::removeFileLock($full_path);
      }
    }

    $new_partial_path = '/'.sfConfig::get('majax_media_dir_name').'/'.$path.'/'.$name;

    if ($path_only)
      return $new_partial_path;


    $render_class =  sfConfig::get('app_majax_media_audio_render', 'majaxMediaAudioRender');
b    $render  = new $render_class();
    return $render->render($this, $new_partial_path);
  }

  public function photoToString($path_only = false, $ignore_type = false)
  {
    $name = $this->getPhotoName();
    $sha1 = $this->getPhotoSha1();
    $path = self::sha1ToPath($sha1);
    $full_path = sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$name;
    if (!file_exists(sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$name))
    {
      self::ensurePath($path, sfConfig::get('majax_media_dir'));
      if (majaxMediaToolbox::getFileLock($full_path))
      {
        $data = $this->getPhotoData();
        file_put_contents($full_path, $data);
        majaxMediaToolbox::removeFileLock($full_path);
      }
    }

    if ($this->get('width') !== null || $this->get('height') !== null)
    {
      $dims = $this->getRatioDimensions($this->get('width'), $this->get('height'), $this->getPhotoWidth(), $this->getPhotoHeight(), $this->get('aspect_ratio'));
      $new_width = $dims[0];
      $new_height = $dims[1];
    } else {
      $new_width = $this->getPhotoWidth();
      $new_height = $this->getPhotoHeight();
    }

    if ($this->getType() == 'Photo' && $ignore_type == false && $this->ignore_type() == false)
    {
      $new_height += $this->get('controller_height');
    }

    $new_filename = intval($this->ignore_type()).'_'.$new_width.'x'.$new_height;
    $new_filename .= '_'.$this->get('crop_method', 'fit').'_'.$name;
    $new_partial_path = $path.DIRECTORY_SEPARATOR.$new_filename;
    $new_full_path = sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$new_partial_path;

    try {
    if (!file_exists($new_full_path) && majaxMediaToolbox::getFileLock($new_full_path) && !majaxMediaToolbox::hasFileLock($full_path))
    {
      $image = new sfImage($full_path, $this->getPhotoMime());
      $image->thumbnail($new_width, $new_height, $this->crop_method());
      $image->saveAs(sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$new_partial_path);
      majaxMediaToolbox::removeFileLock($new_full_path);
    } } catch (Exception $e) {
      echo $e;
    }
    if ($path_only)
      return '/'.sfConfig::get('majax_media_dir_name').'/'.$new_partial_path;
    $out = '<img src="/'.sfConfig::get('majax_media_dir_name').'/'.$new_partial_path.'"';
    foreach($this->get('attributes', array()) as $name => $value)
      $out .= ' '.$name.'="'.$value.'"';
    $out .= ' />';
    return $out;
  }

  /**
   * @param $sha1
   * @return string
   */
  function sha1ToPath($sha1)
  {
    return wordwrap($sha1, 2, DIRECTORY_SEPARATOR, true);
  }

  /**
   * @param $path (presumed non-existant)
   * @param $base (presumed existant)
   * @return void
   */

  function ensurePath($path, $base = '')
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

  public function getLength()
  {
    switch($this->getType())
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
    switch($this->getType())
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
    switch($this->getType())
    {
      case 'Video':
        return $this->getVideoSize();
      case 'Photo':
        return $this->getPhotoSize();
      case 'Audio':
        return $this->getAudioSize();
    }
  }

  public function getFormattedSize($precision = 2)
  {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = $this->getSize();
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
  }

  protected function getFlashRequiredBlock($id)
  {
    $cont = '<div id="'.$id.'_display">Flash is required to view this content.</div>';
    return $cont;
  }

  protected function getPlayerJSBlock($id, $file_src, $width, $height, $length = null, $photo_src = null)
  {
    $jscont = '//<![CDATA[

(function(){
  var flashvars = {';

    if ($photo_src !== null)
      $jscont .= '
    image: \''.$photo_src.'\',';

    $jscont .= '
    file: \''.$file_src.'\'';
    if ($length !== null)
    {
      $jscont .= ',
    duration: \''.$length.'\'';
    }

    $jscont .= '
  };
  var params = {
    wmode: \'transparent\',
    allowfullscreen: \'true\',
    allownetworking: \'all\',
    allowscriptaccess: \'always\'
  };
  var attrs = {
    id: \''.$id.'\',
    name: \''.$id.'\'
  };

  swfobject.embedSWF(
    \'/majaxDoctrineMediaPlugin/flash/player.swf\',
    \''.$id.'_display\',
    '.$width.',
    '.$height.',
    \'9\',
    \'/majaxDoctrineMediaPlugin/flash/expressInstall.swf\',
    flashvars,
    params,
    attrs
  );
  //players[\''.$id.'\'] = document.getElementById(\''.$id.'\');
})();
//]]>';
    return $jscont;
  }

  public function getRatioDimensions($new_width = null, $new_height = null, $orig_width = 16, $orig_height = 9, $aspect_ratio = 'auto')
  {
    if ($new_width == null)
    {
      if ($this->properties['aspect_ratio'] == 'auto')
      {
        $new_width = round($new_height * $orig_width / $orig_height);
      } else {
        list($aw, $ah) = explode(':', $this->properties['aspect_ratio'], 2);
        $new_width = round($new_height * $aw / $ah);
      }
    }
    if ($new_height == null)
    {
      if ($this->properties['aspect_ratio'] == 'auto')
      {
        $new_height = round($new_width * $orig_height / $orig_width);
      } else {
        list($aw, $ah) = explode(':', $this->properties['aspect_ratio'], 2);
        $new_height = round($new_width * $ah / $aw);
      }
    }
    return array($new_width, $new_height);
  }

  public function getRatioWidth($new_width = null, $new_height = null, $orig_width = 16, $orig_height = 9, $aspect_ratio = 'auto')
  {
    $r = $this->getRatioDimensions($new_width, $new_height, $orig_width, $orig_height, $aspect_ratio);
    return $r[1];
  }

  public function getRatioHeight($new_width = null, $new_height = null, $orig_width = 16, $orig_height = 9, $aspect_ratio = 'auto')
  {
    $r = $this->getRatioDimensions($new_width, $new_height, $orig_width, $orig_height, $aspect_ratio);
    return $r[1];
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
  abstract public function getVideoName();
  abstract public function getVideoSize();
  abstract public function getVideoMime();
  abstract public function getVideoData();
  abstract public function getVideoSha1();
  abstract public function getVideoLength();
  abstract public function getAudioName();
  abstract public function getAudioSize();
  abstract public function getAudioMime();
  abstract public function getAudioData();
  abstract public function getAudioSha1();
  abstract public function getAudioLength();
  abstract public function getPhotoWidth();
  abstract public function getPhotoHeight();
  abstract public function getVideoWidth();
  abstract public function getVideoHeight();

}
