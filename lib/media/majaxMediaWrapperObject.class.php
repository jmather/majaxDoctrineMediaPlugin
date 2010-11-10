<?php
class majaxMediaWrapperObject extends majaxMediaWrapperManager
{
	protected $obj = null;

	public function __construct($object)
	{
		parent::__construct();
		$this->obj = $object;
	}

	public function getType()
	{
		return $this->obj->getType();
	}

	public function getPhotoName()
	{
		return $this->obj->getObject()->PhotoFile->name;
	}

	public function getPhotoSize()
	{
		return $this->obj->getObject()->PhotoFile->size;
	}

	public function getPhotoMime()
	{	
		return $this->obj->getObject()->PhotoFile->mime;
	}

	public function getPhotoData()
	{
		return $this->obj->getObject()->PhotoFile->FileData->binary_data;
	}

	public function getAudioName()
	{
		return $this->obj->getObject()->AudioFile->name;
	}

	public function getAudioSize()
	{
		return $this->obj->getObject()->AudioFile->size;
	}

	public function getAudioMime()
	{	
		return $this->obj->getObject()->AudioFile->mime;
	}

	public function getAudioData()
	{
		return $this->obj->getObject()->AudioFile->FileData->binary_data;
	}

	public function getAudioLength()
	{
		return $this->obj->getObject()->AudioFile->getLength();
	}

	public function getVideoName()
	{
		return $this->obj->getObject()->VideoFile->name;
	}

	public function getVideoSize()
	{
		return $this->obj->getObject()->VideoFile->size;
	}

	public function getVideoMime()
	{	
		return $this->obj->getObject()->VideoFile->mime;
	}

	public function getVideoData()
	{
		return $this->obj->getObject()->VideoFile->FileData->binary_data;
	}

	public function getVideoLength()
	{
		return $this->obj->getObject()->VideoFile->getLength();
	}

	public function getPhotoWidth()
	{
		return $this->obj->getObject()->PhotoFile->getWidth();
	}

	public function getPhotoHeight()
	{
		return $this->obj->getObject()->PhotoFile->getHeight();
	}

	public function getVideoWidth()
	{
		return $this->obj->getObject()->VideoFile->getWidth();
	}

	public function getVideoHeight()
	{
		return $this->obj->getObject()->VideoFile->getHeight();
	}

	protected function isMedia()
	{
		return true;
	}
}

class majaxMediaWrapperFileInfo extends majaxMediaWrapperManager
{
	protected $obj = null;

	public function __construct($object)
	{
		parent::__construct();
		$this->obj = $object;
	}

	public function getType()
	{
		return $this->obj->getType();
	}

	public function getPhotoName()
	{
		return $this->obj->name;
	}

	public function getPhotoSize()
	{
		return $this->obj->size;
	}

	public function getPhotoMime()
	{	
		return $this->obj->mime;
	}

	public function getPhotoData()
	{
		return $this->obj->FileData->binary_data;
	}

	public function getAudioName()
	{
		return $this->obj->name;
	}

	public function getAudioSize()
	{
		return $this->obj->size;
	}

	public function getAudioMime()
	{	
		return $this->obj->mime;
	}

	public function getAudioData()
	{
		return $this->obj->FileData->binary_data;
	}

	public function getAudioLength()
	{
		return $this->obj->getLength();
	}

	public function getVideoName()
	{
		return $this->obj->name;
	}

	public function getVideoSize()
	{
		return $this->obj->size;
	}

	public function getVideoMime()
	{	
		return $this->obj->mime;
	}

	public function getVideoData()
	{
		return $this->obj->FileData->binary_data;
	}

	public function getVideoLength()
	{
		return $this->obj->getLength();
	}

	public function getPhotoWidth()
	{
		return $this->obj->getWidth();
	}

	public function getPhotoHeight()
	{
		return $this->obj->getHeight();
	}

	public function getVideoWidth()
	{
		return $this->obj->getWidth();
	}

	public function getVideoHeight()
	{
		return $this->obj->getHeight();
	}

}

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

class majaxMediaWrapperImage extends sfImage
{
}

if (!function_exists('mime_content_type'))
{
    function mime_content_type($file, $method = 0)
    {
        if ($method == 0)
        {
            ob_start();
            system('/usr/bin/file -i -b ' . realpath($file));
            $type = ob_get_clean();

            $parts = explode(';', $type);

            return trim($parts[0]);
        }
        else if ($method == 1)
        {
            // another method here
        }
    }
}

