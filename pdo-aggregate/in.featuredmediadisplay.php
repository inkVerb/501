<?php

// This displays Featured Media, such as in blog.php and piece.php

include ('./in.featuredmedia.php');

echo '<div id="featured-media" style="display:block">';

  // Video
  if (($feat_vid_showhide != 'none') && (($feat_vid_ext == 'mp4') || ($feat_vid_ext == 'ogg'))) {
    echo '<div id="featured-video" style="display:block"><video controls height="'.$vid_blog_max.'"><source src="'.$feat_vid_url.'" type="'.$feat_vid_mime.'"></video></div>';
  }

  // Audio/Document line
  if (($feat_aud_showhide != 'none') || ($feat_doc_showhide != 'none')) {

    echo '<div id="featured-audio-document" style="display:block">';

    // Audio
    if (($feat_aud_showhide != 'none') && (($feat_aud_ext == 'mp3') || ($feat_aud_ext == 'ogg'))) {
      echo '<div id="featured-audio" style="display:inline"><audio controls><source src="'.$feat_aud_url.'" type="'.$feat_aud_mime.'"></audio></div>';
    }

    // Document
    if (($feat_doc_showhide != 'none') && ($feat_doc_file != 'aggregated')) {
      echo '<div id="featured-document" style="display:inline"><big style="font-size:16pt;">&nbsp;&#x1F5CF;&nbsp;<code>'.$feat_doc_file_link.'</code></big></div>';
    }

    // End div
    echo '</div>';
  }

  // Image
  if (($feat_img_showhide != 'none') && ($feat_img_file != 'aggregated')) {
    echo '<div id="featured-image"><img style="max-width:'.$img_blog_max.'; max-height:'.$img_blog_max.';" title="'.$feat_img_file_title.'" alt="'.$feat_img_file_alt.'" src="'.$feat_img_url_blog.'"></div>';
  } elseif ($feat_img_file == 'aggregated') {
    echo '<div id="featured-image"><img style="max-width:'.$img_blog_max.'; max-height:'.$img_blog_max.';" title="" alt="" src="'.$feat_img_url.'"></div>';
  }

echo '</div>';
