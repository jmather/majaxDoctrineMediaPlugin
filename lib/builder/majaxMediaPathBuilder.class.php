<?php

class majaxMediaPathBuilder
{
  public function render($hash)
  {
    return '/'.wordwrap($hash, 2, '/', true);
  }
}
