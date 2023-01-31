<style>
    table, td
    {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 5px;
    }
</style>
<?php

set_time_limit(60 * 60); // 1 hour.
$handle = fopen('../logs/access_log', 'r');
$write_handle = fopen('../logs/access_log_29', 'w');
$count = 0;
//echo '<table>';
while (($line = fgets($handle)) !== FALSE)
{

  $original_line = $line;

  // IP.
  $stop = strpos($line, ' - - ');
  $ip = substr($line, 0, strpos($line, ' - - '));
  $line = substr($line, $stop + 5);

  // Date
  $stop = strpos($line, ']');
  $date = substr($line, 1, $stop - 1);
  $line = substr($line, $stop + 1);
  if ((int)substr($date, 0, 2) !== 29)
  {
//    die('25');
    continue;
  }
  fputs($write_handle, $original_line);

  // url
  $stop = strpos($line, '"', 2);
  $url = substr($line, 2, $stop - 2);
  $line = substr($line, $stop + 1);

  if (strpos($url, 'poll/site/check') !== FALSE ||
    strpos($url, '/server-status-AXSG7ZWUMC7VY2DF') !== FALSE ||
    strpos($url, '/poll/chat') !== FALSE)
  {
    continue;
  }

//  echo '<tr>';
//  echo '<td>' . $ip . '</td>';
//  echo '<td>' . $date . '</td>';
//  echo '<td>' . $url . '</td>';
//  echo '</tr>';
//  if ($count >= 500)
//  {
//    die('</table>');
//    fclose($handle);
//    fclose($write_handle);
//    die('part?');
//  }
//  $count++;
}
fclose($handle);
fclose($write_handle);
die('</table>');
//die('Done');


// Suspect IP: 183.136.225.35 looking at robots.txt
// 98.1.96.171 url was "-"