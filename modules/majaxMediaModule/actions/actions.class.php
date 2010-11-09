<?php

require_once dirname(__FILE__).'/../lib/BasemajaxMediaModuleActions.class.php';

/**
 * majaxMediaModule actions.
 * 
 * @package    majaxDoctrineMediaPlugin
 * @subpackage majaxMediaModule
 * @author     Jacob Mather
 * @version    SVN: $Id: actions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class majaxMediaModuleActions extends BasemajaxMediaModuleActions
{
        public function executeIndex($request)
        {
                $this->forward('media', 'show');
        }
        public function executeShow($request)
        {
                sfConfig::set('sf_admin_dash', false);
                error_log(get_class($this->getRoute()));
                $this->file_info = $this->getRoute()->getObject();
                $this->forward404Unless($this->file_info);
                sfConfig::set('sf_web_debug', false);
                $this->file_data = $this->file_info->FileData;
                $this->getResponse()->addHttpMeta('content-type', $this->file_info->mime);

                $this->getResponse()->addHttpMeta('cache-control', 'public');
                $this->getResponse()->addHttpMeta('pragma', ' ');
                $this->getResponse()->addHttpMeta('last-modified', gmdate("D, d M Y H:i:s", strtotime($this->file_info->updated_at)).' GMT');

                $this->getResponse()->addHttpMeta('content-transfer-encoding', 'binary');
                $this->getResponse()->addHttpMeta('content-disposition', 'inline; filename="'.$this->file_info->name.'"');
                $this->getResponse()->addHttpMeta('content-length', $this->file_info->size);


//              echo fread($this->file_data->binary_data, $this->file_info->size);
//              exit(0);
//              $this->file_data = $this->serve($this->file_info);
                $this->setTemplate('show');
//              return 'dsfkjh';
        }
        public function executeView($request)
        {
                sfConfig::set('sf_admin_dash', false);
                list($id, $name) = explode('-', $request->getParameter('filenameid'), 2);
                $this->file_info = FileInfoPeer::retrieveByPk($id);

                $this->forward404Unless($this->file_info);
                sfConfig::set('sf_web_debug', false);
                $this->file_data = $this->file_info->getFileData();
                $this->getResponse()->addHttpMeta('content-type', $this->file_info->getMime());
                $this->getResponse()->addHttpMeta('content-length', $this->file_info->getSize());
//              echo fread($this->file_data->getBinaryData(), $this->file_info->getSize());
//              exit(0);
//              $this->file_data = $this->serve($this->file_info);
                $this->setTemplate('show');
//              return 'dsfkjh';
        }
        protected function serve($file_info)
        {
                $cache = new sfFileCache(array('cache_dir'=>sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'cache'));
                if($file_info->getIsCached() && $cache->has($file_info->getId(), 'uploaded_files'))
                {
                        $file_data = new FileData();
                        $file_data->setBinaryData($cache->get($file_info->getId(), 'uploaded_files'));
                } else {
                        $file_data = $file_info->getFileData();
                        $cache->set($file_info->getId(), 'uploaded_files', fread($file_data->getBinaryData(), $file_info->getSize()));
                        $file_info->setIsCached(true);
                        $file_info->save();
                }
                sfConfig::set('sf_web_debug', false);
//              $this->getResponse()->addHttpMeta('content-type', $file_info->getMime());
//              $this->getResponse()->addHttpMeta('content-length', $file_info->getSize());
                return $file_data;
        }
}
