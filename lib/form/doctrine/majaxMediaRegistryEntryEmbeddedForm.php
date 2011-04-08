<?php

/**
 * majaxMediaRegistryEntry Embedded form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class majaxMediaRegistryEntryEmbeddedForm extends majaxMediaRegistryEntryForm
{
  public function configure()
  {
    parent::configure();
    unset($this['id']);
    unset($this['uuid'], $this['video_media'], $this['audio_media'], $this['photo_media'], $this['gallery_media']);
    unset($this['created_at'], $this['updated_at']);
    $gal_widget_opts = array('multiple' => true, 'model' => 'majaxMediaGallery');
    if ($this->getObject()->getType() == 'Gallery') {
      $gal_widget_opts['exclude'] = $this->getObject()->getObject()->id;
    }
    $this->setWidget('galleries_list', new majaxMediaWidgetFormGallery($gal_widget_opts));
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    $this->saveGalleriesList($con);
  }

  public function isValid()
  {
    return true;
  }

  public function processValues($values)
  {
    $this->values = parent::processValues($values);
    $this->isBound = true;
    return $this->values;
  }
}
