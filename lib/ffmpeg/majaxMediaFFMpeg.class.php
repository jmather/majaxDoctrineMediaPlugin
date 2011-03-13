<?php

class majaxMediaFFMpeg
{
	protected $file_info = null;

	protected $filename_builder = null;
	protected $path_builder = null;
	protected $cmd_line_builder = null;
	protected $executer;

	public function __construct()
	{
		$fb_class = sfConfig::get('app_majax_media_filename_builder', 'majaxMediaFilenameBuilder');
		$this->filename_builder = new $fb_class();
		$pb_class = sfConfig::get('app_majax_media_path_builder', 'majaxMediaPathBuilder');
		$this->path_builder = new $pb_class();
		$clb_class = sfConfig::get('app_majax_media_cmd_line_builder', 'majaxMediaFFMpegVideoTransformationBuilder');
		$this->cmd_line_builder = new $clb_class();
		$executer_class = sfConfig::get('app_majax_media_executer', 'majaxMediaCommandExecuter');
		$this->executer = new $executer_class();
	}
	
	public function setFilenameBuilder(majaxMediaFilenameBuilder $fnb)
	{
	  $this->filename_builder = $fnb;
	}
	
	public function setPathBuilder(majaxMediaPathBuilder $pb)
	{
	  $this->path_builder = $pb;
	}
	public function setCMDLineBuilder(majaxMediaPathBuilder $clb)
	{
	  $this->cmd_line_builder = $clb;
	}
	public function setExecuter(majaxMediaCommandExecuter $e)
	{
	  $this->executor = $e;
	}
	public function process(majaxMediaFileInfo $file_info)
	{
		$name = $file_info->getName();
		$sha1 = $file_info->getSha1();
		$path = $this->path_builder->render($sha1);
		$full_path = sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$name;
		if (!file_exists(sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$name))
		{
			$this->ensurePath($path, sfConfig::get('majax_media_dir'));
			$data = $file_info->getData();
			if (majaxMediaToolbox::getFileLock($full_path))
			{
				file_put_contents($full_path, $data);
				majaxMediaToolbox::removeFileLock($full_path);
			}
		}

		if ($this->get('width') !== null || $this->get('height') !== null)
		{
			$dims = $this->getRatioDimensions($this->get('width'), $this->get('height'), $this->getVideoWidth(), $this->getVideoHeight(), $this->get('aspect_ratio'));
			$new_width = $dims[0];
			$new_height = $dims[1];
		} else {
			$new_width = $this->getVideoWidth();
			$new_height = $this->getVideoHeight();
		}


		$name_bits = explode('.', $name);
		unset($name_bits[(count($name_bits) - 1)]);
		$new_name = implode('.', $name_bits).'.flv';


		$args = array('-i', $full_path, '-ar', '22050', '-b', '409600');

		$translator_class = sfConfig::get('app_majax_media_video_transformation_builder', 'majaxMediaFFMpegVideoTransformationBuilder');
		$translator = new $translator_class();

		switch($this->get('crop_method'))
		{
			case 'center':
			case 'left':
			case 'right':
			case 'top':
			case 'bottom':
				$s_w = $this->getVideoWidth();
				$s_h = $this->getVideoHeight();
				$c_m = $this->get('crop_method');
				$new_args = $translator->render($s_w, $s_h, $new_width, $new_height, $c_m);
				$args = array_merge($args, $new_args);
				break;
				
			case 'fit':
				// fit
				$ratio = min($new_height / $this->getVideoHeight(), $new_width / $this->getVideoWidth());
				$height_check = round($this->getVideoHeight() * $ratio);
				if ($height_check != $new_height)
				{
					$diff = $new_height - $height_check;
					$diff_top = floor($diff / 2);
					$diff_bot = $diff - $diff_top;
					$new_height = $new_height - abs($diff);
				}
				$width_check = round($this->getVideoWidth() * $ratio);
				if ($width_check != $new_width)
				{
					$diff = $new_width - $width_check;
					$diff_l = floor($diff / 2);
					$diff_r = $diff - $diff_l;
					$new_width = $new_width - abs($diff);
				}
		}




		$new_width = (ceil($new_width / 2) * 2);
		$new_height = (ceil($new_height / 2) * 2);

		$new_filename = $new_width.'x'.$new_height;
		$new_filename .= '_'.$this->get('crop_method', 'fit').'_'.$new_name;
		$new_partial_path = $path.DIRECTORY_SEPARATOR.$new_filename;



		// start the transformation code...

		$args[] = '-s';
		$args[] = $new_width.'x'.$new_height;

		$ffmpeg = sfConfig::get('app_majaxMedia_ffmpeg_path', '/usr/bin/ffmpeg');
		// now we need to figure out the cropping/padding


		$new_full_path = sfConfig::get('majax_media_dir').DIRECTORY_SEPARATOR.$new_partial_path;
		$args[] = $new_full_path;


		if ($ffmpeg == false || !file_exists($ffmpeg))
		{
			trigger_error('FFMPEG Not installed. Video source will not be resized', E_WARNING);
			$new_partial_path = $path.DIRECTORY_SEPARATOR.$name;
		}


		if (($ffmpeg != false && file_exists($ffmpeg)) && !file_exists($new_full_path))
		{
			foreach ($args as $i => $arg)
				$args[$i] = escapeshellarg ($arg);
		
			//echo($ffmpeg.' '.join(' ', $args));
			$count = 0;
			while (majaxMediaToolbox::hasFileLock($full_path) && !majaxMediaToolbox::hasFileLock($new_full_path))
			{
				usleep(500);
				$count++;
				if ($count == 10)
					break;
			}

			if (!majaxMediaToolbox::hasFileLock($full_path) && majaxMediaToolbox::getFileLock($new_full_path))
			{
			  $this->executor->setExecutable($ffmpeg);
			  $this->executor->setArguments($args);
			  $this->executor->execute();
				majaxMediaToolbox::removeFileLock($new_full_path);
			}
		}


		$new_partial_path = '/'.sfConfig::get('majax_media_dir_name').'/'.$new_partial_path;



		if ($path_only)
			return $new_partial_path;


		$render_class = sfConfig::get('app_majaxMedia_video_render', 'majaxMediaVideoRender');
		$render = new $render_class();
		return $render->render($this, $new_partial_path);
	}

	/**
	 * @param $sha1
	 * @return string
	 */
	protected function sha1ToPath($sha1)
	{
		return wordwrap($sha1, 2, DIRECTORY_SEPARATOR, true);
	}

	/**
	 * @param $path (presumed non-existant)
	 * @param $base (presumed existant)
	 * @return void
	 */

	protected function ensurePath($path, $base = '')
	{
		$dirs = explode(DIRECTORY_SEPARATOR, $path);
		$dir = $base;
		foreach($dirs as $c_dir)
		{
			$dir .= '/'.$c_dir;
			if (!file_exists($dir))
			{
				@mkdir($dir);
			}
		}
		if (file_exists($base.$path) && is_dir($base.$path))
		{
			return true;
		}
		return false;
	}
}
