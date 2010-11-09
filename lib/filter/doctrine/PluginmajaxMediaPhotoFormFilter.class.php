<?php

/**
 * PluginmajaxMediaPhoto form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginmajaxMediaPhotoFormFilter extends BasemajaxMediaPhotoFormFilter
{
  public function setup()
  {
    parent::setup();
    unset($this['image_file_id'], $this['content'], $this['content_html']);
  }
}
