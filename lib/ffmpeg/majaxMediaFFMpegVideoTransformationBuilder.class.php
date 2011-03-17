<?php

class majaxMediaFFMpegVideoTransformationBuilder
{
  protected $pad_color = '000000';
  public function setPadColor($color)
  {
    $this->pad_color = $color;
  }
	public function render($source_width, $source_height, $new_width, $new_height, $crop_method = 'fit', $pad_color = null)
	{
		$method = 'get'.ucwords(strtolower($crop_method));
		if (!is_callable(array($this, $method)))
		{
			throw new InvalidArgumentException($crop_method.' is not a supported Crop Method.');
		}
		return $this->$method($source_width, $source_height, $new_width, $new_height, $pad_color);
	}
	public function buildRatio($source_width, $source_height, $new_width, $new_height)
	{
		$scale_down_ratio = min($new_height / $source_height, $new_width / $source_width);
		$scale_up_ratio = max($new_height / $source_height, $new_width / $source_width);

    if ($scale_down_ratio < 1)
  		return $scale_down_ratio;
    if ($scale_up_ratio > 1)
      return $scale_up_ratio;
    return 1;
	}
	public function buildAdjustedSet($source_width, $source_height, $new_width, $new_height)
	{
    $ratio = $this->buildRatio($source_width, $source_height, $new_width, $new_height);
		$ratio_height = $source_height * $ratio;
		$ratio_width = $source_width * $ratio;

		if ($ratio_height < $new_height) {
      echo '|1|';
			$ratio_width = $ratio_width * $new_height / $ratio_height;
			$ratio_height = $new_height;
		} elseif ($ratio_width < $new_width) {
      echo '|2|';
			$ratio_height = $ratio_height * $new_width / $ratio_width;
			$ratio_width = $new_width;
		} elseif ($ratio_height > $new_height) {
      echo '|3|';
			$ratio_height = $ratio_height * $new_width / $ratio_width;
			$ratio_width = $new_width;
    } elseif ($ratio_width > $new_width) {
      echo '|4|';
      $ratio_width = $ratio_width * $new_height / $ratio_height;
      $ratio_height = $new_height;
    }

    var_export(array($ratio_width, $ratio_height));

		return array($ratio_width, $ratio_height);
	}

	public function getCenter($source_width, $source_height, $new_width, $new_height)
	{
		$args = array();

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($source_width, $source_height, $new_width, $new_height);

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
	public function getFit($source_width, $source_height, $new_width, $new_height, $pad_color)
	{
    if ($pad_color === null)
    {
      $pad_color = $this->pad_color;
    }
		$args = array();

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($source_width, $source_height, $new_width, $new_height);

		if ($ratio_height < $new_height) {
      echo '{1}';
			$diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-vf';
      $args[] = 'pad='.$new_width.':'.$new_height.':'.$diff_split.':0:'.$pad_color;
		} elseif ($ratio_width < $new_width) {
      echo '{2}';
			$diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-vf';
      $args[] = 'pad='.$new_width.':'.$new_height.':0:'.$diff_split.':'.$pad_color;
		} elseif ($ratio_height > $new_height) {
      echo '{3}';
			$diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-vf';
      $args[] = 'pad='.$new_width.':'.$new_height.':0:'.$diff_split.':'.$pad_color;
    } elseif ($ratio_width > $new_width) {
      echo '{4}';
			$diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
			$diff_split = $diff / 2;
			$args[] = '-vf';
      $args[] = 'pad='.$new_width.':'.$new_height.':'.$diff_split.':0:'.$pad_color;
    }
		return $args;
	}
	public function getLeft($source_width, $source_height, $new_width, $new_height)
	{
		$args = array();

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($source_width, $source_height, $new_width, $new_height);

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

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($source_width, $source_height, $new_width, $new_height);

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

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($source_width, $source_height, $new_width, $new_height);

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

		list($ratio_width, $ratio_height) = $this->buildAdjustedSet($source_width, $source_height, $new_width, $new_height);

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
