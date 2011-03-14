<?php

class majaxMediaFilenameBuilder
{
	public function render($width, $height, $crop_method, $append)
	{
		return $width.'x'.$height.'_'.$crop_method.'_'.$append;
	}
}
