<?php
/*

// removed for uselessness

class majaxMediaWrapperPath extends majaxMediaWrapperManager
{
	protected $path = null;
	protected $path_hash = null;

	public function __construct($path)
	{
		if (!file_exists($path))
		{
			throw new InvalidArgumentException('Path "'.$path.'" is not a file.');
		}
		$this->path = $path;
		$this->path_hash = sha1($path);
	}

	public function getType()
	{
		return 'blah';
	}

	protected function getPhotoEditionQuery()
	{
		$q = Doctrine_Query::create()->from('PhotoEdition pe')->where('pe.path_hash = ?', $this->path_hash);
		return $q;
	}

	protected function getNewPhotoEdition()
	{
		$pe = new PhotoEdition();
		$pe->path_hash = $this->path_hash;
		return $pe;
	}

	public function getName()
	{
		return basename($this->path);
	}

	public function getSize()
	{
		return filesize($this->path);
	}

	public function getMime()
	{
		return mime_content_type($this->path);
	}

	protected function fetchOriginalFileInfo()
	{
		if ($this->file_info == null)
		{
			$this->file_info = array();
			$info = getimagesize($this->path);
			$this->file_info['width'] = $info[0];
			$this->file_info['height'] = $info[1];
		}
	}

	public function getData()
	{
		return file_get_contents($this->path);
	}
}

*/
