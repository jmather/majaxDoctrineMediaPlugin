<?php

class majaxMediaFilenameBuilder
{
	public function render($width, $height, $crop_method, $extension)
	{
		return $width.'x'.$height.'_'.$crop_method.'.'.$extension;
	}
}
