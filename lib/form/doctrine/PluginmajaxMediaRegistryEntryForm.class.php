<?php

/**
 * PluginmajaxMediaRegistryEntry form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginmajaxMediaRegistryEntryForm extends BasemajaxMediaRegistryEntryForm
{
  public function setup()
  {
    parent::setup();
    $cv = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'majaxMediaGallery', 'required' => false));
    $this->setValidator('galleries_list', $cv);
  }
}
