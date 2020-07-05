<?php

// This parses a URL, Title, and Credit
// They must all be on the same line and separated by double semicolon ;;
/* Both of these will work:
https://inkisaverb.com;; Title Here;; Credit Here
<a href="http://inkisaverb.com">Title Here // Credit Here</a>
*/

////
// Keep our form populated in this demo
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['p_links'])) ) {
  $posted_value = $_POST['p_links'];
}
////

// Create our form (a little bigger with: cols="80" rows="8")
echo '
<form action="jsonlinksexplained.php" method="post">
  <textarea id="p_links" name="p_links" cols="80" rows="8">'.$posted_value.'</textarea>
  <br><br>
  <input type="submit" value="Parse me">
</form>';

// Add a note
// Prep this so we see the HTML, not just what it renders
$string1 = htmlspecialchars('<a href="https://inkisaverb.com">Ink is a verb.</a>');
$string2 = htmlspecialchars('<a href="https://verb.vip">Get inking. // VIP Linux</a>');
$string3 = htmlspecialchars('<a href="http://poetryiscode.com">Poetry is code. | piC</a>');
echo
"
<code><i>Separate [url] [title] [credit] via ;;<br>
1. In any order on a line ([title] before [credit])<br>
2. Spaces don't matter<br>
3. Only [url] is required<br>
4. If no [credit], Credit can be pulled after a | Pipe from [title]<br>
5. All else after | Pipe gets truncated</i><br><br>
<b>For example:</b><br><br>
https://verb.one<br>
https://verb.red ;;Get inking.<br>
https://verb.ink;; Ink is a verb.;;inkVerb<br>
https://verb.blue;; Inky | Blue Ink<br>
$string1<br>
$string2<br>
$string3<br>
</code>
<hr>
";

// Parse a POST
// Basic RegEx for no brackets and coding non-link safety checks
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!preg_match('/([\]\[\\{\}\$\*]+)/i', $_POST['p_links'])) ) {


  ////
  // echo demo
  echo '<pre><h1>Results</h1></pre>';
  echo '<pre><h2>1. We get this info from our filters:</h2></pre>';
  echo '<code>
  Logic:<br>
  Is it HTML?<br>
  -  true: Use RegEx to pull what we need<br>
  -  false: Use PHP logic to find what we need<br>
  It is not HTML<br>
  - a. Third item? Start process as if three items<br>
  - b. Second item? Continue process as if more than one item<br>
  - c. First item: Finish process based on what happened so far<br>
  </code><br>';
  ////


  // Prepare our values
  $links_array = array(); // Set it before the foreach loop where we use it
  $p_links = $_POST['p_links'];
  $arr = explode("\n", $p_links);
  $regex_replace = "/[^a-zA-Z0-9-!@&#$%.'\":|]/"; // Pipe is here because we will cut from it later

  // Start our array key
  $i = 0;

  // Iterate through that array
  foreach ($arr as $line) {

    // Is HTML link?
    if (($line != strip_tags($line))
    &&  (strpos($line, 'href='))) { // true = HTML

      // Parse our a link
      // Does it have a valid "href=" attribute?
      preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $line, $href_res);
      if (!empty($href_res)) { // "href=" found
        // URL
        $url = $href_res['href'][0]; // This came from preg_match_all() just above
        $parsed_url = parse_url($url); // This pulls different things from a URL
        $host = $parsed_url['host']; // This gets just the host
        // Title
        $title = substr($line, strpos($line, '>') + 1); // Cut everything before
        $title = substr($title, 0, strpos($title, '<')); // Cut everything after
        // Credit
        if (strpos($title, '//')) {
          $credit = substr($title, strrpos($title, '/') + 1); // Cut everything before
          $title = substr($title, 0, strpos($title, '//')); // Cut everything after
        } else {
          $credit = $host;
          $credit_auto = true;
        }

        // Validate & set $url
        if ( (filter_var($url,FILTER_VALIDATE_URL)) && (strlen($url) <= 148) && (!empty($title))) {
          $url = $url;

        // Next item if we  no valid URL or had an empty $title
        } else {
          // No ghosts from the past
          unset($url);
          unset($title);
          unset($credit);
          if (isset($credit_auto)) {unset($credit_auto);}
          continue;
        }

      // Next item if we found no URL in href=
      } else {
        continue;
      }


    // Not HTML
    } else {

      // Make this yet another array
      $part = explode(";;", $line); // Split by unquoted whitespace

      // Assign the first three items
      $part3 = trim($part[2]);
      $part2 = trim($part[1]);
      $part1 = trim($part[0]);

      $part3 = trim($part[2]);
      $part2 = trim($part[1]);
      $part1 = trim($part[0]);

      // Process 3 parts in reverse order
      // Running last things first, assign values based on the remaining possible outcomes

      // Part 3
      if ( (isset($part3)) && ($part3 != '') ) {
        if ( (filter_var($part3,FILTER_VALIDATE_URL)) && (strlen($part3) <= 128) ) {
          $url = $part3;
          $parsed_url = parse_url($url); // This pulls different things from a URL
          $host = $parsed_url['host']; // This gets just the host
        } else { // Third part is not a URL, it can only be a Credit
          $credit = preg_replace($regex_replace," ", $part3);
        }
      } else {
        $no_url_part3 = true;
      }

      // Part 2
      if ( (isset($part2)) && ($part2 != '') ) {
        if ( (filter_var($part2,FILTER_VALIDATE_URL)) && (strlen($part2) <= 148) ) {
          $url = $part2;
          $parsed_url = parse_url($url); // This pulls different things from a URL
          $host = $parsed_url['host']; // This gets just the host
        } elseif ( (isset($url)) && (!isset($credit)) ) { // If we didn't get a $credit from a cut
          $credit = preg_replace($regex_replace," ", $part2);
        } elseif (!isset($title)) { // Something good has happened, we'll do what's left
          $title = preg_replace($regex_replace," ", $part2);
        } else {
          $no_url_part2 = true;
        }
      } else {
        $no_url_part2 = true;
      }

      // Part 1
      if ( (isset($part1)) && ($part1 != '') ) {
        if ( (filter_var($part1,FILTER_VALIDATE_URL)) && (strlen($part1) <= 148) ) {
          $url = $part1;
          $parsed_url = parse_url($url); // This pulls different things from a URL
          $host = $parsed_url['host']; // This gets just the host

          if ( (!isset($title)) && (!isset($credit)) ) { // If we don't have a Title by now, get it from the URL
            $title = $host;
            $credit = 'link';
            $title_auto = true;
            $credit_auto = true;
          }
          if ( (!isset($credit)) && (isset($url)) ) {// If we don't have a Credit by now, put a placeholder
            $credit = $host;
            $credit_auto = true;
          } elseif (!isset($credit)) {
            $credit = 'link';
            $credit_auto = true;
          }

        } elseif ( (isset($url)) && (!isset($title)) ) { // No Title? set it now
            $title = preg_replace($regex_replace," ", $part1);
          if (!isset($credit)) { // If we didn't get a $credit from a cut
            $credit = $host; // We already have a URL, get its host for our Credit
            $credit_auto = true;
            }
        } else {
          $no_url_part1 = true;
        }
      } else {
        $no_url_part1 = true;
      }

      // Next item if we processed no URL (no empty entries)
      if ( (isset($no_url_part1)) && (isset($no_url_part2)) && (isset($no_url_part3)) ) {
        // No ghosts from the past
        unset($url);
        unset($title);
        unset($credit);
        if (isset($title_auto)) {unset($title_auto);}
        if (isset($credit_auto)) {unset($credit_auto);}

        // Don't trigger this again
        unset($no_url_part1);
        unset($no_url_part2);
        unset($no_url_part3);

        // Next item
        continue;
      }

    } // All values should be set now, one way or another

  // Check for pipes in the Title

    // Cut the $credit from $title if auto-credit, but not auto-title
    if ( (!isset($title_auto)) && (isset($credit_auto)) ) {
      if (strpos($title, '|') !== false) {
        $credit = substr($title, strrpos($title, '|') + 1);
      }
    }

    // Tuncate after any pipes in the $title or $credit
    if (strpos($title, '|') !== false) {
      $title = substr($title, 0, strpos($title, '|'));
    }
    if (strpos($credit, '|') !== false) {
      $credit = substr($credit, 0, strpos($credit, '|'));
    }

    // Sanitize
    $credit = substr($credit, 0, 54);
    $title = substr($title, 0, 91);


  // Set our final values and increment to our next final array entry

    // trim() whitespace
    $url = trim($url);
    $title = trim($title);
    $credit = trim($credit);

    // Iterate into array
    $links_array[$i][0] = $url;
    $links_array[$i][1] = $title;
    $links_array[$i][2] = $credit;


    ////
    // echo demo
    $url_ = $links_array[$i][0];
    $title_ = $links_array[$i][1];
    $credit_ = $links_array[$i][2];
    echo "<pre><i>$url_</i> <b>$title_ // $credit_</b></pre>";
    ////


    // Next item
    $i++;

    // No ghosts from the past
    unset($url);
    unset($title);
    unset($credit);
    if (isset($title_auto)) {unset($title_auto);}
    if (isset($credit_auto)) {unset($credit_auto);}

  }


  ////
  // echo demo
  echo '<pre><h2>2. We have all our info in the PHP array $links_array[]:</h2></pre>';
  echo '<pre>Inside our giant loop: (our key starts as $i=0, then increases with each loop)</pre>';
  echo '<code style="background-color:#ddd;">
  $links_array[$i][0] = $url;<br>
  $links_array[$i][1] = $title;<br>
  $links_array[$i][2] = $credit;<br>
  $i++; // Next item<br>
  </code><br>';
  foreach ($links_array as $line_item) {
    echo "<pre style=\"color:#225893;\"><b>$line_item</b></pre>";
    foreach ($line_item as $key => $avalue) {
      echo "<pre style=\"color:#227883;\">[$key] = $avalue</pre>";
    }
  }
  ////


  // Send $links_array to JSON, as we would before sending to SQL
  $links_json = json_encode($links_array);


  ////
  // echo demo
  echo '<pre><h2>3. We put all our info in JSON as $links_json:</h2></pre>';

  // Show code
  echo '<pre><code style="background-color:#ddd;">$links_json = json_encode($links_array);</code></pre>';

  // Arrays as-JSON, pretty
  $links_json = json_encode($links_array, JSON_PRETTY_PRINT);
  echo "<pre style=\"color:#a85022;\"><b>\$json = json_encode(\$array, JSON_PRETTY_PRINT):</b> <i>(array as-JSON objects)</i></pre>";
  echo "<pre style=\"color:#a83012;\">$links_json</pre>";

  // Arrays inside-JSON, pretty
  $links_json_inobjects = json_encode($links_array, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT);
  echo "<pre style=\"color:#a85022;\"><b>\$json_inobjects = json_encode(\$array, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT):</b> <i>(arrays inside-JSON objects)</i></pre>";
  echo "<pre style=\"color:#a83012;\">$links_json_inobjects</pre>";

  // ...This will go into our database
  ////


  // Get our array back to a PHP array from JSON, as we would after retrieving from SQL
  $links_array = json_decode($links_json);
  //$links_array =json_decode($links_json_inobjects, true); // Or this if from JSON_FORCE_OBJECT -made JSON


  ////
  // echo demo
  echo "<pre><h3>Without 'JSON_PRETTY_PRINT', everything is on one line: <i>(great for SQL)</i></h3></pre>";

  // as-JSON
  $links_json = json_encode($links_array);
  echo "<pre style=\"color:#a85022;\"><b>\$json = json_encode(\$array):</b> <i>(array as-JSON objects)</i></pre>";
  echo "<pre style=\"color:#a83012;\">$links_json</pre>";

  // inside-JSON
  $links_json_inobjects = json_encode($links_array, JSON_FORCE_OBJECT);
  echo "<pre style=\"color:#a85022;\"><b>\$json_inobjects = json_encode(\$array, JSON_FORCE_OBJECT):</b> <i>(arrays inside-JSON objects)</i></pre>";
  echo "<pre style=\"color:#a83012;\">$links_json_inobjects</pre>";

  echo "<pre>An array as-JSON objects (without 'JSON_FORCE_OBJECT') is smaller, so we will use it for SQL because both work</pre>";

  echo '<pre><h2>4. Send our JSON to SQL, get it back from the database when we need it (not shown here)</h2></pre>';

  echo '<pre><h2>5. Convert JSON back into the same PHP array:</h2></pre>';

  // Show code
  echo '<pre><code style="background-color:#ddd;">$links_array =json_decode($links_json);</code> // From as-JSON To 3-D PHP array</pre>';
  echo '<pre><code style="background-color:#ddd;">$links_array =json_decode($links_json_inobjects, true);</code> // From inside-JSON To 3-D PHP array</pre>';

  foreach ($links_array as $line_item) {
    echo "<pre style=\"color:#225893;\"><b>$line_item</b></pre>";
    foreach ($line_item as $key => $avalue) {
      echo "<pre style=\"color:#227883;\">[$key] = $avalue</pre>";
    }
  }
  ////


  // Only if we actually have links
  if (!empty($links_array)) {
    $p_links_json_in = ''; // Start the $p_links_json_in set
    // Parse $links_array into <a> tag variables
    foreach ($links_array as $line_item) {
      $links = '<span class="link_item">';
      $link_item = '<a href="'.$line_item[0].'" title="'.$line_item[2].'" target="_blank">'.$line_item[1].' <i>// '.$line_item[2].'</i></a>';
      $links .= $link_item.'<br><br>';
      $links .= '</span>';
    }
  }


  ////
  // echo demo
  $processed_link = htmlspecialchars('<a href="\'.$line_item[0].\'" title="\'.$line_item[2].\' target="_blank">\'.$line_item[1].\' <i>// \'.$line_item[2].\'</i></a>');
  $br_tag = htmlspecialchars('<br>');
  $span_end = htmlspecialchars('</span>');
  echo '<pre><h2>6. Iterate our PHP array into HTML links</h2></pre>';
  echo '<code style="background-color:#ddd;">
  $p_links_json_in = \'\'; // Start the $p_links_json_in set
  foreach ($links_array as $line_item) {<br>
  &nbsp;&nbsp;$link_item = \''.$processed_link.'\';<br>
  &nbsp;&nbsp;$links .= $link_item.\''.$br_tag.$br_tag.'\';<br>
  }<br>
  </code><br>';

  $links_code = htmlspecialchars($links);
  echo "<pre><b>Our links...</b></pre>
  <pre><b>...rendered:</b></pre>
  <p>$links</p>
  <pre><b>...as code:</b></pre>
  <p><code>$links_code</cpde></p>";
  // ...This came from our database to our HTML page
  ////


}

?>
