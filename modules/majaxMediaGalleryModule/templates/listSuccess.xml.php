<?php use_helper('majaxMedia'); ?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
        <channel>
                <title><?php echo $gallery->name; ?></title>
<?php foreach($gallery->Media as $idx => $gallery_item): ?>
<?php $gi_obj = $gallery_item->getObject(); ?>
                <item>
                        <pubDate><?php echo date('r', strtotime($gi_obj->updated_at)); ?></pubDate>
                        <description><?php echo strip_tags($gi_obj->getContentHtml(ESC_RAW)); ?></description>
                        <title><?php echo $gi_obj->name; ?></title>
<?php $media = majaxMedia($gallery_item); ?>
<?php $media->width($width); ?>
<?php $media->height($height); ?>
<?php $media->crop_method($crop_method); ?>
<?php $media->aspect_ratio($aspect_ratio); ?>
<?php if ($media->getType() == 'Photo'): ?>
                        <media:content url="<?php echo $media->photoToString(true, true); ?>" type="<?php echo $media->getPhotoMime(); ?>" duration="5" />
                        <media:thumbnail url="<?php echo $media->photoToString(true, true); ?>" />
<?php endif; ?>
<?php if ($media->getType() == 'Audio'): ?>
                        <media:content url="<?php echo $media->audioToString(true); ?>" type="audio/mpeg" duration="<?php echo $media->getLength(); ?>" />
                        <media:thumbnail url="<?php echo $media->photoToString(true); ?>" />
<?php endif; ?>
<?php if ($media->getType() == 'Video'): ?>
                        <media:content url="<?php echo $media->videoToString(true); ?>" type="video/x-flv" duration="<?php echo $media->getLength(); ?>" />
                        <media:thumbnail url="<?php echo $media->photoToString(true, true); ?>" />
<?php endif; ?>
                </item>
<?php endforeach; ?>
        </channel>
</rss>

