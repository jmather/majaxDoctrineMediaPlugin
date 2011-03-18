<style>
  .sort_list li {
    display: inline-block;
    margin: 5px;
  }
  .sort_list {
    margin: 0px;
    padding: 0px;
  }
  .photo { border: 1px solid black; padding: 5px; }
  .photo .name { display: block; }
  .photo .name small { float: right; }
  .handle { position: relative; display: block; left: -5px; top: -5px; float: left; width: 5px; background-color: black; height: 129px; cursor: move; }
</style>
<script type="text/javascript">
$(function() {
  $('#photo-list').sortable({
    axis: false,
    tolerance: 'pointer',
    opacity: 0.75,
    appendTo: 'body',
    distance: 1,
    items: 'li',
    scroll: true,
    helper: 'original',
    cancel: ':input,button,object',
    update : function () {                   
      setTimeout('updatePositions();', 500);
    },
    beforeStop: function(event, ui) {
      $(ui.helper).hide();
    },
    stop: function(event, ui) {
      $(ui.item).hide();
      $(ui.item).fadeIn('slow');
    }
  });
});

function updatePositions()
{
  var org = $('#photo-list').sortable('toArray');
  var arr = new Array();

  for (var i in org)
  {
    var n = org[i].split('_');
    arr[arr.length] = n[1];
  }

  var data = {
    payload: arr.join(','),
    id: <?php echo $gallery->id; ?>
  };

  $.post('<?php echo url_for('majaxMediaGalleries/reorder'); ?>', data, updatePositionsCallback, 'text');
}

function updatePositionsCallback(res)
{
  // not needed
}
</script>
<ul class="sort_list" id="photo-list">
<?php use_helper('majaxMedia'); ?>
<?php foreach($gallery->Media as $media): ?>
<li class="photo" id="photo_<?php echo $media->id; ?>">
<?php $m = majaxMedia($media)->width(220)->aspect_ratio('16:9')->crop_method('center'); ?>
  <span class="image"><?php echo $m; ?>
  <span class="name"><span title="<?php echo $m->getName(); ?>"><?php echo majaxMediaToolbox::truncate($m->getName(), 30); ?></span> <small><?php echo $m->getFormattedSize(); ?></small></span>
</li>
<?php endforeach; ?>
</ul>
