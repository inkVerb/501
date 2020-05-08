<?php
$date_now = date("Y-m-d H:i:s"); // SQL date format
$epoch_date_now = strtotime($date_now); // SQL date -> PHP epoch
$epoch_time_now = time(); // PHP epoch
$thirty_days = (30 * 24 * 60 * 60); // (30 days * hours * minutes * seconds)
$epoch_later = $epoch_time_now + $thirty_days; // epoch 30 days from now
$epoch_simple_later = time() + (30 * 24 * 60 * 60); // epoch 30 days from now
$date_later = date("Y-m-d H:i:s", substr($epoch_later, 0, 10)); // epoch -> SQL date (for whatever date $epoch_later is)

echo '$date_now: '.$date_now.'<br>';
echo '$epoch_date_now: '.$epoch_date_now.'<br>';
echo '$epoch_time_now: '.$epoch_time_now.'<br>';
echo '$thirty_days: '.$thirty_days.'<br>';
echo '$epoch_later: '.$epoch_later.'<br>';
echo '$epoch_simple_later: '.$epoch_simple_later.'<br>';
echo '$date_later: '.$date_later.'<br>';
?>
