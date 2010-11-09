<?php

/**
 * PluginmajaxMediaAudio form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginmajaxMediaAudioFormFilter extends BasemajaxMediaAudioFormFilter
{
  public function setup()
  {
    parent::setup();
    unset($this['audio_file_id'], $this['image_file_id'], $this['content'], $this['content_html']);
    unset($this['transcript'], $this['transcript_html']);
  }
}
