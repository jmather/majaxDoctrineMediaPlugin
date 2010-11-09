<?php
class majaxMediaGalleryItemListener extends Doctrine_Record_Listener
{
	public function __construct($options = array())
	{
		$this->_options = array_merge($this->_options, $options);
	}

	public function postDelete(Doctrine_Event $event)
	{
		$pos_name = $this->_options['name'];
		$obj = $event->getInvoker();
		$val = $obj->$pos_name;
		$gallery_id = $obj->gallery_id;
		$q = Doctrine_Query::create()->update('majaxMediaGalleryItem gi')->set($pos_name, $pos_name.' - 1');
		$q->where('gi.gallery_id = ?', $gallery_id)->andWhere($pos_name.' > ?', $val)->execute();
	}

	public function preInsert(Doctrine_Event $event)
	{
		$pos_name = $this->_options['name'];
		$obj = $event->getInvoker();
		$val = $obj->$pos_name;
		$gallery_id = $obj->gallery_id;
		if ($val == 0)
		{
			$q = Doctrine_Query::create()->select('MAX('.$pos_name.') as top')->from('majaxMediaGalleryItem gi');
			$result = $q->where('gi.gallery_id = ?', $gallery_id)->fetchOne();
			$obj->$pos_name = intval($result->top) + 1;
		} else {
			$q = Doctrine_Query::create()->update('majaxMediaGalleryItem gi')->set($pos_name, $pos_name.' + 1');
			$q->where('gi.gallery_id = ?', $gallery_id)->andWhere($pos_name.' >= ?', $val)->execute();
		}
	}
}
