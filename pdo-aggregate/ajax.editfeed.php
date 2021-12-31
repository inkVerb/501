<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.logincheck.php');

// Pro images filenames
$upload_subdir = 'media/pro/';
$file_size_limit = 1000000; // 1MB
$podcast_size_limit = 10000000; // 10MB
$pro_path = $upload_subdir;
$pro_rss_name = 'series-rss.jpg';
$pro_podcast_name = 'series-podcast.jpg';

// Check & validate for what we need
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_POST['u_id'])) && (filter_var($_POST['u_id'], FILTER_VALIDATE_INT)) && (isset($_SESSION['user_id'])) && (($_POST['u_id']) == (isset($_SESSION['user_id']))) ) {

  // Set our $u_id
  $u_id = preg_replace("/[^0-9]/"," ", $_POST['u_id']);

  // Create our AJAX response array
  $ajax_response = array();

    // Delete feed?
    if ((isset($_POST['series-delete'])) && ($_POST['series-delete'] == 'delete')) {
      $query = $database->prepare("DELETE FROM aggregation WHERE id=:id");
      $query->bindParam(':id', $f_id);
      $pdo->exec_($query);
      if ($pdo->change) { // Successful change
        $ajax_response['message'] = '<span class="green notehide">Feed "'.$agg_name.'" deleted, refresh to see changes</span>';
        $ajax_response['name'] = $agg_name;
        $ajax_response['source'] = $agg_source;
        $ajax_response['change'] = 'delete';
      } else { // Impossible fail to delete feed
        $ajax_response['message'] = '<span class="red">Strange database trouble deleting feed "'.$agg_name.'"!</span>';
        $ajax_response['name'] = $series_name_trim;
        $ajax_response['slug'] = $clean_slug_trim;
        $ajax_response['change'] = 'nochange';
      }

    // Update Feed
    } elseif ( (isset($_POST['feed_name'])) && (isset($_POST['feed_url'])) && (!empty($_POST['f_id'])) && (filter_var($_POST['f_id'], FILTER_VALIDATE_INT)) ) {

      // Assign the media ID and sanitize as the same time
      $f_id = preg_replace("/[^0-9]/"," ", $_POST['f_id']);

      // Set our reponse ID
      $ajax_response['f_id'] = $f_id;

      // Feed name
      if (preg_replace('/\s+/', '', $_POST['agg_name']) != '') {
        $result = filter_var($_POST['agg_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
        $result = str_replace(' -- ',' – ',$result); // to en-dash
        $result = str_replace('---','—',$result); // to em-dash
        $result = str_replace('--','—',$result); // to em-dash
        $result = substr($result, 0, 90); // Limit to 90 characters
        $agg_name = $result;
        $agg_name = DB::trimspace($agg_name);

        $query = $database->prepare("SELECT id FROM aggregation WHERE name=:name AND NOT id=:id");
        $query->bindParam(':name', $agg_name);
        $query->bindParam(':id', $f_id);
        $rows = $pdo->exec_($query);
        if ($pdo->numrows > 0) {
          $ajax_response['message'] = '<span class="red notehide">Feed nickname already in use!</span>';
          // We're done here
          $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
          echo $json_response;
          exit ();
        }

      } else {
        $ajax_response['message'] = '<span class="red notehide">Must enter a feed nickname!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

      // Feed URL
      if ((filter_var($_POST['agg_source'],FILTER_VALIDATE_URL)) && (strlen($value) <= 128)) {
        $result = substr(preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $_POST['agg_source']),0,128);
        $agg_source = $result;
        $agg_source = DB::trimspace($agg_source);

        $query = $database->prepare("SELECT id FROM aggregation WHERE source=:source AND NOT id=:id");
        $query->bindParam(':source', $agg_source);
        $query->bindParam(':id', $f_id);
        $rows = $pdo->exec_($query);
        if ($pdo->numrows > 0) {
          $ajax_response['message'] = '<span class="red notehide">Feed URL already in use!</span>';
          // We're done here
          $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
          echo $json_response;
          exit ();
        }

      } else {
        $ajax_response['message'] = '<span class="red notehide">Must enter a feed URL!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

      // Feed Series
      if (filter_var($_POST['agg_series'], FILTER_VALIDATE_INT)) {
        $result = preg_replace("/[^0-9]/"," ", $_POST['agg_series']);
        $agg_series = $result;

      } else {
        $ajax_response['message'] = '<span class="red notehide">Must enter a feed URL!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

      // SQL
      $query = $database->prepare("UPDATE aggregation SET name=:name, source=:source WHERE id=:id");
      $query->bindParam(':name', $agg_name);
      $query->bindParam(':source', $agg_source);
      $query->bindParam(':id', $f_id);
      $pdo->exec_($query);
      if ($pdo->change) { // Successful change
        $ajax_response['message'] = '<span class="green notehide">Saved, refresh to see changes</span>';
        $ajax_response['name'] = $agg_name;
        $ajax_response['source'] = $agg_source;
        $ajax_response['change'] = 'change';
      } else { // No changes
        $ajax_response['message'] = '<span class="orange notehide">No changes.</span>';
        $ajax_response['name'] = $agg_name;
        $ajax_response['source'] = $agg_source;
        $ajax_response['change'] = 'nochange';
      } // Changes check

      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;

    } // End UPDATE

  } // End POST check

} else { // End POST check
  exit ();
}

?>
