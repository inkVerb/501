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
$pro_rss_path = $pro_path.$pro_rss_name;
$pro_podcast_name = 'series-podcast.jpg';

// Check & validate for what we need
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_POST['u_id'])) && (filter_var($_POST['u_id'], FILTER_VALIDATE_INT)) && (isset($_SESSION['user_id'])) && (($_POST['u_id']) == (isset($_SESSION['user_id']))) ) {

  // Set our $u_id
  $u_id = preg_replace("/[^0-9]/"," ", $_POST['u_id']);

  // Create our AJAX response array
  $ajax_response = array();

    // Update Series
  if ( (isset($_POST['series_name'])) && (isset($_POST['series_slug'])) && (!empty($_POST['s_id'])) && (filter_var($_POST['s_id'], FILTER_VALIDATE_INT)) ) {

    // Assign the media ID and sanitize as the same time
    $s_id = preg_replace("/[^0-9]/"," ", $_POST['s_id']);

    // Set our reponse ID
    $ajax_response['s_id'] = $s_id;

    // Pro images
    // RSS feed
    $rss_info = ($_FILES["pro-rss"]["tmp_name"]) ? getimagesize($_FILES["pro-rss"]["tmp_name"]) : false;
    $pro_rss_path = $pro_path.$s_id.'-'.$pro_rss_name;
    if ($rss_info) {
      $tmp_file = $_FILES["pro-rss"]['tmp_name'];
      $image_width = $rss_info[0];
      $image_height = $rss_info[1];
      $upload_ext = strtolower(pathinfo(basename($_FILES["pro-rss"]["name"]),PATHINFO_EXTENSION));
      if ($_FILES['pro-rss']['size'] <= $file_size_limit) {
        if ($rss_info["mime"] == "image/jpeg") {
          if (($image_width == $image_height)
          &&  ($image_width == 144)
          &&  ($image_height == 144)) {

            if (move_uploaded_file($tmp_file, $pro_rss_path)) {
              $upload_img_success = true;
              $upload_rss_success = true;
            } else {
              $ajax_response['message'] = '<p class="red">path:'.$pro_rss_path.' RSS image upload unknown failure.</p>';
              $ajax_response['change'] = 'nochange';
              $ajax_response['upload'] = 'failed';
              $ajax_response['new_podcast'] = 'notnew';
              $ajax_response['new_rss'] = 'notnew';
              // We're done here
              $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
              echo $json_response;
              exit ();
            }

          } else {
            $ajax_response['message'] = '<p class="red">Logo is wrong size. Must be square and 144 pixels wide and high.</p>';
            $ajax_response['change'] = 'nochange';
            $ajax_response['upload'] = 'failed';
            $ajax_response['new_podcast'] = 'notnew';
            $ajax_response['new_rss'] = 'notnew';
            // We're done here
            $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
            echo $json_response;
            exit ();
          }

        } else {
          $ajax_response['message'] = '<p class="red">RSS image is wrong formatt. Allowed: JPEG, PNG, GIF</p>';
          // We're done here
          $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
          echo $json_response;
          exit ();
        }

      } else {
        $ajax_response['message'] = '<p class="red">RSS image file size is too big. Limit is 1MB.</p>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }
    } elseif ((isset($_POST['pro-delete'])) && ($_POST['pro-delete'] == 'delete')) {
      $delete_rss_image = true;
    }

    // iTunes Podcast
    $podcast_info = ($_FILES["pro-podcast"]["tmp_name"]) ? getimagesize($_FILES["pro-podcast"]["tmp_name"]) : false;
    $pro_podcast_path = $pro_path.$s_id.'-'.$pro_podcast_name;
    if ($podcast_info) {
      $tmp_file = $_FILES["pro-podcast"]['tmp_name'];
      $image_width = $podcast_info[0];
      $image_height = $podcast_info[1];
      $upload_ext = strtolower(pathinfo(basename($_FILES["pro-podcast"]["name"]),PATHINFO_EXTENSION));
      if ($_FILES['pro-podcast']['size'] <= $file_size_limit) {
        if ($podcast_info["mime"] == "image/jpeg") {
          if (($image_width == $image_height)
          &&  ($image_width == 3000)
          &&  ($image_height == 3000)) {

            if (move_uploaded_file($tmp_file, $pro_podcast_path)) {
              $upload_img_success = true;
              $upload_podcast_success = true;
            } else {
              $ajax_response['message'] =  '<p class="red">path:'.$pro_podcast_path.' Podcast image upload unknown failure.</p>';
              $ajax_response['change'] = 'nochange';
              $ajax_response['upload'] = 'failed';
              $ajax_response['new_podcast'] = 'notnew';
              $ajax_response['new_rss'] = 'notnew';
              // We're done here
              $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
              echo $json_response;
              exit ();
            }

          } else {
            $ajax_response['message'] =  '<p class="red">Podcast image is wrong size. Must be square and 3000 pixels wide and high.</p>';
            $ajax_response['change'] = 'nochange';
            $ajax_response['upload'] = 'failed';
            $ajax_response['new_podcast'] = 'notnew';
            $ajax_response['new_rss'] = 'notnew';
            // We're done here
            $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
            echo $json_response;
            exit ();
          }

        } else {
          $ajax_response['message'] =  '<p class="red">Podcast image is wrong formatt. Allowed: JPEG, PNG, GIF</p>';
          $ajax_response['change'] = 'nochange';
          $ajax_response['upload'] = 'failed';
          $ajax_response['new_podcast'] = 'notnew';
          $ajax_response['new_rss'] = 'notnew';
          // We're done here
          $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
          echo $json_response;
          exit ();
        }

      } else {
        $ajax_response['message'] =  '<p class="red">Podcast image file size is too big. Limit is 1MB.</p>';
        $ajax_response['change'] = 'nochange';
        $ajax_response['upload'] = 'failed';
        $ajax_response['new_podcast'] = 'notnew';
        $ajax_response['new_rss'] = 'notnew';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }
    } elseif ((isset($_POST['pro-delete'])) && ($_POST['pro-delete'] == 'delete')) {
      $delete_podcast_image = true;
    }
    // End pro image uploads

    // No uploads
    // Series slug
    if (preg_replace('/\s+/', '', $_POST['series_slug']) != '') {
      $regex_replace = "/[^a-zA-Z0-9-]/";
      $result = filter_var($_POST['series_slug'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = strtolower(preg_replace($regex_replace,"-", $result)); // Lowercase, all non-alnum to hyphen
      $result = substr($result, 0, 90); // Limit to 90 characters
      $clean_slug = $result;
      $clean_slug_trim = DB::trimspace($clean_slug);
      $query = $database->prepare("SELECT id FROM series WHERE slug=:slug AND NOT id=:id");
      $query->bindParam(':slug', $clean_slug_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Slug already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }
    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series slug!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series name
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }

    // SQL
    $query = $database->prepare("UPDATE series SET name=:name, slug=:slug WHERE id=:id");
    $query->bindParam(':name', $series_name_trim);
    $query->bindParam(':slug', $clean_slug_trim);
    $query->bindParam(':id', $s_id);
    $pdo->exec_($query);
    if ($pdo->change) { // Successful change
      $ajax_response['message'] = '<span class="green notehide">Saved, refresh to see changes in Series lists</span>';
      $ajax_response['name'] = $series_name_trim;
      $ajax_response['slug'] = $clean_slug_trim;
      $ajax_response['change'] = 'change';
    } else { // No changes
      // Images?
      if ($upload_img_success == true) {
        $ajax_response['message'] = '<span class="green notehide">Image uploaded. Changes may not take effect until cache reloads.</span>';
        $ajax_response['name'] = $series_name_trim;
        $ajax_response['slug'] = $clean_slug_trim;
        $ajax_response['change'] = 'change';
        $ajax_response['upload'] = 'uploaded';
        $ajax_response['new_podcast'] = ($upload_podcast_success) ? 'newpodcast' : 'notnew';
        $ajax_response['new_rss'] = ($upload_rss_success) ? 'newrss' : 'notnew';
      // Deleting something?
      } else {
        // Delete the images?
        if (($delete_rss_image == true) && ($delete_podcast_image == true)) {
           unlink($pro_rss_path);
           unlink($pro_podcast_path);
           $ajax_response['message'] = '<span class="red notehide">Images deleted.</span>';
           $ajax_response['name'] = $series_name_trim;
           $ajax_response['slug'] = $clean_slug_trim;
           $ajax_response['change'] = 'nochange';
           $ajax_response['upload'] = 'delete';
           $ajax_response['new_podcast'] = 'notnew';
           $ajax_response['new_rss'] = 'notnew';
        // Delete series?
        } elseif ((isset($_POST['series-delete'])) && ($_POST['series-delete'] == 'delete')) {
          $queryp = $database->prepare("UPDATE pieces SET series=:newseries WHERE series=:oldseries");
          $queryp->bindParam(':newseries', $blog_default_series);
          $queryp->bindParam(':oldseries', $s_id);
          $pdo->exec_($queryp);
          $delokp = ($pdo->ok) ? true : false;
          $queryb = $database->prepare("UPDATE publications SET series=:newseries WHERE series=:oldseries");
          $queryb->bindParam(':newseries', $blog_default_series);
          $queryb->bindParam(':oldseries', $s_id);
          $pdo->exec_($queryb);
          $delokb = ($pdo->ok) ? true : false;
          $queryh = $database->prepare("UPDATE publication_history SET series=:newseries WHERE series=:oldseries");
          $queryh->bindParam(':newseries', $blog_default_series);
          $queryh->bindParam(':oldseries', $s_id);
          $pdo->exec_($queryh);
          $delokh = ($pdo->ok) ? true : false;
          if ($delokp && $delokb && $delokh) {
            $pdo->delete('series', 'id', $s_id);
            if ($pdo->ok) { // Series deleted
              $ajax_response['message'] = '<span class="red">Series deleted!</span>';
              $ajax_response['name'] = $series_name_trim;
              $ajax_response['slug'] = $clean_slug_trim;
              $ajax_response['change'] = 'delete';
              $ajax_response['upload'] = 'none';
              $ajax_response['new_podcast'] = 'notnew';
              $ajax_response['new_rss'] = 'notnew';
            } else { // Impossible fail to delete series
              $ajax_response['message'] = '<span class="error">Strange database trouble deleting series!</span>';
              $ajax_response['name'] = $series_name_trim;
              $ajax_response['slug'] = $clean_slug_trim;
              $ajax_response['change'] = 'nochange';
              $ajax_response['upload'] = 'none';
              $ajax_response['new_podcast'] = 'notnew';
              $ajax_response['new_rss'] = 'notnew';
            }
          } else { // Impossible fail to change affected pices to default series
            $ajax_response['message'] = '<span class="error">Strange database trouble preparing to delete series!</span>';
            $ajax_response['name'] = $series_name_trim;
            $ajax_response['slug'] = $clean_slug_trim;
            $ajax_response['change'] = 'nochange';
            $ajax_response['upload'] = 'none';
            $ajax_response['new_podcast'] = 'notnew';
            $ajax_response['new_rss'] = 'notnew';
          }
        } else { // Truly no changes at all
          $ajax_response['message'] = '<span class="orange notehide">No changes.</span>';
          $ajax_response['name'] = $series_name_trim;
          $ajax_response['slug'] = $clean_slug_trim;
          $ajax_response['change'] = 'nochange';
          $ajax_response['upload'] = 'none';
          $ajax_response['new_podcast'] = 'notnew';
          $ajax_response['new_rss'] = 'notnew';
        } // No changes
      } // Delete check
    } // Changes check

    // We're done here
    $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
    echo $json_response;

  // Just getting the <form> from seriesEditor AJAX loader
  } else {
    echo '<div id="series-editor-container">';
    echo '<h2 class="editor-title">Series Editor</h2>';

    // Pagination
    // Valid the Pagination
    if ((isset($_POST['r'])) && (filter_var($_POST['r'], FILTER_VALIDATE_INT, array('min_range' => 1)))) {
     $paged = preg_replace("/[^0-9]/","", $_POST['r']);
    } else {
     $paged = 1;
    }
    // Set pagination variables:
    $pageitems = 100;
    $itemskip = $pageitems * ($paged - 1);
    // We add this to the end of the $query, after DESC
    // LIMIT $itemskip,$pageitems

    // Pagination navigation: How many items total?
    $query = $database->prepare("SELECT id FROM series ORDER BY name");
    $rows = $pdo->exec_($query);
    $totalrows = $pdo->numrows;

    $totalpages = floor($totalrows / $pageitems);
    $remainder = $totalrows % $pageitems;
    if ($remainder > 0) {
    $totalpages = $totalpages + 1;
    }
    if ($paged > $totalpages) {
    $totalpages = 1;
    }
    $nextpaged = $paged + 1;
    $prevpaged = $paged - 1;

    // Pagination nav row
    if ($totalpages > 1) {
    	echo "
    	<div class=\"paginate_nav_container\">
    		<div class=\"paginate_nav\">
    			<table>
    				<tr>
    					<td>
    						<a class=\"paginate";
    						if ($paged == 1) {echo " disabled";}
    						echo "\" title=\"Page 1\" href=\"#\" onclick=\"seriesEditor($user_id, 1);\">&laquo;</a>
    					</td>
    					<td>
    						<a class=\"paginate";
                if ($paged == 1) {echo " disabled";}
               echo "\" title=\"Previous\" href=\"#\" onclick=\"seriesEditor($user_id, $prevpaged);\">&lsaquo;&nbsp;</a>
    					</td>
    					<td>
    						<a class=\"paginate current\" title=\"Next\" href=\"#\" onclick=\"seriesEditor($user_id, $paged);\">Page $paged ($totalpages)</a>
    					</td>
    					<td>
    						<a class=\"paginate";
                if ($paged == $totalpages) {echo " disabled";}
               echo "\" title=\"Next\" href=\"#\" onclick=\"seriesEditor($user_id, $nextpaged);\">&nbsp;&rsaquo;</a>
    					</td>
    					 <td>
    						 <a class=\"paginate";
    						 if ($paged == $totalpages) {echo " disabled";}
    	 					echo "\" title=\"Last Page\" href=\"#\" onclick=\"seriesEditor($user_id, $totalpages);\">&raquo;</a>
    					 </td>
    		 		</tr>
    			</table>
    		</div>
    	</div>";
    }

    // Iterate the rows
    $query = $database->prepare("SELECT * FROM series ORDER BY name LIMIT $itemskip,$pageitems");
    $rows = $pdo->exec_($query);
    if ($pdo->numrows > 0) {

      // Start our HTML table
      $table_row_color = 'blues';
      echo '
      <table class="contentlib" id="series-table">
        <tbody>
          <tr>
          <th width="15%">Name</th>
          <th width="15%">Slug</th>
          <th width="14%"></th>
          <th width="23%"></th>
          <th width="23%"></th>
          </tr>
      ';

      foreach ($rows as $row) {
        $series_id = $row->id;
        $series_name = $row->name;
        $series_slug = $row->slug;


        // View items row (default shown)
        // File paths
        $pro_rss_path = $pro_path.$series_id.'-'.$pro_rss_name;
        $pro_podcast_path = $pro_path.$series_id.'-'.$pro_podcast_name;
        // Contents
        echo '<tr class="pieces '."$table_row_color".'" id="v_row_'.$series_id.'" onmouseover="showChangeButton('.$series_id.');" onmouseout="showChangeButton('.$series_id.');">';
        echo '<td id="sne-'.$series_id.'">
        <form id="series-edit-'.$series_id.'" enctype="multipart/form-data">
        <input type="hidden" name="u_id" value="'.$user_id.'">
        <input type="hidden" name="s_id" value="'.$series_id.'">
        </form>
        <input type="text" form="series-edit-'.$series_id.'" id="input-name-'.$series_id.'" name="series_name" value="'.$series_name.'">
        <div id="delete-checkbox-'.$series_id.'" style="display:none;">
        <br><br>
        <button id="edit-details-'.$series_id.'"type="button" class="postform link-button inline blue" onclick="detailsEditor('.$user_id.', '.$series_id.');">Edit podcast details</button>';
        if ($series_id != $blog_default_series) {
          echo '<br><br><label for="series-delete-'.$series_id.'"><input type="checkbox" form="series-edit-'.$series_id.'" id="series-delete-'.$series_id.'" name="series-delete" value="delete"> <i><small>Permanently delete series</small></i></label>
          </div>';
        } else {
          echo '</div>
          <br><br><i><small>Default</small></i>';
        }

        echo '</td>';
        echo '<td id="sse-'.$series_id.'">
        <input type="text" form="series-edit-'.$series_id.'" id="input-slug-'.$series_id.'" name="series_slug" value="'.$series_slug.'"></td>';
        echo '<td id="scv-'.$series_id.'">
              <table>
                <tr>
                  <td id="mcv-'.$series_id.'">
                    <div id="make-edits-'.$series_id.'" style="display:none;">
                      <button id="change-cancel-'.$series_id.'"type="button" class="postform link-button inline blue" onclick="showHideEdit('.$series_id.');">Change</button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div id="e_buttons_'.$series_id.'" style="display:none;">
                      <button type="button" onclick="seriesSave('.$series_id.');">Save</button>&nbsp;
                      <button type="button" onclick="showHideEdit('.$series_id.');">Cancel</button>';
                      if ((file_exists($pro_rss_path)) || (file_exists($pro_podcast_path))) {
                        echo '<br><br><label for="pro-delete-'.$series_id.'"><input type="checkbox" form="series-edit-'.$series_id.'" id="pro-delete-'.$series_id.'" name="pro-delete" value="delete"> <i>Delete images</i></label>';
                      }
                      echo '
                    </div>
                    <div id="edit-message-'.$series_id.'"></div>
                  </td>
                </tr>
              </table>
              </td>';
        echo '<td id="sav-'.$series_id.'">';
          // RSS feed
          echo '<p class="settings-pro-image" id="pro-rss-upload-'.$series_id.'"><b>RSS</b> <small>(JPEG 144x144)</small> <input type="file" name="pro-rss" id="pro-rss" form="series-edit-'.$series_id.'"><br><br>';
          if (file_exists($pro_rss_path)) {
            echo '<img id="rss-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" src="'.$pro_rss_path.'">';
            echo '<code id="rss-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" class="gray"><i>no image</i></code>';
          } else {
            echo '<img id="rss-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" src="'.$pro_rss_path.'">';
            echo '<code id="rss-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" class="gray"><i>no image</i></code>';

          }
        echo '</p>';
        echo '<div id="message-'.$series_id.'" style="display:none;"></div>
        </td>';
        echo '<td id="smv-'.$series_id.'">';
          // iTunes Podcast
          echo '<p class="settings-pro-image" id="pro-podcast-upload-'.$series_id.'"><b>Podcast</b> <small>(JPEG 3000x3000)</small> <input type="file" name="pro-podcast" id="pro-podcast" form="series-edit-'.$series_id.'"><br><br>';
          if (file_exists($pro_podcast_path)) {
            echo '<img id="podcast-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" src="'.$pro_podcast_path.'">';
            echo '<code id="podcast-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" class="gray"><i>no image</i></code>';
          } else {
            echo '<img id="podcast-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" src="'.$pro_podcast_path.'">';
            echo '<code id="podcast-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" class="gray"><i>no image</i></code>';
          }
        echo '</td>';
        echo '</tr>';

        // Toggle our row colors
        $table_row_color = ($table_row_color == 'blues') ? 'shady' : 'blues';

      }
      echo '</tbody></table>';

      echo '</div>';

      echo '<br><br>';

      // Pagination nav row
      if ($totalpages > 1) {
      	echo "
      	<div class=\"paginate_nav_container\">
      		<div class=\"paginate_nav\">
      			<table>
      				<tr>
      					<td>
      						<a class=\"paginate";
      						if ($paged == 1) {echo " disabled";}
      						echo "\" title=\"Page 1\" href=\"#\" onclick=\"seriesEditor($user_id, 1);\">&laquo;</a>
      					</td>
      					<td>
      						<a class=\"paginate";
                  if ($paged == 1) {echo " disabled";}
                 echo "\" title=\"Previous\" href=\"#\" onclick=\"seriesEditor($user_id, $prevpaged);\">&lsaquo;&nbsp;</a>
      					</td>
      					<td>
      						<a class=\"paginate current\" title=\"Next\" href=\"#\" onclick=\"seriesEditor($user_id, $paged);\">Page $paged ($totalpages)</a>
      					</td>
      					<td>
      						<a class=\"paginate";
                  if ($paged == $totalpages) {echo " disabled";}
                 echo "\" title=\"Next\" href=\"#\" onclick=\"seriesEditor($user_id, $nextpaged);\">&nbsp;&rsaquo;</a>
      					</td>
      					 <td>
      						 <a class=\"paginate";
      						 if ($paged == $totalpages) {echo " disabled";}
      	 					echo "\" title=\"Last Page\" href=\"#\" onclick=\"seriesEditor($user_id, $totalpages);\">&raquo;</a>
      					 </td>
      		 		</tr>
      			</table>
      		</div>
      	</div>";
      }

    } else { // If no entries in the series table
      echo "<p>That's strange. Nothing here.</p>";
    }

  } // seriesEditor AJAX loader

} else { // End POST check
  exit ();
}

?>
