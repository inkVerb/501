<?php

// This parses a URL, Title, and Credit
// They must all be on the same line and separated by double semicolon ;;
/* Both of these will work:
https://write.pink;; Noun Case;; PinkWrite
<a href="http://jesse.house">Jesse House // Books</a>
*/

// Parse a POST
//Basic
if (($_SERVER['REQUEST_METHOD'] === 'POST') && (!preg_match('/([\]\[\\{\}\$\*]+)/i', $_POST['p_links']))) {



  // echo demo
  echo '<pre><h1>First, we get this info from our filters:</h1></pre>';



  // Prepare our values
  $links_array = array();
  $p_links = $_POST['p_links'];
  $arr = explode("\n", $p_links);
  $regex_replace = "/[^a-zA-Z0-9-!@&#$%.'\":]/";

  // Start our array key
  $i = 0;

  // Iterate through that array
  foreach ($arr as $line) {

    // Is HTML link?
    if (($line != strip_tags($line))
    &&  (strpos($line, 'href='))) { // true = html

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
        }

        // Validate & Sanitize
        if ( (filter_var($url,FILTER_VALIDATE_URL)) && (strlen($url) <= 148) ) {
          $url = $url;
          $credit = substr(preg_replace($regex_replace," ", $credit), 0, 54);
          $title = substr(preg_replace($regex_replace," ", $title), 0, 91);

        } else { // Next item if we processed no URL
          unset($url);
          unset($title);
          unset($credit);
          continue;
        }

      } else { // Next item if we processed no URL
        unset($url);
        unset($title);
        unset($credit);
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

      // Only get the $credit from a cut if there is no $part3
      if ((isset($part3)) && ($part3 == '') && ($part2 != '')) {
        if (strpos($part2, '|') !== false) {
          $credit = substr($part1, strrpos($part1, '|') + 1);
        } elseif (strpos($part1, '|') !== false) {
          $credit = substr($part1, strrpos($part1, '|') + 1);
        }
      }

      // Tuncate after any pipe
      if (strpos($part1, '|') !== false) {
        $part1 = substr($part1, 0, strpos($part1, '|'));
      }
      if (strpos($part2, '|') !== false) {
        $part2 = substr($part2, 0, strpos($part2, '|'));
      }
      if (strpos($part3, '|') !== false) {
        $part3 = substr($part3, 0, strpos($part3, '|'));
      }

      // Process 3 parts in reverse order
      // Running last things first, assign values based on the remaining possible outcomes

      // Part 3
      if ( (isset($part3)) && ($part3 != '') ) {
        if ( (filter_var($part3,FILTER_VALIDATE_URL)) && (strlen($part3) <= 128) ) {
          $url = $part3;
          $parsed_url = parse_url($url); // This pulls different things from a URL
          $host = $parsed_url['host']; // This gets just the host
        } else { // Third part is not a URL, it can only be a Credit
          $credit = substr(preg_replace($regex_replace," ", $part3), 0, 56);
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
          $credit = substr(preg_replace($regex_replace," ", $part2), 0, 54);
        } elseif (!isset($title)) { // Something good has happened, we'll do what's left
          $title = substr(preg_replace($regex_replace," ", $part2), 0, 91);
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
          }
          if ( (!isset($credit)) && (isset($url)) ) {// If we don't have a Credit by now, put a placeholder
            $credit = $host;
          } elseif (!isset($credit)) {
            $credit = 'link';
          }

        } elseif ( (isset($url)) && (!isset($title)) ) { // No Title? set it now
            $title = substr(preg_replace($regex_replace," ", $part1), 0, 91);
          if (!isset($credit)) { // If we didn't get a $credit from a cut
            $credit = $host; // We already have a URL, get its host for our Credit
            }
        } else {
          $no_url_part1 = true;
        }
      } else {
        $no_url_part1 = true;
      }

      // Next item if we processed no URL
      if ( (isset($no_url_part1)) && (isset($no_url_part2)) && (isset($no_url_part3)) ) {
        unset($url);
        unset($title);
        unset($credit);
        continue;
      }

    } // All values should be set


    // trim() whitespace
    $url = trim($url);
    $title = trim($title);
    $credit = trim($credit);

    // Iterate into array
    $links_array[$i][0] = $url;
    $links_array[$i][1] = $title;
    $links_array[$i][2] = $credit;





    // echo demo
    $url_ = $links_array[$i][0];
    $title_ = $links_array[$i][1];
    $credit_ = $links_array[$i][2];
    echo "<pre><i>$url_</i> <b>$title_ // $credit_</b></pre>";




    // Next item
    $i++;

    // Careful of those non-empty variables
    unset($url);
    unset($title);
    unset($credit);

  }


  // echo demo
  echo '<pre><h1>Second, we have all our info in $links_array[]:</h1></pre>';
  foreach ($links_array as $line_item) {
    echo "<pre style=\"color:#225893;\"><b>$line_item</b></pre>";
    foreach ($line_item as $key => $avalue) {
      echo "<pre style=\"color:#227883;\">[$key] = $avalue</pre>";
    }
  }


  // Send $links_array to JSON

  // echo demo
  // ...This will go into our database

  // Parse JSON into <a> tag variables

  // echo demo
  // ...This came from our database to our HTML page



$posted_value = $p_links; // Keep our form populated in this demo
}


// Create our form (a little bigger with: cols="80" rows="8")
echo '
<form action="jsonarraylinkparse.php" method="post">
  <textarea id="p_links" name="p_links" cols="80" rows="8">'.$posted_value.'</textarea>
  <br><br>
  <input type="submit" value="Parse me">
</form>';

// Add a note
// Prep this so we see the HTML, not just what it renders
$string1 = htmlspecialchars('<a href="https://inkisaverb.com">Ink is a verb.</a>');
$string2 = htmlspecialchars('<a href="https://verb.vip">Get inking. // VIP Linux</a>');
echo
'
<code><b>For example, try these...</b></code><br><br>
<code>https://verb.one</code><br>
<code>https://verb.red;;Get inking.</code><br>
<code>https://verb.ink;;Ink is a verb.;;inkVerb</code><br>
<code>'.$string1.'</code><br>
<code>'.$string2.'</code><br>
';

?>
