<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// SQL for current server timestamp: SELECT CURRENT_TIMESTAMP;

// Time duration function Thanks https://stackoverflow.com/a/48187008/10343144
function validTime($time, $format='H:i:s') {
 $d = DateTime::createFromFormat("Y-m-d $format", "2017-12-01 $time");
 return $d && $d->format($format) == $time;
}

// Current time of SQL server
$query = $database->prepare("SELECT CURRENT_TIMESTAMP;");
$rows = $pdo->exec_($query);
foreach ($rows as $row) { $curr_time_sql = $row->CURRENT_TIMESTAMP; }
$curr_time_php = strtotime($curr_time_sql);

// Series info
if ((isset($agg_id)) && ($process_action == true)) {
  $query = $database->prepare("SELECT id, name, source, update_interval, status, last_updated, series FROM aggregation WHERE id='$agg_id'");
}
$rows = $pdo->exec_($query);
foreach ($rows as $row) {

  // Assign basic values
  $f_id = $row->id;
  $f_source = $row->source;
  $f_update_interval = $row->update_interval;
  $f_status = $row->status;
  $f_last_updated_sql = $row->last_updated;
  $f_series = $row->series;
  $f_type = 'post';
  $f_name = ((isset($agg_id)) && ($process_action == true)) ? $row->name : 'false';

  // Test source
  if (!filter_var($f_source,FILTER_VALIDATE_URL)) { continue; }

  // Next update
  $f_last_updated_php = strtotime($f_last_updated_sql);
  $next_update_php = $f_last_updated_php + ($f_update_interval * 60);

  // Time to update?
  if ((((isset($agg_id)) && ($process_action == true)) || ($next_update_php <= $curr_time_php)) && ($f_status == 'active')) {
    // Fetch the feed
    $rss = simplexml_load_file($f_source);

    // Skip if not valid
    if ((!$rss)
    || ($rss['version'] != '2.0')
    || (!isset($rss->channel->title))
    || (!isset($rss->channel->link))
    || (!isset($rss->channel->lastBuildDate))
    || (!isset($rss->channel->description))) {
      // Set status to problematic
      $query = $database->prepare("UPDATE aggregation SET status='problematic' WHERE id=:id");
      $query->bindParam(':id', $f_id);
      $pdo->exec_($query);
      continue;
    }

    // Refresh action?
    echo ((isset($agg_id)) && ($process_action == true)) ? '<h1>Fetching '.$f_name.'...</h1>' : false;

    // Each feed item
    foreach ($rss->channel->item as $item) {

      $itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
      $content = $item->children('http://purl.org/rss/1.0/modules/content/');
      $atom = $item->children('http://www.w3.org/2005/Atom'); // For future use
      $dc = $item->children('http://purl.org/dc/elements/1.1/');

      // Parse every item we are ready to handle

      // Title
      $f_title = $item->title;
      $regex_replace = "/[^0-9a-zA-Z_ !@&#$%.,+-=\/|]/";
      $f_title = preg_replace($regex_replace,"", $f_title);
      // Exit if this is empty
      if ((!isset($item->title)) || ($f_title == '')) { continue; }
      // Refresh action?
      echo ((isset($agg_id)) && ($process_action == true)) ? '<pre>'.$f_title.'</pre>' : false;

      // Description
      // <description> gets priority
      $f_subtitle = $item->description;
      $regex_replace = "/[^0-9a-zA-Z_ !@&#$%.,+-=\/|]/";
      $f_subtitle = preg_replace($regex_replace,"", $f_subtitle);
      // Make sure our variables are not empty
      $f_subtitle = (isset($item->description)) ? $f_subtitle : NULL;

      // Excerpt / Summary
      $f_excerpt = $itunes->summary;
      $f_excerpt = preg_replace('/([A-Z].[a-z]+)-([A-Z].[a-z]+)/','$1–$2',$f_excerpt); // Proper noun range to en-dash
      $f_excerpt = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$f_excerpt); // number range to en-dash
      $f_excerpt = str_replace('---','—',$f_excerpt); // to em-dash
      $f_excerpt = str_replace(' -- ',' – ',$f_excerpt); // to en-dash
      $f_excerpt = str_replace(' --','—',$f_excerpt); // to em-dash
      $f_excerpt = str_replace('-- ','—',$f_excerpt); // to em-dash
      $f_excerpt = str_replace('--','—',$f_excerpt); // to em-dash
      $f_excerpt = strip_tags($f_excerpt); // Remove any HTML tags
      $f_excerpt = substr($f_excerpt, 0, 65530); // Limit to 65,530 characters for TEXT datatype
      // Make sure our variables are not empty
      $f_excerpt = (isset($itunes->summary)) ? $f_excerpt : NULL;

      // Content
      $f_content = $content->encoded;
      $f_content = preg_replace('/([A-Z].[a-z]+)-([A-Z].[a-z]+)/','$1–$2',$f_content); // Proper noun range to en-dash
      $f_content = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$f_content); // number range to en-dash
      $f_content = str_replace('---','—',$f_content); // to em-dash
      $f_content = str_replace(' -- ',' – ',$f_content); // to en-dash
      $f_content = str_replace(' --','—',$f_content); // to em-dash
      $f_content = str_replace('-- ','—',$f_content); // to em-dash
      $f_content = str_replace('--','—',$f_content); // to em-dash
      $f_content = htmlspecialchars($f_content); // Convert HTML tags to their HTML entities
      $f_content = substr($f_content, 0, 4294967290); // Limit to 4,294,967,290 characters for LONGTEXT datatype
      // Exit if this is empty
      if ((!isset($f_content)) || ($f_content == '')) { continue; }

      // Featured media
      foreach ($item->enclosure as $enclosure) {
        switch ($enclosure['type']) {
          case 'image/jpeg':
          case 'image/png':
          case 'image/gif':
            $f_feat_img = $enclosure['url'];
            $f_feat_img_length = (filter_var($enclosure['length'], FILTER_VALIDATE_INT, array('min_range' => 1))) ? $enclosure['length'] : 0;
            $f_feat_mime = $enclosure['type'];
            $f_feat_img = ((filter_var($f_feat_img,FILTER_VALIDATE_URL)) && (strlen($f_feat_img) <= 2048))
            ? substr(preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $f_feat_img),0,2048) : '';
          break;

          case 'audio/mpeg':
          case 'audio/mpeg3':
          case 'audio/x-mpeg':
          case 'audio/x-mpeg-3':
          case 'audio/ogg':
          case 'audio/x-wav':
          case 'audio/wav':
          case 'audio/x-flac':
          case 'audio/flac':
            $f_feat_aud = $enclosure['url'];
            $f_feat_aud_length = (filter_var($enclosure['length'], FILTER_VALIDATE_INT, array('min_range' => 1))) ? $enclosure['length'] : 0;
            $f_feat_aud_mime = $enclosure['type'];
            $f_feat_aud = ((filter_var($f_feat_aud,FILTER_VALIDATE_URL)) && (strlen($f_feat_aud) <= 2048))
            ? substr(preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $f_feat_aud),0,2048) : '';
          break;

          case 'video/mp4':
          case 'video/ogg':
          case 'video/x-theora+ogg':
          case 'video/webm':
          case 'video/x-flv':
          case 'video/x-msvideo':
          case 'video/x-matroska':
          case 'video/quicktime':
            $f_feat_vid = $enclosure['url'];
            $f_feat_vid_length = (filter_var($enclosure['length'], FILTER_VALIDATE_INT, array('min_range' => 1))) ? $enclosure['length'] : 0;
            $f_feat_vid_mime = $enclosure['type'];
            $f_feat_vid = ((filter_var($f_feat_vid,FILTER_VALIDATE_URL)) && (strlen($f_feat_vid) <= 2048))
            ? substr(preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $f_feat_vid),0,2048) : '';
          break;

          case 'application/pdf':
          case 'application/x-pdf':
          case 'text/plain':
          case 'text/html':
          case 'application/msword':
          case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
          case 'application/vnd.oasis.opendocument.text':
            $f_feat_doc = $enclosure['url'];
            $f_feat_doc_length = (filter_var($enclosure['length'], FILTER_VALIDATE_INT, array('min_range' => 1))) ? $enclosure['length'] : 0;
            $f_feat_doc_mime = $enclosure['type'];
            $f_feat_doc = ((filter_var($f_feat_doc,FILTER_VALIDATE_URL)) && (strlen($f_feat_doc) <= 2048))
            ? substr(preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $f_feat_doc),0,2048) : '';
          break;
        } // switch
      } // foreach
      // Make sure our variables are not empty
      $f_feat_img = (isset($f_feat_img)) ? $f_feat_img : '0';
      $f_feat_aud = (isset($f_feat_aud)) ? $f_feat_aud : '0';
      $f_feat_vid = (isset($f_feat_vid)) ? $f_feat_vid : '0';
      $f_feat_doc = (isset($f_feat_doc)) ? $f_feat_doc : '0';
      $f_feat_img_length = (isset($f_feat_img_length)) ? $f_feat_img_length : '0';
      $f_feat_aud_length = (isset($f_feat_aud_length)) ? $f_feat_aud_length : '0';
      $f_feat_vid_length = (isset($f_feat_vid_length)) ? $f_feat_vid_length : '0';
      $f_feat_doc_length = (isset($f_feat_doc_length)) ? $f_feat_doc_length : '0';
      $f_feat_img_mime = (isset($f_feat_img_mime)) ? $f_feat_img_mime : '0';
      $f_feat_aud_mime = (isset($f_feat_aud_mime)) ? $f_feat_aud_mime : '0';
      $f_feat_vid_mime = (isset($f_feat_vid_mime)) ? $f_feat_vid_mime : '0';
      $f_feat_doc_mime = (isset($f_feat_doc_mime)) ? $f_feat_doc_mime : '0';

      // Date live
      $f_live = $item->pubDate;
      $f_live_php = strtotime($f_live);
      $f_live_sql = date("Y-m-d H:i:s", substr($f_live_php, 0, 10));
      // Make sure our variables are not empty
      $f_live_sql = (isset($f_live_sql)) ? $f_live_sql : $curr_time_sql;

      // Tags
      $f_tags = $itunes->keywords;
      $regex_replace = "/[^a-zA-Z0-9, ]/";
      $f_tags = strtolower(preg_replace($regex_replace," ", $f_tags)); // Lowercase, all non-alnum & comma to space
      $f_tags = substr($f_tags, 0, 55530); // Limit to 55,530 characters to stay safe within TEXT datatype after JSON conversion
      $f_tags_json = json_encode(explode(', ', $f_tags)); // Convert into JSON objects
      $f_tags_sqljson = (json_decode($f_tags_json)) ? $f_tags_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false

      // Duration
      $f_duration = (validTime($itunes->duration)) ? $itunes->duration : 0;

      // Slug & GUID
      // Slug
      $f_slug = $f_title;
      $regex_replace = "/[^a-zA-Z0-9-]/";
      $f_slug = strtolower(preg_replace($regex_replace,"-", $f_slug)); // Lowercase, all non-alnum to hyphen
      $f_slug = substr($f_slug, 0, 95); // Limit to 95 characters
      $f_slug_test_trim = DB::trimspace($f_slug);
      // GUID
      if ((isset($item->guid)) && ($item->guid != '')) {
        $f_guid = 'rss-'.$f_id.'-'.$item->guid;
      } else { // No GUID, so use the slug instead
        $f_guid = 'rss-'.$f_id.'-'.$f_slug;
      }
      $f_guid_test_trim = DB::trimspace($f_guid);

      // Test database
      $query = $database->prepare("SELECT id FROM pieces WHERE slug=:slug");
      $query->bindParam(':slug', $f_slug_test_trim);
      $querypub = $database->prepare("SELECT id FROM publications WHERE slug=:slug AND NOT guid=:guid");
      $querypub->bindParam(':slug', $f_slug_test_trim);
      $querypub->bindParam(':guid', $f_guid_test_trim);
      $pdo->exec_($query);
      $piecesrows = $pdo->numrows;
      $pdo->exec_($querypub);
      $publicationsrows = $pdo->numrows;
      // Loop through new slug if needed
      if (($piecesrows > 0) || ($publicationsrows > 0) || ($f_slug_test_trim == 'feed')) {
        $add_num = 0;
        $dup = true;
        // If there were no changes
        while ($dup = true) {
          $add_num = $add_num + 1;
          $new_f_slug = $f_slug.'-'.$add_num;
          // In case this gets longer than allowed characters
          $new_f_slug = ($add_num == 1) ? substr($new_f_slug, 0, 93) : $new_f_slug;
          $new_f_slug = ($add_num == 10) ? substr($new_f_slug, 0, 92) : $new_f_slug;
          $new_f_slug = ($add_num == 100) ? substr($new_f_slug, 0, 91) : $new_f_slug;
          $new_f_slug = ($add_num == 1000) ? substr($new_f_slug, 0, 90) : $new_f_slug;
          $new_f_slug = ($add_num == 10000) ? substr($new_f_slug, 0, 89) : $new_f_slug;
          $new_f_slug = ($add_num == 100000) ? substr($new_f_slug, 0, 88) : $new_f_slug;
          $new_f_slug_test_trim = DB::trimspace($new_f_slug);
          // Check again
          $query = $database->prepare("SELECT id FROM pieces WHERE slug=:slug");
          $query->bindParam(':slug', $new_f_slug_test_trim);
          $querypub = $database->prepare("SELECT id FROM publications WHERE slug=:slug");
          $querypub->bindParam(':slug', $new_f_slug_test_trim);
          $pdo->exec_($query);
          $piecesrows = $pdo->numrows;
          $pdo->exec_($querypub);
          $publicationsrows = $pdo->numrows;
          if (($piecesrows == 0) && ($publicationsrows == 0)) {
            $f_slug = $new_f_slug;
            break;
          }
        }
      }

      // Is this a dup to be updated?
      $query = $database->prepare("SELECT id FROM publications WHERE guid=:guid");
      $query->bindParam(':guid', $f_guid_test_trim);
      $pdo->exec_($query);
      $rows = $pdo->numrows;
      // Updating?
      if ($rows > 0) {
        $pub_update = true; // We already changed the GUID on this and it exists, update this entry
      }

      // Trim everything
      $f_slug_trim = DB::trimspace($f_slug);
      $f_type_trim = DB::trimspace($f_type);
      $f_series_trim = DB::trimspace($f_series);
      $f_title_trim = DB::trimspace($f_title);
      $f_subtitle_trim = DB::trimspace($f_subtitle);
      $f_content_trim = DB::trimspace($f_content);
      $f_excerpt_trim = DB::trimspace($f_excerpt);
      $f_duration_trim = DB::trimspace($f_duration);
      $f_guid_trim = DB::trimspace($f_guid);
      $f_feat_img_trim = DB::trimspace($f_feat_img);
      $f_feat_aud_trim = DB::trimspace($f_feat_aud);
      $f_feat_vid_trim = DB::trimspace($f_feat_vid);
      $f_feat_doc_trim = DB::trimspace($f_feat_doc);
      $f_feat_img_length_trim = DB::trimspace($f_feat_img_length);
      $f_feat_aud_length_trim = DB::trimspace($f_feat_aud_length);
      $f_feat_vid_length_trim = DB::trimspace($f_feat_vid_length);
      $f_feat_doc_length_trim = DB::trimspace($f_feat_doc_length);
      $f_feat_img_mime_trim = DB::trimspace($f_feat_img_mime);
      $f_feat_aud_mime_trim = DB::trimspace($f_feat_aud_mime);
      $f_feat_vid_mime_trim = DB::trimspace($f_feat_vid_mime);
      $f_feat_doc_mime_trim = DB::trimspace($f_feat_doc_mime);
      $f_live_trim = DB::trimspace($f_live_sql);

      // Update or new entry?
      if ((isset($pub_update)) && ($pub_update = true)) {
        $query = $database->prepare("UPDATE publications SET type=:type, series=:series, duration=:duration, title=:title, subtitle=:subtitle, slug=:slug, content=:content, excerpt=:excerpt, tags=:tags, feat_img=:feat_img, feat_aud=:feat_aud, feat_vid=:feat_vid, feat_doc=:feat_doc, feat_img=:feat_img_length, feat_aud=:feat_aud_length, feat_vid=:feat_vid_length, feat_doc=:feat_doc_length, feat_img=:feat_img_mime, feat_aud=:feat_aud_mime, feat_vid=:feat_vid_mime, feat_doc=:feat_doc_mime, date_live=:date_live WHERE guid=:guid AND aggregated=:aggregated");
      } else { // Insert into the database
        $query = $database->prepare("INSERT INTO publications
          (piece_id, type, series, aggregated, title, subtitle, slug, content, excerpt, duration, guid, tags, feat_img, feat_aud, feat_vid, feat_doc, feat_img_length, feat_aud_length, feat_vid_length, feat_doc_length, feat_img_mime, feat_aud_mime, feat_vid_mime, feat_doc_mime, date_live)
          VALUES (0, :type, :series, :aggregated, :title, :subtitle, :slug, :content, :excerpt, :duration, :guid, :tags, :feat_img, :feat_aud, :feat_vid, :feat_doc, :feat_img_length, :feat_aud_length, :feat_vid_length, :feat_doc_length, :feat_img_mime, :feat_aud_mime, :feat_vid_mime, :feat_doc_mime, :date_live)");
      }

      // Run the query
      $query->bindParam(':type', $f_type_trim);
      $query->bindParam(':series', $f_series_trim);
      $query->bindParam(':aggregated', $f_id);
      $query->bindParam(':title', $f_title_trim);
      $query->bindParam(':subtitle', $f_subtitle_trim);
      $query->bindParam(':slug', $f_slug_trim);
      $query->bindParam(':content', $f_content_trim);
      $query->bindParam(':excerpt', $f_excerpt_trim);
      $query->bindParam(':duration', $f_duration_trim);
      $query->bindParam(':guid', $f_guid_trim);
      $query->bindParam(':tags', $f_tags_sqljson);
      $query->bindParam(':feat_img', $f_feat_img);
      $query->bindParam(':feat_aud', $f_feat_aud);
      $query->bindParam(':feat_vid', $f_feat_vid);
      $query->bindParam(':feat_doc', $f_feat_doc);
      $query->bindParam(':feat_img_length', $f_feat_img_length);
      $query->bindParam(':feat_aud_length', $f_feat_aud_length);
      $query->bindParam(':feat_vid_length', $f_feat_vid_length);
      $query->bindParam(':feat_doc_length', $f_feat_doc_length);
      $query->bindParam(':feat_img_mime', $f_feat_img_mime);
      $query->bindParam(':feat_aud_mime', $f_feat_aud_mime);
      $query->bindParam(':feat_vid_mime', $f_feat_vid_mime);
      $query->bindParam(':feat_doc_mime', $f_feat_doc_mime);
      $query->bindParam(':date_live', $f_live_trim);
      $pdo->exec_($query);

      // Set dormant if there is a problem
      if (!$pdo->ok) {
        // Set status to problematic
        $query = $database->prepare("UPDATE aggregation SET status='problematic' WHERE id=:id");
        $query->bindParam(':id', $f_id);
        $pdo->exec_($query);
      } else {
        // Note the update
        $query = $database->prepare("UPDATE aggregation SET last_updated=NOW() WHERE id=:id");
        $query->bindParam(':id', $f_id);
        $pdo->exec_($query);
      }

    } // Each feed item

  } elseif ($f_status == 'deleting') { // Not active & time to update, deleting?

    $query = $database->prepare("SELECT id FROM publications WHERE aggregated=:aggregated");
    $query->bindParam(':aggregated', $f_id);
    $rows = $pdo->exec_($query);

    // Loop INSERT through each aggregated publication
    foreach ($rows as $row) {
      $p_id = $row->id;

      // Insert from publications to pieces
      $query = $database->prepare("INSERT INTO pieces (type, series, title, subtitle, slug, content, excerpt, tags, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated) SELECT type, series, title, subtitle, slug, content, excerpt, tags, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM publications WHERE id=:id");
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);
      $piece_id = $pdo->lastid;

      // Set publications piece_id
      $query = $database->prepare("UPDATE publications SET piece_id=:piece_id, aggregated=0 WHERE id=:id");
      $query->bindParam(':piece_id', $piece_id);
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);

    } // Each publication converted/erased

    // Deleting?
    $query = $database->prepare("DELETE FROM aggregation WHERE id=:id");
    $query->bindParam(':id', $f_id);
    $rows = $pdo->exec_($query);

  } // Active/Deleting/Time test

} // Each aggregation
