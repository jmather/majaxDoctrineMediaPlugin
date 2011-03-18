<?php

class majaxMediaToolbox
{
  public static function getLockFile($file)
  {
    return $file.'.lock';
  }

  public static function hasFileLock($file)
  {
    $lock = static::getLockFile($file);
    if (file_exists($lock))
    {
      $mtime = filemtime($lock);
      $elapse = sfConfig::get('app_majax_media_lockfile_expiration', 30);
      $limit = time() - $elapse;
      if ($mtime > $limit)
        return true;
      return false;
    }
    return false;
  }

  public static function getFileLock($file)
  {
    $lock = static::getLockFile($file);
    if (static::hasFileLock($file))
      return false;
    touch($lock);
    return true;
  }

  public static function removeFileLock($file)
  {
    $lock = static::getLockFile($file);
    unlink($lock);
  }

        public static function uuid($prefix = '')
        {
                $chars = md5(uniqid(mt_rand(), true));
                $uuid  = substr($chars,0,8) . '-';
                $uuid .= substr($chars,8,4) . '-';
                $uuid .= substr($chars,12,4) . '-';
                $uuid .= substr($chars,16,4) . '-';
                $uuid .= substr($chars,20,12);
                return $prefix . $uuid;
        }
        public static function truncate($str, $len = 30, $suffix = '...')
        {
                if (strlen($str) > $len)
                {
                        $chop = $len - strlen($suffix);
                        return trim(substr($str, 0, $chop)).$suffix;
                }
                return $str;
        }
}
