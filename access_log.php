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
$write_handle = fopen('../logs/access_log.csv', 'w');
$count = 0;
$clients = array();
$ips = array();
$paths = array();
$useragents = array();

$row = array('IP', 'Date', 'Client', 'Operation', 'Path', 'URL', 'Response Code', 'Referrer', 'User Agent');
fputcsv($write_handle, $row);

while (($line = fgets($handle)) !== FALSE)
{
  // IP.
  $stop = strpos($line, ' - - ');
  $ip = substr($line, 0, strpos($line, ' - - '));
  $line = substr($line, $stop + 5);

  // Clients Sum
  if (!isset($ips[$ip]))
  {
    $ips[$ip] = 0;
  }
  $ips[$ip]++;

  // Date
  $stop = strpos($line, ']');
  $date = substr($line, 1, $stop - 1);
  $line = substr($line, $stop + 1);

  // Ony today.
  if (strpos($date, '03/Oct/2022') === FALSE)
  {
    continue;
  }

  // url
  $stop = strpos($line, '"', 2);
  $url = substr($line, 2, $stop - 2);
  $line = substr($line, $stop + 1);
  $client = '';
  $operation = '';
  $path = '';
  $address = '';
  if (strlen($url) && ((int)strpos($url, '/') !== 0))
  {
    $parts = explode(' ', $url);
    if (isset($parts[0]))
    {
      $operation = $parts[0];
    }
    if (isset($parts[1]))
    {
      $address = $parts[1];
      $stop = strpos($address, '/', 1);
      $client = substr($address, 1, $stop - 1);
      $end = strpos($address, '?');
      if ($end)
      {
        $path = substr($address, $stop, $end - $stop);
      }
      else
      {
        $path = substr($address, $stop);
      }
    }
  }

  // Path Sum
  if (!isset($paths[$path]))
  {
    $paths[$path] = 0;
  }
  $paths[$path]++;

//  $agent =
  // Client Filter
//  if (!$client || in_array($client, array('admin-portal', 'patient-portal')))
//  {
////    continue;134.209.146.245
//  }

  // Clients Sum
  if (!isset($clients[$client]))
  {
    $clients[$client] = 0;
  }
  $clients[$client]++;

  // Status
  $response_code = substr($line, 1, 3);
  $line = substr($line, strpos($line, '"') + 1);

  // Referrer
  $stop = strpos($line, '"');
  $referrer = substr($line, 0, $stop);
  $useragent = substr($line, $stop + 3, -2);

  // Path Sum
  if (!isset($useragents[$useragent]))
  {
    $useragents[$useragent] = 0;
  }
  $useragents[$useragent]++;


//  if (strpos($url, '/batch') === FALSE)
//  {
//    continue;
//  }
  // Path Filter.
//  if (strpos($url, 'poll/site/check') !== FALSE ||
//      strpos($url, '/server-status-AXSG7ZWUMC7VY2DF') !== FALSE ||
//      strpos($url, '/chat') !== FALSE ||
//      strpos($url, '/logout') !== FALSE ||
////      strpos($url, '/login') !== FALSE ||
//      strpos($url, '/ajax') !== FALSE ||
//      strpos($url, '/poll/chat') !== FALSE)
//  {
//    continue;
//  }

  $row = array($ip, $date, $client, $operation, $path, $url, $response_code, $referrer, $useragent);
  fputcsv($write_handle, $row);
}
fclose($handle);
fclose($write_handle);

$clients_handle = fopen('../logs/clients_list.csv', 'w');
fputcsv($clients_handle, array('Client', 'Count'));
foreach ($clients as $client => $count)
{
  fputcsv($clients_handle, array($client, $count));
}
fclose($clients_handle);

$clients_handle = fopen('../logs/ips_list.csv', 'w');
fputcsv($clients_handle, array('IP', 'Count'));
foreach ($ips as $client => $count)
{
  fputcsv($clients_handle, array($client, $count));
}
fclose($clients_handle);

$clients_handle = fopen('../logs/path_list.csv', 'w');
fputcsv($clients_handle, array('Path', 'Count'));
foreach ($paths as $client => $count)
{
  fputcsv($clients_handle, array($client, $count));
}
fclose($clients_handle);

$clients_handle = fopen('../logs/useragent_list.csv', 'w');
fputcsv($clients_handle, array('Path', 'Count'));
foreach ($useragents as $client => $count)
{
  fputcsv($clients_handle, array($client, $count));
}
fclose($clients_handle);

die('done');
