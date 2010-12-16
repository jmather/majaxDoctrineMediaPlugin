<?php

class majaxMediaGalleryRender extends majaxMediaRender
{
	public function render($media_object)
	{
		$context = sfContext::getInstance();
		$context->getResponse()->addJavascript('/js/swfobject.js');
		$id = 'gallery_'.md5(time().microtime(true).'majaxMedia'.rand());
		$media_object->set('controller_height', 25);
		$gal = $media_object->obj->getObject();

		if (count($gal->Media) == 0)
		{
			$cont = 'No items assigned to gallery.';
			return $cont;
		}

		$fi = new majaxMediaWrapperObject($gal->Media[0]);
		$fi->set('width', $media_object->get('width'));
		$fi->set('height', $media_object->get('height'));
		$fi->set('crop_method', $media_object->get('crop_method'));
		$fi->set('aspect_ratio', $media_object->get('aspect_ratio'));
		$fi->set('controller_height', $media_object->get('controller_height'));
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

		$context->getConfiguration()->loadHelpers(array('Url'));

		$width = $media_object->get('width', 400);
		$height = $media_object->get('controller_height');
		$height += $media_object->getRatioHeight($width, null, $nw, $nh, $media_object->get('aspect_ratio'));
		$checksum = md5($gal->id.sfConfig::get('sf_csrf_secret'));
		$uri = 'majaxMediaGalleryModule/list?checksum='.$checksum.'&id='.$gal->id.'&width='.$width.'&height='.($height - $media_object->get('controller_height'));
		$uri .= '&aspect_ratio='.str_replace(':', 'x', $media_object->get('aspect_ratio', '16:9')).'&crop_method='.$media_object->get('crop_method', 'fit');
		$uri .= '&sf_format=xml';
		$url = url_for($uri);
		$length = $media_object->getLength();
		$cont = '<div class="player" style="width: '.$width.'px; height: '.$height.'px; background-image: url('.$ip.'); background-repeat: no-repeat; padding-top: '.($height - $media_object->get('controller_height')).'px;" id="'.$id.'_display">Flash is required to view this content.</div>';
		$cont .= '<script type="text/javascript">';
		$cont .= $this->getGalleryJWPlayerJSBlock($media_object, $id, $vp, $width, $height, $length, $ip, $url);
		$cont .= '</script>';
		return $cont;
        }

	protected function getGalleryJWPlayerJSBlock($media_object, $id, $file_src, $width, $height, $length = null, $photo_src = null, $playlist_path)
	{
		$playlist_size = $media_object->get('playlist_size', 180);
		$playlist_position = $media_object->get('playlist_position', 'bottom');
		$playlist_enabled = $media_object->get('playlist_enabled', true);
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
