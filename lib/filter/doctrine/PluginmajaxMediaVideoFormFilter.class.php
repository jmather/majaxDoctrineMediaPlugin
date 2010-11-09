<?php

/**
 * PluginmajaxMediaVideo form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginmajaxMediaVideoFormFilter extends BasemajaxMediaVideoFormFilter
{
  public function setup()
  {
    parent::setup();
    unset($this['video_file_id'], $this['image_file_id'], $this['content'], $this['content_html']);
    unset($this['transcript'], $this['transcript_html']);
  }
}
