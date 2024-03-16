<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.logincheck.php');

// Check & validate for what we need
if ( ($_SERVER['REQUEST_METHOD'] === 'POST')
&& (!empty($_POST['u_id']))
&& (filter_var($_POST['u_id'], FILTER_VALIDATE_INT))
&& (isset($_SESSION['user_id']))
&& (($_POST['u_id']) == (isset($_SESSION['user_id'])))
&& (isset($_POST['f_id']))
&& (filter_var($_POST['f_id'], FILTER_VALIDATE_INT)) ) {

    // AJAX token check
    if ( $_POST['ajax_token'] !== $_SESSION['ajax_token'] ) {
      exit();
    }

  // Set our $u_id
  $u_id = preg_replace("/[^0-9]/"," ", $_POST['u_id']);

  // Assign the media ID and sanitize as the same time
  $f_id = preg_replace("/[^0-9]/"," ", $_POST['f_id']);

  // Create our AJAX response array
  $ajax_response = array();

    // Delete feed?
    if ((isset($_POST['feed-delete']))
    && ($_POST['feed-delete'] == $f_id)
    && (isset($_POST['agg_del_feed_posts']))) {
      $on_delete = ($_POST['agg_del_feed_posts'] == 'erase') ? 'erase' : 'convert';
      $query = $database->prepare("UPDATE aggregation SET status='deleting', on_delete=:on_delete WHERE id=:id");
      $query->bindParam(':on_delete', $on_delete);
      $query->bindParam(':id', $f_id);
      $pdo->exec_($query);
      if ($pdo->change) { // Successful change
        $ajax_response['name'] = $agg_name;
        $ajax_response['source'] = $agg_source;
        $ajax_response['change'] = 'delete';

        // Update now
        include ("./act.processfetch.php?f=$f_id");


      } else { // Impossible fail to delete feed
        $ajax_response['message'] = '<span class="red">Strange database trouble deleting feed "'.$agg_name.'"!</span>';
        $ajax_response['name'] = $series_name_trim;
        $ajax_response['slug'] = $clean_slug_trim;
        $ajax_response['change'] = 'nochange';
      }

      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;

    // Update Feed
  } elseif ((isset($_POST['agg_name']))
        && (isset($_POST['agg_source']))
        && (isset($_POST['agg_series']))
        && (isset($_POST['agg_description']))
        && (isset($_POST['agg_update_interval']))) {

      // Set our reponse ID
      $ajax_response['f_id'] = $f_id;

      // Feed name
      if (preg_replace('/\s+/', '', $_POST['agg_name']) != '') {
        $result = strip_tags($_POST['agg_name']); // Remove any HTML tags
        $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
        $result = str_replace('---','—',$result); // to em-dash
        $result = str_replace(' -- ',' – ',$result); // to en-dash
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

      // Feed update interval
      if (filter_var($_POST['agg_update_interval'], FILTER_VALIDATE_INT)) {
        $result = preg_replace("/[^0-9]/"," ", $_POST['agg_update_interval']);
        $agg_update_interval = ($result < 15) ? 15 : $result;

      } else {
        $ajax_response['message'] = '<span class="red notehide">Must enter a valid update interval! (in minutes)</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

      // Feed description
      if (isset($_POST['agg_description'])) {
        $result = strip_tags($_POST['agg_description']); // Remove any HTML tags
        $result = substr($result, 0, 255); // Limit to 255 characters
        $agg_description = $result;
        $agg_description = DB::trimspace($agg_description);

      } // Optional, no response needed

      // If updating a problematic feed, set active
      if ((isset($_POST['feed-status'])) && ($_POST['feed-status'] == $f_id)) {
        $agg_status = 'active';
      } else {
        // Fetch current status
        $rows = $pdo->select('aggregation', 'id', $f_id, 'status');
        // Shoule be 1 row
        if ($pdo->numrows == 1) {
          foreach ($rows as $row) {
            // Assign the values
            $old_status = "$row->status";
          }
        }
        $agg_status = ($old_status == 'problematic') ? 'active' : 'dormant';
      }

      // SQL
      $query = $database->prepare("UPDATE aggregation SET name=:name, source=:source, series=:series, description=:description, update_interval=:update_interval, status=:status WHERE id=:id");
      $query->bindParam(':name', $agg_name);
      $query->bindParam(':source', $agg_source);
      $query->bindParam(':series', $agg_series);
      $query->bindParam(':description', $agg_description);
      $query->bindParam(':update_interval', $agg_update_interval);
      $query->bindParam(':status', $agg_status);
      $query->bindParam(':id', $f_id);
      $pdo->exec_($query);
      if ($pdo->change) { // Successful change

        // Fetch slug for updated series
        $rowsc = $pdo->select('series', 'id', $agg_series, 'slug');
        // Shoule be 1 row
        if ($pdo->numrows == 1) {
          foreach ($rowsc as $row) {
            // Assign the values
            $agg_slug = "$row->slug";
          }
        }
        $ajax_response['message'] = '<span class="green notehide">Saved, refresh to see changes</span>';
        $ajax_response['name'] = $agg_name;
        $ajax_response['source'] = $agg_source;
        $ajax_response['slug'] = $agg_slug;
        $ajax_response['status'] = $agg_status;
        $ajax_response['change'] = 'change';
      } else { // No changes
        $ajax_response['message'] = '<span class="orange notehide">No changes.</span>';
        $ajax_response['name'] = $agg_name;
        $ajax_response['source'] = $agg_source;
        $ajax_response['status'] = $agg_status;
        $ajax_response['change'] = 'nochange';
      } // Changes check

      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;

    } // End UPDATE

} else { // End POST check
  exit ();
}

?>
