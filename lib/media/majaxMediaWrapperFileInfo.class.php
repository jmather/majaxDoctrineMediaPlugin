<?php
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