This is the first working edition of my media plugin for symfony.

There are three main things to know:

1. This module relies on majaxJqueryPlugin (or an equal plugin that provides up-to-date jquery)
2. This module relies on sfImageTransformPlugin for it's photo management.
3. **If you use it on a commercial site, you will need a commercial license of JWPlayer, or another player.**

Now on to the good stuff:

###Set your path to FFMPEG:

In your config/app.yml:

    all:
    # ... snip ...
      majaxMedia:
        ffmpeg_path: /usr/local/bin/ffmpeg


###Enable the modules in your settings.yml:

    majaxMediaAdminModule
    majaxMediaAudios
    majaxMediaModule
    majaxMediaPhotos
    majaxMediaVideos


Then when you browse to 

    /your_app.php/photos [or]
    /your_app.php/videos [or]
    /your_app.php/audios

You will see management panes set up.

To be able to tie a media item to your object you will need to follow a couple steps:

###1. Update your schema:

    YourObject:
      columns:
    # ... snip ...
        media_id: { type: integer }
      relations:
    # ... snip ...
        MediaItem:
          local: media_id
          class: majaxMediaRegistryEntry
          onDelete: SET NULL


Rebuild your db and model classes.

###2. Your Form

Now in your lib/form/doctrine/YourObjectForm.class.php

    <?php
    class YourObjectForm extends BaseYourObjectForm
    {
      public function configure()
      {
        // ... snip ...
        $this->setWidget('media_id', new majaxMediaWidgetFormMedia());
      }
    }



You will then see a field which will let you bring up a popup window that will show you the latest media items you have uploaded.


###3. Your View

Now, for the best part, using this.

####In your view:

    <?php use_helper('majaxMedia'); ?>

    <?php echo majaxMedia($your_object->MediaItem)->width(400)->aspect_ratio('16:9')->crop_method('center'); ?>

or

    <?php $media = majaxMedia($your_object->MediaItem)->width(400)->aspect_ratio('16:9'); ?>
    <?php echo $media->crop_method('fit'); ?>
    <?php echo $media->crop_method('center'); ?>
    <?php echo $media->aspect_ratio('4:3'); ?>


It automatically picks up if it needs to show a player for video/audio content, and adds in extra height to compensate for the lack of a control bar on photos.


#TODO:

1. Build a better selection feature for attaching media items and (naturally) items to galleries.
2. I'm sure there's something that could be done to bring getid3 into php 5.3, but for now i'll just fix it on an as needed basis.
3. Make a rendering system for the player so players can be more easily swapped out
