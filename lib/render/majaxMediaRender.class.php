<?php

abstract class majaxMediaRender {
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
}
