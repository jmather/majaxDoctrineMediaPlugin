<?php
class majaxMediaWidgetFormInputFile extends sfWidgetFormInputFileEditable
{
  protected $oFileInfo = null;
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('with_delete', true);
    $this->setOption('file_src', false);
    $this->setOption('edit_mode', false);
    $this->setOption('template', '<div>%file%<br />%input%<br />%delete% %delete_label%</div>');
    $this->addOption('controller_height', 25);

    $this->addRequiredOption('file_id');
  }

  public function getFileInfo()
  {
    $file_id = $this->getOption('file_id');
    if ($file_id > 0 && $this->oFileInfo == null)
    {
      $file = Doctrine_Query::create()->from('majaxMediaFileInfo fi')->where('fi.id = ?', $file_id)->fetchOne();
      if ($file)
      {
        $this->oFileInfo = $file;
      }
    }
    return $this->oFileInfo;
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ($file = $this->getFileInfo())
    {
      $this->setOption('file_src', $file->web_path);
      $this->setOption('edit_mode', true);
    }
    return parent::render($name, $value, $attributes, $errors);
  }

  protected function getFileAsTag($attributes)
  {
    $fi = $this->getFileInfo();

    $context = sfContext::getInstance();
    $context->getConfiguration()->loadHelpers(array('majaxMedia'), $context->getModuleName());

    if (!$fi)
    {
      return null;
    }

    if ($fi->isImage())
    {
      $stuff = array();
      $width = ($this->getOption('width') !== null) ? $this->getOption('width') : 600;
      $height = (($this->getOption('height') !== null) ? $this->getOption('height') : null);
      $ratio = '16:9';
      $stuff['width'] = $width;
      $stuff['height'] = $height;
      $stuff['src'] = $this->getOption('file_src');
      $stuff['alt'] = $fi->getName();
      return majaxMedia($fi)->width($width);
      return $this->renderTag('img', array_merge($stuff, $attributes));
    }

    
    if ($fi->isVideo())
    {
      return majaxMedia($fi)->width(600);
      $context = sfContext::getInstance();
      $context->getResponse()->addJavascript('/majaxDoctrineMediaPlugin/js/swfobject.js');
      $id = $this->generateIdString('video_');
      $cont = $this->getFlashRequiredBlock($id, 'video');
      $width = ($this->getOption('width') !== null) ? $this->getOption('width') : $fi->getWidth();
      $height = (($this->getOption('height') !== null) ? $this->getOption('height') : $fi->getHeight())+$this->getOption('controller_height');
      $length = ($this->getOption('length') !== null) ? $this->getOption('length') : $fi->getLength();
      $jscont = $this->getPlayerJSBlock($id, $this->getOption('file_src'), $width, $height, $length);
      $cont .= $this->renderContentTag('script', $jscont, array('type'=>'text/javascript'));
      return $cont;
    }


    if ($fi->isAudio())
    {
      return majaxMedia($fi)->width(600);
      $context = sfContext::getInstance();
      $context->getResponse()->addJavascript('/majaxDoctrineMediaPlugin/js/swfobject.js');
      $id = $this->generateIdString('audio_');
      $cont = $this->getFlashRequiredBlock($id, 'audio');
      $width = ($this->getOption('width') !== null) ? $this->getOption('width') : 400;
      $height = (($this->getOption('height') !== null) ? $this->getOption('height') : 0)+$this->getOption('controller_height');
      $length = ($this->getOption('length') !== null) ? $this->getOption('length') : $fi->getLength();
      $jscont = $this->getPlayerJSBlock($id, $this->getOption('file_src'), $width, $height, $length);
      $cont .= $this->renderContentTag('script', $jscont, array('type'=>'text/javascript'));
      return $cont;
    }


    $n = $fi->getName().' ('.$fi->getSizeFormatted().')';
    return $this->renderContentTag('a', $n, array_merge(array('href' => $this->getOption('file_src')), $attributes));
  }

  
  protected function getFlashRequiredBlock($id, $class)
  {
    $cont = '<div id="'.$id.'">Flash is required to view this content.</div>';
    return $cont;
  }

  protected function generateIdString($prefix = '')
  {
    return $prefix.substr(md5(time().'fdjkhdf'.microtime()), 0, 8);
  }

  protected function getPlayerJSBlock($id, $file_src, $width, $height, $length = null)
  {
    $jscont = '//<![CDATA[
if (!players)
  var players = {};

(function(){
  var flashvars = {
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
  players[\''.$id.'\'] = swfobject.embedSWF(
    \'/majaxDoctrineMediaPlugin/flash/player.swf\',
    \''.$id.'\',
    '.$width.',
    '.$height.',
    \'9\',
    \'/majaxDoctrineMediaPlugin/flash/expressInstall.swf\',
    flashvars,
    params
  );
})();
//]]>';
    return $jscont;
  }

}
