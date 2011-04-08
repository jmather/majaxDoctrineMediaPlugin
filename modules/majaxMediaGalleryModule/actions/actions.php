<?php

require_once dirname(__FILE__) . '/../lib/BasemajaxMediaGalleryModuleActions.class.php';

/**
 * majaxMediaGalleryModule actions.
 *
 * @package    majaxDoctrineMediaPlugin
 * @subpackage majaxMediaGalleryModule
 * @author     Jacob Mather
 * @version    SVN: $Id: actions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class majaxMediaGalleryModuleActions extends BasemajaxMediaGalleryModuleActions
{
  public function executeList(sfRequest $request)
  {
    $id = $request->getParameter('id');
    $checksum = md5($id . sfConfig::get('sf_csrf_secret'));
    if ($checksum != $request->getParameter('checksum'))
      $this->forward404();

    $this->gallery = Doctrine::getTable('majaxMediaGallery')->find($id);
    $this->forward404Unless($this->gallery);
    $this->setLayout(false);
    sfConfig::set('sf_web_debug', false);
    sfConfig::set('sf_debug', false);
    $this->getResponse()->setHttpHeader('Content-Type', 'text/xml');
    $this->width = $request->getParameter('width', 400);
    $this->height = $request->getParameter('height', null);
    $this->aspect_ratio = str_replace('x', ':', $request->getParameter('aspect_ratio', '16:9'));
    $this->crop_method = $request->getParameter('crop_method', 'center');
  }
}
