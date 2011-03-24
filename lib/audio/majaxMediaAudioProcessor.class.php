<?php

class majaxMediaAudioProcessor extends majaxMediaProcessor
{
  /**
   * We just override the process function from majaxMediaProcessor because we just need to ensure the source file is cached to disk.
   */
  public function process(majaxMediaFileInfo $file_info, $new_width, $new_height, $crop_method, $aspect_ratio, $extra_width = 0, $extra_height = 0)
  {
    $src_path = $this->ensureSourceFileIsCached($file_info);
    return $src_path;
  }
}

