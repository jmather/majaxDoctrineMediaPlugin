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

    $fetch_url = url_for('majaxMediaGalleryAdminModule/list?sf_format=xml', true);
    $lookup_url = url_for('majaxMediaGalleryAdminModule/lookupMany', true);
    $exclude = ($this->getOption('exclude')) ? $this->getOption('exclude') : false;


    $out = '';
    $out .= '<div id="' . $id . '_values">';
    foreach ($value as $idx => $val)
    {
      $out .= '<input type="hidden" name="' . $name . '[' . $val . ']" id="' . $id . '_' . $val . '" value="' . $val . '" />';
    }
    $out .= '</div>';
    $out .= '<div id="' . $id . '">Loading...</div>';
    $out .= '<script type="text/javascript">
(function($){
  $(function(){
    var opts = {
      name: \'' . $name . '\',
      exclude: ' . var_export($exclude, true) . ',
      fetch_url: \'' . $fetch_url . '\',
      lookup_url: \'' . $lookup_url . '\'
    }
    $(\'#' . $id . '\').majaxgalleryselector(opts);
  });
})(jQuery);
</script>
';
    return $out;
  }
}

