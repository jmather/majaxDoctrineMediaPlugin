<?php

class majaxMediaValidatedFile extends sfValidatedFile
{
  public function save($file = null, $fileMode = 0666, $create = true, $dirMode = 0777)
  {
    parent::save($file, $fileMode, $create, $dirMode);
    $file_data = new majaxMediaFileData();
    $data = fread(fopen($this->savedName, 'rb'), $this->size);
    $file_data->binary_data = $data;
    $file_data->save();
    $file_info = new majaxMediaFileInfo();
    $file_info->name = $this->originalName;
    $file_info->size = $this->size;
    $file_info->mime = $this->type;
    $file_info->file_data_id = $file_data->id;
    $file_info->save();
    unlink($this->savedName);
    return $file_info->id;
  }
}

