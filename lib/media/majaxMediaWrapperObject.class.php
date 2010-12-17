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

	public function getAudioSha1()
	{
		return $this->obj->getObject()->AudioFile->getSha1();
	}

	public function getPhotoSha1()
	{
		return $this->obj->getObject()->PhotoFile->getSha1();
	}



	protected function isMedia()
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
		$render_class =	sfConfig::get('app_majaxMedia_gallery_render', 'majaxMediaGalleryRender');
		$render	= new $render_class();
		return $render->render($this);


                $context = sfContext::getInstance();
                $context->getResponse()->addJavascript('/js/swfobject.js');
                $id = 'gallery_'.md5(time().microtime(true).'majaxMedia'.rand());
                $this->set('controller_height', 25);
                $gal = $this->obj->getObject();
                if (count($gal->Media) == 0)
                {
                        $cont = 'No items assigned to gallery.';
                        return $cont;
                }
                $fi = new majaxMediaWrapperObject($gal->Media[0]);
                $fi->set('width', $this->get('width'));
                $fi->set('height', $this->get('height'));
                $fi->set('crop_method', $this->get('crop_method'));
                $fi->set('aspect_ratio', $this->get('aspect_ratio'));
                $fi->set('controller_height', $this->get('controller_height'));
                switch($fi->getType())
                {
                        case 'Photo':
                                $nw = $fi->getPhotoWidth();
                                $nh = $fi->getPhotoHeight();
                                $vp = $fi->photoToString(true, true);
                                $ip = $fi->photoToString(true, true);
                                break;
                        case 'Audio':
                                $nw = $fi->getPhotoWidth();
                                $nh = $fi->getPhotoHeight();
                                $vp = $fi->audioToString(true);
                                $ip = $fi->photoToString(true);
                                break;
                        case 'Video':
                                $nw = $fi->getVideoWidth();
                                $nh = $fi->getVideoHeight();
                                $vp = $fi->videoToString(true);
                                $ip = $fi->photoToString(true);
                                break;
                }

                $context = sfContext::getInstance();
                $context->getConfiguration()->loadHelpers(array('Url'));
                $width = $this->get('width', 400);
                $height = $this->get('controller_height');
                $height += $this->getRatioHeight($width, null, $nw, $nh, $this->get('aspect_ratio'));
		$checksum = md5($gal->id.sfConfig::get('sf_csrf_secret'));
                $uri = 'majaxMediaGalleryModule/list?checksum='.$checksum.'&id='.$gal->id.'&width='.$width.'&height='.($height - $this->get('controller_height'));
                $uri .= '&aspect_ratio='.str_replace(':', 'x', $this->get('aspect_ratio', '16:9')).'&crop_method='.$this->get('crop_method', 'fit');
		$uri .= '&sf_format=xml';
                $url = url_for($uri);
                $length = $this->getLength();
                $cont = '<div class="player" style="width: '.$width.'px; height: '.$height.'px; background-image: url('.$ip.'); background-repeat: no-repeat; padding-top: '.($height - $this->get('controller_height')).'px;" id="'.$id.'_display">Flash is required to view this content.</div>';
                $cont .= '<script type="text/javascript">';
                $cont .= $this->getGalleryJWPlayerJSBlock($id, $vp, $width, $height, $length, $ip, $url);
                $cont .= '</script>';
                return $cont;
        }
        protected function getGalleryJWPlayerJSBlock($id, $file_src, $width, $height, $length = null, $photo_src = null, $playlist_path)
        {
                $playlist_size = $this->get('playlist_size', 180);
                $playlist_position = $this->get('playlist_position', 'bottom');
                $playlist_enabled = $this->get('playlist_enabled', true);
                $real_height = ($playlist_enabled && ($playlist_position == 'bottom' || $playlist_position == 'top')) ? $playlist_size + $height : $height;
                $real_width = ($playlist_enabled && ($playlist_position == 'left' || $playlist_position == 'right')) ? $playlist_size + $width : $width;


                $jscont = '//<![CDATA[

(function(){
  var flashvars = {
    backcolor: \'111111\',
    frontcolor: \'cccccc\',
    lightcolor: \'66cc00\',
    repeat: \'list\'';


                if ($playlist_enabled)
                        $jscont .= '
    ,playlistsize: '.$playlist_size.'
    ,playlist: \''.$playlist_position.'\'
    ,playlistfile: \''.$playlist_path.'\'';

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
    '.$real_width.',
    '.$real_height.',
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
}
