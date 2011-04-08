<?php

class majaxMediaFFMpegVideoTransformationBuilder
{
  protected $pad_color = '000000';

  public function setPadColor($color)
  {
    $this->pad_color = $color;
  }

  public function render($src_width, $src_height, $new_width, $new_height, $crop_method = 'fit', $pad_color = null)
  {
    $method = 'get' . ucwords(strtolower($crop_method));
    if (!is_callable(array($this, $method))) {
      throw new InvalidArgumentException($crop_method . ' is not a supported Crop Method.');
    }
    return $this->$method($src_width, $src_height, $new_width, $new_height, $pad_color);
  }

  public function buildRatio($src_width, $src_height, $new_width, $new_height)
  {
    $scale_down_ratio = min($new_height / $src_height, $new_width / $src_width);
    $scale_up_ratio = max($new_height / $src_height, $new_width / $src_width);

    if ($scale_down_ratio < 1)
      return $scale_down_ratio;
    if ($scale_up_ratio > 1)
      return $scale_up_ratio;
    return 1;
  }

  public function buildAdjustedSet($src_width, $src_height, $new_width, $new_height)
  {
    $ratio = $this->buildRatio($src_width, $src_height, $new_width, $new_height);
    $ratio_height = $src_height * $ratio;
    $ratio_width = $src_width * $ratio;

    if ($ratio_height < $new_height) {
      $ratio_width = $ratio_width * $new_height / $ratio_height;
      $ratio_height = $new_height;
    } elseif ($ratio_width < $new_width) {
      $ratio_height = $ratio_height * $new_width / $ratio_width;
      $ratio_width = $new_width;
    } elseif ($ratio_height > $new_height) {
      $ratio_height = $ratio_height * $new_width / $ratio_width;
      $ratio_width = $new_width;
    } elseif ($ratio_width > $new_width) {
      $ratio_width = $ratio_width * $new_height / $ratio_height;
      $ratio_height = $new_height;
    }

    return array($ratio_width, $ratio_height);
  }

  public function getCenter($src_width, $src_height, $new_width, $new_height)
  {
    $args = array();

    list($ratio_width, $ratio_height) = $this->buildAdjustedSet($src_width, $src_height, $new_width, $new_height);

    if ($ratio_height != $new_height) {
      $diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
      $diff_split = $diff / 2;
      $args[] = '-croptop';
      $args[] = $diff_split;
      $args[] = '-cropbottom';
      $args[] = $diff_split;
    }


    if ($ratio_width != $new_width) {
      $diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
      $diff_split = $diff / 2;
      $args[] = '-cropright';
      $args[] = $diff_split;
      $args[] = '-cropleft';
      $args[] = $diff_split;
    }
    return $args;
  }

  public function getFit($src_width, $src_height, $new_width, $new_height, $pad_color)
  {
    if ($pad_color === null) {
      $pad_color = $this->pad_color;
    }
    $args = array();

    $scale = $new_width / $src_width;
    if ($src_height * $scale > $new_height)
      $scale = $new_height / $src_height;

    $scale_width = $src_width * $scale;
    $scale_height = $src_height * $scale;

    if ($scale_width < 1) {
      $scale_height = $scale_height * (1 / $scale_width);
      $scale_width = 1;
    }

    if ($scale_height < 1) {
      $scale_width = $scale_width * (1 / $scale_height);
      $scale_height = 1;
    }

    if ($scale_width == $new_width && $scale_height == $new_height) {
      return $args;
    }

    $offset_x = ceil(abs($new_width - $scale_width) / 2);
    $offset_y = ceil(abs($new_height - $scale_height) / 2);

    $args[] = '-vf';
    $args[] = 'pad=' . $new_width . ':' . $new_height . ':' . $offset_x . ':' . $offset_y . ':' . $pad_color;

    return $args;
  }

  public function getLeft($src_width, $src_height, $new_width, $new_height)
  {
    $args = array();

    list($ratio_width, $ratio_height) = $this->buildAdjustedSet($src_width, $src_height, $new_width, $new_height);

    if ($ratio_height != $new_height) {
      $diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
      $diff_split = $diff / 2;
      $args[] = '-croptop';
      $args[] = $diff_split;
      $args[] = '-cropbottom';
      $args[] = $diff_split;
    }

    if ($ratio_width != $new_width) {
      $diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
      $diff_split = $diff / 2;
      $args[] = '-cropright';
      $args[] = $diff;
    }
    return $args;
  }

  public function getRight($src_width, $src_height, $new_width, $new_height)
  {
    $args = array();

    list($ratio_width, $ratio_height) = $this->buildAdjustedSet($src_width, $src_height, $new_width, $new_height);

    if ($ratio_height != $new_height) {
      $diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
      $diff_split = $diff / 2;
      $args[] = '-croptop';
      $args[] = $diff_split;
      $args[] = '-cropbottom';
      $args[] = $diff_split;
    }

    if ($ratio_width != $new_width) {
      $diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
      $diff_split = $diff / 2;
      $args[] = '-cropleft';
      $args[] = $diff;
    }
    return $args;
  }

  public function getTop($src_width, $src_height, $new_width, $new_height)
  {
    $args = array();

    list($ratio_width, $ratio_height) = $this->buildAdjustedSet($src_width, $src_height, $new_width, $new_height);

    if ($ratio_height != $new_height) {
      $diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
      $args[] = '-cropbottom';
      $args[] = $diff;
    }

    if ($ratio_width != $new_width) {
      $diff = (ceil(abs($new_width - $ratio_width) / 2) * 2);
      $diff_split = $diff / 2;
      $args[] = '-cropright';
      $args[] = $diff_split;
      $args[] = '-cropleft';
      $args[] = $diff_split;
    }
    return $args;
  }

  public function getBottom($src_width, $src_height, $new_width, $new_height)
  {
    $args = array();

    list($ratio_width, $ratio_height) = $this->buildAdjustedSet($src_width, $src_height, $new_width, $new_height);

    if ($ratio_height != $new_height) {
      $diff = (ceil(abs($new_height - $ratio_height) / 2) * 2);
      $args[] = '-croptop';
      $args[] = $diff;
    }

    if ($ratio_width != $new_width) {
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
