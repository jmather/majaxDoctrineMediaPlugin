<?php

class majaxMediaFFMpegVideoTransformationBuilder
{
	public function render($source_width, $source_height, $new_width, $new_height, $crop_method = 'fit')
	{
		$method = 'get'.ucwords(strtolower($crop_method));
		if (!is_callable(array($this, $method)))
		{
			throw new InvalidArgumentException($crop_method.' is not a supported Crop Method.');
		}
		return $this->$method($source_width, $source_height, $new_width, $new_height);
	}
	public function buildRatio($source_width, $source_height, $new_width, $new_height)
	{
		$ratio = min($new_height / $source_height, $new_width / $source_width);
		return $ratio;
	}
	public function buildAdjustedSet($ratio, $source_width, $source_height, $new_width, $new_height)
	{
		error_log('Ratio: '.$ratio);

		$ratio_height = $source_height * $ratio;
		$ratio_width = $source_width * $ratio;

		error_log('r_width: '.$ratio_width);
		error_log('r_height: '.$ratio_height);

		if ($ratio_height < $new_height)
		{
			$ratio_width = $ratio_width * $new_height / $ratio_height;
			$ratio_height = $new_height;
//			$ratio_width = $ratio_height * $new_width / $new_height;
		}
		if ($ratio_width < $new_width)
		{
			$ratio_height = $ratio_height * $new_width / $ratio_width;
			$ratio_width = $new_width;
//			$ratio_height = $ratio_width * $new_height / $new_width;
		}

		error_log('r_width: '.$ratio_width);
		error_log('r_height: '.$ratio_height);

		return array($ratio_width, $ratio_height);
	}

	public function getCenter($source_width, $source_height, $new_width, $new_height)
	{
		$args = array();

		$ratio = $this->buildRatio($source_width, $source_height, $new_width, $new_height);

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($ratio, $source_width, $source_height, $new_width, $new_height);

		if ($ratio_height != $new_height)
		{
			$diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-croptop';
			$args[] = $diff_split;
			$args[] = '-cropbottom';
			$args[] = $diff_split;
		}


		if ($ratio_width != $new_width)
		{
			$diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-cropright';
			$args[] = $diff_split;
			$args[] = '-cropleft';
			$args[] = $diff_split;
		}
		return $args;
	}
	public function getLeft($source_width, $source_height, $new_width, $new_height)
	{
		$args = array();

		$ratio = $this->buildRatio($source_width, $source_height, $new_width, $new_height);

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($ratio, $source_width, $source_height, $new_width, $new_height);

		if ($ratio_height != $new_height)
		{
			$diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-croptop';
			$args[] = $diff_split;
			$args[] = '-cropbottom';
			$args[] = $diff_split;
		}

		if ($ratio_width != $new_width)
		{
			$diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-cropright';
			$args[] = $diff;
		}
		return $args;
	}
	public function getRight($source_width, $source_height, $new_width, $new_height)
	{
		$args = array();

		$ratio = $this->buildRatio($source_width, $source_height, $new_width, $new_height);

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($ratio, $source_width, $source_height, $new_width, $new_height);

		if ($ratio_height != $new_height)
		{
			$diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-croptop';
			$args[] = $diff_split;
			$args[] = '-cropbottom';
			$args[] = $diff_split;
		}

		if ($ratio_width != $new_width)
		{
			$diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-cropleft';
			$args[] = $diff;
		}
		return $args;
	}
	public function getTop($source_width, $source_height, $new_width, $new_height)
	{
		$args = array();

		$ratio = $this->buildRatio($source_width, $source_height, $new_width, $new_height);

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($ratio, $source_width, $source_height, $new_width, $new_height);

		if ($ratio_height != $new_height)
		{
			error_log('N: '.$new_height.' R: '.$ratio_height);
			$diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
			$args[] = '-cropbottom';
			$args[] = $diff;
		}

		if ($ratio_width != $new_width)
		{
			$diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-cropright';
			$args[] = $diff_split;
			$args[] = '-cropleft';
			$args[] = $diff_split;
		}
		return $args;
	}
	public function getBottom($source_width, $source_height, $new_width, $new_height)
	{
		$args = array();

		$ratio = $this->buildRatio($source_width, $source_height, $new_width, $new_height);

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($ratio, $source_width, $source_height, $new_width, $new_height);

		if ($ratio_height != $new_height)
		{
			$diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
			$args[] = '-croptop';
			$args[] = $diff;
		}

		if ($ratio_width != $new_width)
		{
			$diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-cropright';
			$args[] = $diff_split;
			$args[] = '-cropleft';
			$args[] = $diff_split;
		}
		return $args;
	}
}
?>
