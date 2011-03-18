<?php

class majaxMediaFilenameBuilder
{
  public function render($width, $height, $crop_method, $append, $extension = null)
  {
    $name = $width.'x'.$height.'_'.$crop_method.'_'.$append;
    if ($extension !== null)
    {
      $name = $this->replaceExtension($name, $extension);
    }
    return $name;
  }
  protected function replaceExtension($name, $extension)
  {
    $name_bits = explode('.', $name);
    unset($name_bits[(count($name_bits) - 1)]);
    $new_name = implode('.', $name_bits).'.'.$extension;
    return $new_name;
  }
}
