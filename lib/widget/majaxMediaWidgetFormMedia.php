<?php
class majaxMediaWidgetFormMedia extends sfWidgetFormInput
{
  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $sfContext = sfContext::getInstance();
    $resp = $sfContext->getResponse();
    $resp->addJavascript('/majaxDoctrineMediaPlugin/js/grid.locale-en.js');
    $resp->addJavascript('/majaxDoctrineMediaPlugin/js/jquery.jqGrid.min.js');
    $resp->addStylesheet('/majaxDoctrineMediaPlugin/css/ui.jqgrid.css');
    $resp->addJavascript('/majaxDoctrineMediaPlugin/js/jquery.majax.media.js');

    $sfContext->getConfiguration()->loadHelpers(array('Url'));

    $id = $this->generateId($name);

    $fetch_url = url_for('majaxMediaAdminModule/list?sf_format=xml');
    $lookup_url = url_for('majaxMediaAdminModule/lookup');

    $out = $this->renderTag('input', array_merge(array('type' => 'text', 'name' => $name, 'value' => $value), $attributes));
    $out .= '<script type="text/javascript">
(function($){
  $(function(){
    var opts = {
      lookup_url: \'' . $lookup_url . '\',
      fetch_url: \'' . $fetch_url . '\',
    };
    $(\'#' . $id . '\').majaxmediaselector(opts);
  });
})(jQuery);
</script>
';
    return $out;
  }
}

