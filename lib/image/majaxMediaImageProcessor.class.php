<?php

class majaxMediaImageProcessor extends majaxMediaProcessor
{
  protected function execute($src_path, $src_width, $src_height, $new_width, $new_height, $crop_method)
  {
    $new_path = $this->filename_builder->render($src_path, $new_width, $new_height, $crop_method);
    if (!$this->file_helper->exists($new_path))
    {
      if (!$this->file_helper->hasFileLock($src_path) && $this->file_helper->getFileLock($new_path))
      {
        try {
          $image = sfImage($src_path);
          $image->thumbnail($new_width, $new_height, $crop_method);
          $image->saveAs($new_path);
          $this->file_helper->removeFileLock($new_path);
        } catch (Exception $e) {
          $this->file_helper->removeFileLock($new_path);
          $new_path = $src_path;
        }
      } else {
        $new_path = $src_path;
      }
    }
    return $new_path;
  }
}
