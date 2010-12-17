<?php
class majaxMediaAudioRender extends majaxMediaRender
{
	function render($media_object, $render_path)
	{
		$context = sfContext::getInstance();
		$context->getResponse()->addJavascript('/majaxDoctrineMediaPlugin/js/swfobject.js');
		$id = 'audio_'.md5(time().microtime(true).'majax'.rand());
		$cont = $this->getFlashRequiredBlock($id);
		$width = $media_object->get('width', 400);
		$height = $media_object->get('controller_height');
		if ($media_object->isMedia())
		{
			$height += $media_object->getRatioHeight($width, null, $media_object->getPhotoWidth(), $media_object->getPhotoHeight(), $media_object->get('aspect_ratio'));
		}
		$length = $media_object->getLength();
		if ($media_object->isMedia())
		{
			$jscont = $this->getPlayerJSBlock($id, $render_path, $width, $height, $length, $media_object->photoToString(true));
		} else {
			$jscont = $this->getPlayerJSBlock($id, $render_path, $width, $height, $length);
		}
		$cont .= '<script type="text/javascript">'.$jscont.'</script>';
		return $cont;
	}	
}
