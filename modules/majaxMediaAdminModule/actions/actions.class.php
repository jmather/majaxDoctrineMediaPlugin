<?php

require_once dirname(__FILE__).'/../lib/BasemajaxMediaAdminModuleActions.class.php';

/**
 * majaxMediaAdminModule actions.
 * 
 * @package    majaxDoctrineMediaPlugin
 * @subpackage majaxMediaAdminModule
 * @author     Jacob Mather
 * @version    SVN: $Id: actions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class majaxMediaAdminModuleActions extends BasemajaxMediaAdminModuleActions
{
  public function executeLookup(sfWebRequest $request)
  {
    sfConfig::set('sf_admin_dash', false);
    sfConfig::set('sf_web_debug', false);
    $q = Doctrine_Query::create()->from('majaxMediaRegistryEntry mre')->where('mre.id = ?', $request->getParameter('value', null));
    $item = $q->fetchOne();
    if ($item)
    {
      $ret = array('status' => 'valid');
      $ret = array_merge($this->objectToArray($item), $ret);
    } else {
      $ret = array('status' => 'invalid');
    }
    echo json_encode($ret);
    exit(0);
    return sfView::NONE;
  }
  public function executeList(sfWebRequest $request)
  {
    sfConfig::set('sf_admin_dash', false);
    sfConfig::set('sf_web_debug', false);
    $this->getLogger()->debug(print_r($_REQUEST, true));
    $this->getResponse()->addHttpMeta('content-type', 'text/xml');


    $type = $request->getParameter('media_type', null);
    $limit = $request->getParameter('limit', 10);
    $page = $request->getParameter('page', 1);
    $offset = ($page - 1) * $limit;
    $media_items_query = Doctrine_Query::create()->from('majaxMediaRegistryEntry mre');
    switch($type)
    {
      case 'audio':
        $media_items_query->addWhere('audio_media > 0');
        $media_items_query->leftJoin('mre.Audio m');
        break;
      case 'photo':
        $media_items_query->addWhere('photo_media > 0');
        $media_items_query->leftJoin('mre.Photo m');
        break;
      case 'video':
        $media_items_query->addWhere('video_media > 0');
        $media_items_query->leftJoin('mre.Video m');
        break;
    }

    $sidx = $request->getParameter('sidx');
    $sord = $request->getParameter('sord');
    $sort = 'mre.id DESC';
    switch($sidx)
    {
      case 'id':
        $sort = 'mre.id '.$sord;
        break;
      case 'updated_at':
        $sort = 'updated_at '.$sord;
        break;
      case 'created_at':
        $sort = 'created_at '.$sord;
        break;
    }
    $total = $media_items_query->count();
    $media_items_query->limit($limit);
    $media_items_query->offset($offset);
    $media_items_query->orderby($sort);
    $items = $media_items_query->execute();
    $data = array('objects' => array());
    foreach($items as $item)
    {
      $d = $this->objectToArray($item);
      $data['objects'][] = $d;
    }
    $data['offset'] = $offset;
    $data['limit'] = $limit;
    $data['type'] = $type;
    $data['total'] = $total;
    $data['page'] = ($offset / $limit) + 1;
    $data['total_pages'] = ceil($total / $limit);
    $this->data = $data;
  }

  private function objectToArray($obj)
  {
    $d = array();
    $d['id'] = $obj->id;
    $o = $obj->getObject(true);
    $d['name'] = $o->name;
    $d['type'] = $obj->type;
    $d['last_updated_by'] = ($o->LastUpdatedBy) ? $o->LastUpdatedBy->__toString() : '';
    $d['created_by'] = ($o->CreatedBy) ? $o->CreatedBy->__toString() : '';
    $d['created_at'] = date('m/d/y g:ia', strtotime($obj->created_at));
    $d['updated_at'] = date('m/d/y g:ia', strtotime($obj->updated_at));
    return $d;
  }
}
