<?php
class majaxMediaWrapperObject extends majaxMediaWrapperManager
{
  protected $obj = null;

  public function __construct($object)
  {
    parent::__construct();
    $this->obj = $object;
  }

  public function getType()
  {
    return $this->obj->getType();
  }

  public function getPhotoName()
  {
    return $this->obj->getObject()->PhotoFile->name;
  }

  public function getPhotoSize()
  {
    return $this->obj->getObject()->PhotoFile->size;
  }

  public function getPhotoMime()
  {  
    return $this->obj->getObject()->PhotoFile->mime;
  }

  public function getPhotoData()
  {
    return $this->obj->getObject()->PhotoFile->FileData->binary_data;
  }

  public function getPhotoFileInfo()
  {
    return $this->obj->getObject()->PhotoFile;
  }

  public function getAudioName()
  {
    return $this->obj->getObject()->AudioFile->name;
  }

  public function getAudioSize()
  {
    return $this->obj->getObject()->AudioFile->size;
  }

  public function getAudioMime()
  {  
    return $this->obj->getObject()->AudioFile->mime;
  }

  public function getAudioData()
  {
    return $this->obj->getObject()->AudioFile->FileData->binary_data;
  }

  public function getAudioLength()
  {
    return $this->obj->getObject()->AudioFile->getLength();
  }

  public function getAudioFileInfo()
  {
    return $this->obj->getObject()->AudioFile;
  }

  public function getVideoName()
  {
    return $this->obj->getObject()->VideoFile->name;
  }

  public function getVideoSize()
  {
    return $this->obj->getObject()->VideoFile->size;
  }

  public function getVideoMime()
  {  
    return $this->obj->getObject()->VideoFile->mime;
  }

  public function getVideoData()
  {
    return $this->obj->getObject()->VideoFile->FileData->binary_data;
  }

  public function getVideoLength()
  {
    return $this->obj->getObject()->VideoFile->getLength();
  }

  public function getPhotoWidth()
  {
    return $this->obj->getObject()->PhotoFile->getWidth();
  }

  public function getPhotoHeight()
  {
    return $this->obj->getObject()->PhotoFile->getHeight();
  }

  public function getVideoWidth()
  {
    return $this->obj->getObject()->VideoFile->getWidth();
  }

  public function getVideoHeight()
  {
    return $this->obj->getObject()->VideoFile->getHeight();
  }

  public function getVideoSha1()
  {
    return $this->obj->getObject()->VideoFile->getSha1();
  }

  public function getVideoFileInfo()
  {
    return $this->obj->getObject()->VideoFile;
  }

  public function getAudioSha1()
  {
    return $this->obj->getObject()->AudioFile->getSha1();
  }

  public function getPhotoSha1()
  {
    return $this->obj->getObject()->PhotoFile->getSha1();
  }

  public function getObject()
  {
    return $this->obj;
  }

  public function isMedia()
  {
    return true;
  }

        public function __toString()
        {
    try {
      if ($this->getType() == 'Gallery')
        return $this->galleryToString();
    } catch (Exception $e) {
      return $e->__toString();
    }
                return parent::__toString();
        }

        public function galleryToString()
        {
    $render_class =  sfConfig::get('app_majax_media_gallery_render', 'majaxMediaGalleryRender');
    $render  = new $render_class();
    return $render->render($this);
        }
}
