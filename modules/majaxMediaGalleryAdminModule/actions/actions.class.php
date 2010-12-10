<?php

require_once dirname(__FILE__).'/../lib/BasemajaxMediaGalleryAdminModuleActions.class.php';

/**
 * majaxMediaGalleryAdminModule actions.
 * 
 * @package    majaxDoctrineMediaPlugin
 * @subpackage majaxMediaGalleryAdminModule
 * @author     Jacob Mather
 * @version    SVN: $Id: actions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class majaxMediaGalleryAdminModuleActions extends BasemajaxMediaGalleryAdminModuleActions
{
  public function executeLookup(sfWebRequest $request)
  {
    sfConfig::set('sf_admin_dash', false);
    sfConfig::set('sf_web_debug', false);
    $q = Doctrine_Query::create()->from('majaxMediaGallery g')->where('g.id = ?', $request->getParameter('value', null));
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
  public function executeLookupMany(sfWebRequest $request)
  {
    sfConfig::set('sf_admin_dash', false);
    sfConfig::set('sf_web_debug', false);
    $q = Doctrine_Query::create()->from('majaxMediaGallery g')->where('g.id IN ('.implode(',', $request->getParameter('values', array())).')');
    $items = $q->execute();
    if ($items)
    {
      $ret = array('status' => 'valid');
      $ret['results'] = array();
      foreach($items as $item)
        $ret['results'][] = $this->objectToArray($item);
    } else {
      $ret = array('status' => 'invalid');
    }
    $this->getLogger()->debug(var_Export($ret, true));
    echo json_encode($ret);
    exit(0);
    return sfView::NONE;
  }
  public function executeList(sfWebRequest $request)
  {
    sfConfig::set('sf_admin_dash', false);
    sfConfig::set('sf_web_debug', false);
    $this->getResponse()->addHttpMeta('content-type', 'text/xml');

    $this->getLogger()->debug(print_r($_REQUEST, true));

    $limit = $request->getParameter('limit', 10);
    $page = $request->getParameter('page', 1);
    $offset = ($page - 1) * $limit;
    $query = Doctrine_Query::create()->from('majaxMediaGallery g');

    $exclude = $request->getParameter('exclude', array());
    if ($request->getParameter('also_exclude', false))
      $exclude[] = $request->getParameter('also_exclude');

    if (count($exclude) > 0)
      $query->where('g.id NOT IN ('.implode(',', $exclude).')');

    $filter = trim($request->getParameter('filter', ''));
    if ($filter != '')
    {
      $fl = strlen($filter);
      $query->andWhere('LEFT(g.name, '.$fl.') = ?', $filter);
    }


    $sidx = $request->getParameter('sidx');
    $sord = $request->getParameter('sord');
    $sort = 'g.id DESC';
    switch($sidx)
    {
      case 'id':
        $sort = 'g.id '.$sord;
        break;
      case 'updated_at':
        $sort = 'g.updated_at '.$sord;
        break;
      case 'created_at':
        $sort = 'g.created_at '.$sord;
        break;
    }
    $total = $query->count();
    $query->limit($limit);
    $query->offset($offset);
    $query->orderby($sort);
    $items = $query->execute();
    $data = array('objects' => array());
    foreach($items as $item)
    {
      $d = $this->objectToArray($item);
      $data['objects'][] = $d;
    }
    $data['offset'] = $offset;
    $data['limit'] = $limit;
    $data['total'] = $total;
    $data['page'] = ($offset / $limit) + 1;
    $data['total_pages'] = ceil($total / $limit);
    $this->data = $data;
  }

  private function objectToArray($obj)
  {
    $d = array();
    $d['id'] = $obj->id;
    $d['name'] = $obj->name;
    $d['created_at'] = date('m/d/y g:ia', strtotime($obj->created_at));
    $d['updated_at'] = date('m/d/y g:ia', strtotime($obj->updated_at));
    return $d;
  }
}
