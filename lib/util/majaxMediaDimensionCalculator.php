<?php

class majaxMediaDimensionCalculator
{
  /**
   * @param int $src_width
   * @param int $src_height
   * @param int|null $new_width
   * @param int|null $new_height
   * @param string $aspect_ratio
   * @return array
   */
  public function calculate($src_width, $src_height, $new_width = null, $new_height = null, $aspect_ratio = 'auto')
  {
    // If width or height is omitted, we need to calculate them.
    if ($new_width === null || $new_height === null) {
      list($new_width, $new_height) = $this->getRatioDimensions($src_width, $src_height, $new_width, $new_height, $aspect_ratio);
    } else {
      list($new_width, $new_height) = array($new_width, $new_height);
    }


    // FFMpeg likes things to be even.
    $new_width = (ceil($new_width / 2) * 2);
    $new_height = (ceil($new_height / 2) * 2);

    return array($new_width, $new_height);
  }

  /**
   * @param int $src_width
   * @param int $src_height
   * @param int|null $new_width
   * @param int|null $new_height
   * @param string $aspect_ratio
   * @return array
   */
  public function getRatioDimensions($src_width = 16, $src_height = 9, $new_width = null, $new_height = null, $aspect_ratio = 'auto')
  {
    if ($new_width === null && $new_height === null) {
      $new_width = $src_width;
    }

    if ($new_width === null) {
      if ($aspect_ratio == 'auto') {
        $new_width = round($new_height * $src_width / $src_height);
      } else {
        list($aw, $ah) = explode(':', $aspect_ratio, 2);
        $new_width = round($new_height * $aw / $ah);
      }
    }
    if ($new_height === null) {
      if ($aspect_ratio == 'auto') {
        $new_height = round($new_width * $src_height / $src_width);
      } else {
        list($aw, $ah) = explode(':', $aspect_ratio, 2);
        $new_height = round($new_width * $ah / $aw);
      }
    }
    return array($new_width, $new_height);
  }
}
