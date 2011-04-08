<?php

require_once dirname(__FILE__) . '/../lib/majaxMediaGalleriesGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/majaxMediaGalleriesGeneratorHelper.class.php';

/**
 * majaxMediaGalleries actions.
 *
 * @package    dcms
 * @subpackage majaxMediaGalleries
 * @author     Jacob Mather
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class majaxMediaGalleriesActions extends autoMajaxMediaGalleriesActions
{
  public function executeListSort($request)
  {
    $this->gallery = $this->getRoute()->getObject();
  }

  public function executeReorder($request)
  {
    sfConfig::set('sf_web_debug', false);

    $pl = $request->getParameter('payload', '');

    $gallery_id = $request->getParameter('id');

    if ($pl == '')
      return sfView::NONE;

    $list = explode(',', $pl);

    $q = Doctrine_Query::create()->update('majaxMediaGalleryItem gi');
    $q->set('position', '?');
    $q->where('gi.gallery_id = ?');
    $q->andWhere('gi.media_id = ?');
    foreach ($list as $idx => $id)
    {
      $q->execute(array(($idx + 1), $gallery_id, $id));
    }
    echo $request->getParameter('payload');
    return sfView::NONE;
  }
}
