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
			$elapse = sfConfig::get('app_majaxMedia_lockfile_expiration', 30);
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
	}

	public static function removeFileLock($file)
	{
		$lock = static::getLockFile($file);
		unlink($lock);
	}
}
