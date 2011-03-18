<?php

/**
 * PluginmajaxMediaAudio form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginmajaxMediaAudioForm extends BasemajaxMediaAudioForm
{
  public function setup()
  {
    parent::setup();

    unset($this['created_by'], $this['last_updated_by']);
    unset($this['created_at'], $this['updated_at'], $this['version']);
    $this->setWidget('name', new sfWidgetFormInputText(array(), array('size'=>50)));
    $content_params = array('style' => 'height: 80px;');
    $this->setWidget('content', new majaxWidgetFormMarkdownEditor(array(), $content_params));
    $transcript_params = array('style' => 'height: 200px;');
    $this->setWidget('transcript', new majaxWidgetFormMarkdownEditor(array(), $transcript_params));

    $this->setWidget('recorded_on', new majaxWidgetFormDate());


    $this->setWidget('audio_file_id', new majaxMediaWidgetFormInputFile(array('file_id' => $this->getObject()->audio_file_id)));
    $this->setValidator('audio_file_id', new sfValidatorFile(
      array(
        'required' => false,
        'path' => sfConfig::get('sf_upload_dir'),
        'validated_file_class' => 'majaxMediaValidatedFile',
      )
    ));
    $this->setValidator('audio_file_delete', new sfValidatorPass());

    $this->setWidget('image_file_id', new majaxMediaWidgetFormInputFile(array('file_id' => $this->getObject()->image_file_id)));
    $this->setValidator('image_file_id', new sfValidatorFile(
      array(
        'required' => false,
        'path' => sfConfig::get('sf_upload_dir'),
        'validated_file_class' => 'majaxMediaValidatedFile',
      )
    ));
    $this->setValidator('image_file_delete', new sfValidatorPass());

    $context = sfContext::getInstance();
    if ($context && $context->getUser()->isAuthenticated() && $context->getUser()->getGuardUser()->is_super_admin)
    {
      $summary_params = array('jsoptions'=>array('height'=>200,'width'=>680));
      $this->setWidget('content_html', new sfWidgetFormCKEditor($summary_params));
      $summary_params = array('jsoptions'=>array('height'=>400,'width'=>680));
      $this->setWidget('transcript_html', new sfWidgetFormCKEditor($summary_params));
    } else {
      unset($this['content_html'], $this['transcript_html']);
    }

    if (sfConfig::get('app_majax_media_galleries', true))
    {
      $f = new majaxMediaRegistryEntryEmbeddedForm($this->getObject()->MediaRegistryEntry);
      $f->updateDefaultsFromObject();
      $this->embedMergeForm('media', $f);
    }
  }
}
