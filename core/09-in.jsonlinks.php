<?php

// This parses a URL, Title, and Credit
// They must all be on the same line and separated by double semicolon ;;
/* Both of these will work:
https://inkisaverb.com;; Title Here;; Credit Here
<a href="http://inkisaverb.com">Title Here // Credit Here</a>
*/

/*
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

*/


// Basic RegEx for no brackets and coding non-link safety checks
if ( ($p_links_check != '') && (!empty($p_links_check)) && (preg_match("/((https?)\:\/\/)?/",$p_links_check)) ) {


  // Prepare our values
  $links_array = array(); // Set it before the foreach loop where we use it
  $arr = explode("\n", $p_links_check);
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
      $part3 = (empty($part[2])) ? '' : trim($part[2]);
      $part2 = (empty($part[1])) ? '' : trim($part[1]);
      $part1 = (empty($part[0])) ? '' : trim($part[0]);

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

    // Next item
    $i++;

    // No ghosts from the past
    unset($url);
    unset($title);
    unset($credit);
    if (isset($title_auto)) {unset($title_auto);}
    if (isset($credit_auto)) {unset($credit_auto);}

  }

  // Send $links_array to JSON, as we would before sending to SQL
  $p_links_json_in = json_encode($links_array);

// Fails our non-bracket RegEx check
} else {

  $p_links_json_in = json_encode(explode(', ', ''));

}

?>
