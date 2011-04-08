<?php

class majaxMediaToolbox
{
  /**
   * @static
   * @param string $prefix
   * @return string
   */
  public static function uuid($prefix = '')
  {
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars, 0, 8) . '-';
    $uuid .= substr($chars, 8, 4) . '-';
    $uuid .= substr($chars, 12, 4) . '-';
    $uuid .= substr($chars, 16, 4) . '-';
    $uuid .= substr($chars, 20, 12);
    return $prefix . $uuid;
  }

  /**
   * @static
   * @param string $str
   * @param int $len
   * @param string $suffix
   * @return string
   */
  public static function truncate($str, $len = 30, $suffix = '...')
  {
    if (strlen($str) > $len) {
      $chop = $len - strlen($suffix);
      return trim(substr($str, 0, $chop)) . $suffix;
    }
    return $str;
  }
}
