<?php

class majaxMediaFFMpegVideoTransformationFitBuilder extends majaxMediaFFMpegVideoTransformationBuilder
{
	public function render($source_width, $source_height, $new_width, $new_height, $crop_method = 'fit')
	{
		$method = 'get'.ucwords(strtolower($crop_method));
		if ($method != 'getFit')
		{
			throw new InvalidArgumentException($crop_method.' is not a supported Crop Method.');
		}
		return $this->$method($source_width, $source_height, $new_width, $new_height);
	}

	public function getFit($source_width, $source_height, $new_width, $new_height)
	{
		$args = array();

		$ratio = $this->buildRatio($source_width, $source_height, $new_width, $new_height);

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($ratio, $source_width, $source_height, $new_width, $new_height);

		if ($ratio_height != $new_height)
		{
			$diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
			$args[] = $new_width;
			$args[] = $new_height - $diff;
		}
		if ($ratio_width != $new_width)
		{
		  $diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
		  $args[] = $new_width - $diff;
		  $args[] = $new_height;
		}
		return $args;
	}
}
?>
