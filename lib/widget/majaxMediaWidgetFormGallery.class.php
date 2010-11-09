<?php

class majaxMediaWidgetFormGallery extends sfWidgetFormDoctrineChoice
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('exclude', false);
  }
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ($value === null)
      $value = array();
    if (!is_array($value))
      $value = array($value);

    $sfContext = sfContext::getInstance();
    $resp = $sfContext->getResponse();
    $resp->addJavascript('/majaxDoctrineMediaPlugin/js/grid.locale-en.js');
    $resp->addJavascript('/majaxDoctrineMediaPlugin/js/jquery.majax.gallery.js');
    $resp->addJavascript('/majaxDoctrineMediaPlugin/js/jquery.jqGrid.min.js');
    $resp->addStylesheet('/majaxDoctrineMediaPlugin/css/ui.jqgrid.css');
    $id = $this->generateId($name);

    $out = '';
    $out .= '<div id="'.$id.'_values">';
    foreach($value as $idx => $val)
    {
      $out .= '<input type="hidden" name="'.$name.'['.$val.']" id="'.$id.'_'.$val.'" value="'.$val.'" />';
    }
    $out .= '</div>';
    $out .= '<div id="'.$id.'">Loading...</div>';
    if ($this->getOption('exclude'))
    {
      $out .= '<script type="text/javascript">
(function($){
  $(function(){
    $(\'#'.$id.'\').majaxgalleryselector({name: \''.$name.'\', exclude: \''.$this->getOption('exclude').'\'});
  });
})(jQuery);
</script>
';
    } else {
      $out .= '<script type="text/javascript">
(function($){
  $(function(){
    $(\'#'.$id.'\').majaxgalleryselector({name: \''.$name.'\'});
  });
})(jQuery);
</script>
';
    }
    return $out;
  }
}

