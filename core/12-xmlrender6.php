<?php

  // Set the MIME type
  header('Content-type: text/xml'); // Comment to break //

  // Prepare special characters variable
  $special_chars_raw = '" < > &';

  // Convert to HTML entities
  $special_chars = htmlentities($special_chars_raw); // Comment to break //
  // $special_chars = $special_chars_raw;
  // $special_chars = '&quot; &lt; &gt; &amp;';

  // Prepare the XML document
  $xml_text = <<<EOF
<?xml version="1.0" encoding="utf-8" ?>
<root>

  <spcharcdata>
    <![CDATA[$special_chars_raw]]>
  </spcharcdata>

  <something attrib="$special_chars">
    <spchar>
      $special_chars
    </spchar>
  </something>

</root>
EOF;

  // echo everything
  echo $xml_text;

?>
