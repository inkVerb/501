<?php

// infoPop
function infoPop($help_id, $infomsg) {
  $result = '
  <div class="infopop" onclick="INFO'.$help_id.'()">&#9432;
    <span class="infopoptext" id="'.$help_id.'">'.$infomsg.'</span>
  </div>
  <script>
    function INFO'.$help_id.'() {
      var infopop = document.getElementById("'.$help_id.'");
      infopop.classList.toggle("show");
    }
  </script>
';

  return $result;
} // Finish function
