<?php

/*

Simplified FCC Lookup for Active Amateur Radio Licenses

Web syntax:
https://www.example.com/this_file.php?callsign=K7COI

Command line synax:
php this_file.php K7COI

Copyright (C) 2020 Kevin Durette, K7COI

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.


*/

error_reporting(E_ALL);

if (!isset($_GET["callsign"]) && !isset($argv[1])) {
   echo 'error: no callsign';
   exit;
}

if (isset($_GET["callsign"])) {
   $callsign = $_GET["callsign"];
} else {
   $callsign = $argv[1];
}


if (!preg_match('/[A-Z]{1,2}[0-9]{1}[A-Z]{1,3}/', $callsign)) {
   echo 'error: bad callsign';
   exit;
}


$search_url = 'https://wireless2.fcc.gov/UlsApp/UlsSearch/results.jsp';

$search_request = 'fiulsTrusteeName=&fiOwnerName=&fiUlsFRN=&fiCity=&ulsState=&fiUlsZipcode=&ulsCallSign=' . $callsign . '&AActive=AActive&ulsDateType=&dateSearchType=+&ulsFromDate=&ulsToDate=6%2F24%2F2020&fiRowsPerPage=10&ulsSortBy=uls_l_callsign++++++++++++++++&ulsOrderBy=ASC&x=30&y=2&hiddenForm=hiddenForm&jsValidated=true';

$search_context_opts = array('http' =>
   array(
      'method'  => 'POST',
      'header'  =>
         "content-type: application/x-www-form-urlencoded\r\n" .
         "authority: wireless2.fcc.gov\r\n" .
         "cache-control: max-age=0\r\n" .
         "upgrade-insecure-requests: 1\r\n" .
         "origin: https://wireless2.fcc.gov\r\n" .
         "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36\r\n" .
         "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9\r\n" .
         "sec-fetch-site: same-origin\r\n" .
         "sec-fetch-mode: navigate\r\n" .
         "sec-fetch-user: ?1\r\n" .
         "sec-fetch-dest: document\r\n" .
         "referer: https://wireless2.fcc.gov/UlsApp/UlsSearch/searchAmateur.jsp\r\n" .
         "accept-language: en-US,en;q=0.9\r\n",
      'content' => $search_request,
      'timeout' => 60
   )
);

$search_context  = stream_context_create($search_context_opts);
$search_result = file_get_contents($search_url, false, $search_context, -1, 40000);

preg_match('/<a href=(license.jsp[^>]+) title="View Licensee">' . $callsign . '<\/a>/', $search_result, $license_url_array);


$license_url = 'https://wireless2.fcc.gov/UlsApp/UlsSearch/' . $license_url_array[1];

$license_result = file_get_contents($license_url);

$license_result = str_replace("\t", " ", $license_result);
$license_result = preg_replace('/[ ][ ]+/', ' ', $license_result);
$license_result = str_replace("\n ", "\n", $license_result);
$license_result = preg_replace('/<!--[^-]*-->/', '', $license_result);
$license_result = str_replace("\r", "", $license_result);
$license_result = str_replace("\n", " ", $license_result);


preg_match('/Operator Class<\/td> <td [^>]+>([^<]+)<\/td>/', $license_result, $operator_class_array);
$operator_class = $operator_class_array[1];
$operator_class = str_replace("&nbsp;", " ", $operator_class);
$operator_class = trim($operator_class);

preg_match('/Licensee Name<\/b><\/td> <\/tr> <tr> <td class="cell-pri-light" valign="top" colspan="3" align="left">([^\/]+)\/td>/', $license_result, $licensee_name_array);
$licensee_name = $licensee_name_array[1];
$licensee_name = str_replace("&nbsp;", " ", $licensee_name);
$licensee_name = str_replace("<br>", "\n", $licensee_name);
$licensee_name = str_replace("\n ", "\n", $licensee_name);
$licensee_name = rtrim($licensee_name, '<');
$licensee_name = trim($licensee_name);

$response = $licensee_name . "\n" . $operator_class . "\n"; 

if (isset($_GET["callsign"])) {
   $response = '<pre>' . $response . '</pre>';
}

echo $response;

