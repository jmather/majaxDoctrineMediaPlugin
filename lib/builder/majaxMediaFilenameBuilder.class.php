<?php

class majaxMediaFilenameBuilder
{
  public function render($source_file, $width, $height, $crop_method, $extension = null)
  {
    $file = basename($source_file);
    $dir = dirname($source_file);

    $name = $width.'x'.$height.'_'.$crop_method.'_'.$file;
    if ($extension !== null)
    {
      $name = $this->replaceExtension($name, $extension);
    }
    return $dir.DIRECTORY_SEPARATOR.$name;
  }
  protected function replaceExtension($name, $extension)
  {
    $name_bits = explode('.', $name);
    unset($name_bits[(count($name_bits) - 1)]);
    $new_name = implode('.', $name_bits).'.'.$extension;
    return $new_name;
  }
}
