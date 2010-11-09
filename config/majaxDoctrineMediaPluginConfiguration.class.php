<?php

/**
 * majaxDoctrineMediaPlugin configuration.
 * 
 * @package     majaxDoctrineMediaPlugin
 * @subpackage  config
 * @author      Jacob Mather
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class majaxDoctrineMediaPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $modules = sfConfig::get('sf_enabled_modules', array());
    $modules[] = 'majaxMedia';
    $modules[] = 'majaxMediaPhotos';
    $modules[] = 'majaxMediaAudios';
    $modules[] = 'majaxMediaVideos';
    $modules[] = 'majaxMediaGalleries';
    $modules[] = 'majaxMediaAdminModule';
    $modules[] = 'majaxMediaGalleryAdminModule';
    sfConfig::set('sf_enabled_modules', $modules);
    sfConfig::set('majax_media_dir_name', 'media_cache');
    sfConfig::set('majax_media_dir', sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.sfConfig::get('majax_media_dir_name'));
  }
}
