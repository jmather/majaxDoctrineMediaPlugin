<div id="sf_admin_container">
  <h1>Sort Gallery Items</h1>

  <div id="sf_admin_header">
  </div>
  <div id="sf_admin_content">
    <div class="sf_admin_form">
      <fieldset id="sf_fieldset_none">
        <div class="sf_admin_form_row sf_admin_text">
          <?php include_partial('listSort', array('gallery' => $gallery)); ?>
        </div>
      </fieldset>
      <ul class="sf_admin_actions">
        <li class="sf_admin_action_edit">
          <?php echo link_to('Edit Gallery', 'galleries/edit?id=' . $gallery->id); ?>
        </li>
        <li class="sf_admin_action_list">
          <?php echo link_to('Back to list', 'galleries/index'); ?>
        </li>
    </div>
  </div>
  <div id="sf_admin_footer">
  </div>
</div>
