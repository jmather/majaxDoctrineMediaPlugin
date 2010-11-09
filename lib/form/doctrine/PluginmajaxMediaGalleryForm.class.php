<?php

/**
 * PluginmajaxMediaGallery form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginmajaxMediaGalleryForm extends BasemajaxMediaGalleryForm
{
  public function setup()
  {
    parent::setup();


    unset($this['created_at'], $this['updated_at'], $this['media_list'], $this['created_by'], $this['last_updated_by']);
    unset($this['media_list']);
    $this->setWidget('content', new majaxWidgetFormMarkdownEditor(array(), array('style' => 'height: 100px;')));

    $context = sfContext::getInstance();
    if ($context && $context->getUser()->isAuthenticated() && $context->getUser()->getGuardUser()->is_super_admin)
    {
      $summary_params = array('jsoptions'=>array('height'=>200,'width'=>680));
      $this->setWidget('content_html', new sfWidgetFormCKEditor($summary_params));
    } else {
      unset($this['content_html']);
    }

//    $f = new majaxMediaRegistryEntryEmbeddedForm($this->getObject()->MediaRegistryEntry);
//    $f->updateDefaultsFromObject();
//    $this->embedMergeForm('media', $f);
  }
}
