<?php

//include_once('common.php');

echo $_SERVER['DOCUMENT_ROOT'].'</br>';

echo 'PHP version is <b>'. phpversion().'</b>';

chkServer('gateway.sandbox.push.apple.com',2195); 

chkServer('smtp.mailgun.org',465); 

chkServer('smtp.mailgun.org',587); 

// ================================ Disk Space Start
function isa_bytes_to_gb($bytes, $decimal_places = 1 ){
    return number_format($bytes / 1073741824, $decimal_places);
}
$free = disk_free_space("/");
$total = disk_total_space("/");
$percent = ($free/$total) * 100;
echo "<br> Total Space GB: ".isa_bytes_to_gb($total);
echo "<br> Total Free Space GB: ".isa_bytes_to_gb($free);
// ================================ Disk Space END

// ================================ Processor Start
$ncpu = 1;
if(is_file('/proc/cpuinfo')) {
    $cpuinfo = file_get_contents('/proc/cpuinfo');
    preg_match_all('/^processor/m', $cpuinfo, $matches);
    $ncpu = count($matches[0]);
}
if($ncpu < 8){
  $styleproce = " style='color:red; font-weight:600; '";
}
echo "<br> <p ".$styleproce." >Number Of Processors Installed: ".$ncpu ."</p>";
// ================================ Processor END

//chkServer('gateway.push.apple.com',2195); 

    $fh = fopen('/proc/meminfo','r');
    $mem = 0;
    while ($line = fgets($fh)) {
		$pieces = array();
    if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
      $mem = $pieces[1];
      break;
    }
    }
    fclose($fh);
    
    
    $mem=$mem/(1024*1024);
    $mem=round($mem,0);
    echo "</br> $mem GB RAM found</br>";

function get_tls_version($sslversion = null)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, "https://www.howsmyssl.com/a/check");
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    if ($sslversion !== null) {
        curl_setopt($c, CURLOPT_SSLVERSION, $sslversion);
    }
    $rbody = curl_exec($c);
    if ($rbody === false) {
        $errno = curl_errno($c);
        $msg = curl_error($c);
        curl_close($c);
        return "Error! errno = " . $errno . ", msg = " . $msg;
    } else {
        $r = json_decode($rbody);
        curl_close($c);
        return $r->tls_version;
    }
}

echo "<pre>\n";

echo "OS: " . PHP_OS . "\n";
echo "uname: " . php_uname() . "\n";
echo "PHP version: " . phpversion() . "\n";

$curl_version = curl_version();
echo "curl version: " . $curl_version["version"] . "\n";
echo "SSL version: " . $curl_version["ssl_version"] . "\n";
echo "SSL version number: " . $curl_version["ssl_version_number"] . "\n";
echo "OPENSSL_VERSION_NUMBER: " . dechex(OPENSSL_VERSION_NUMBER) . "\n";

echo "TLS test (default): " . get_tls_version() . "\n";
echo "TLS test (TLS_v1): " . get_tls_version(1) . "\n";
echo "TLS test (TLS_v1_2): " . get_tls_version(6) . "\n";

echo "</pre>\n";

function chkServer($host, $port) 

{ 

  $hostip = @gethostbyname($host); 

  

  if ($hostip == $host) 

  { 

    echo "Server is down or does not exist"; 

  } 

  else 

  { 

    if (!$x = @fsockopen($hostip, $port, $errno, $errstr, 5)) 

    { 

      echo "<br /><span style='color:#FF0000;text-align:center;'>Port $port is <b>CLOSED.</b></span>"; 

    } 

    else 

    { 

      echo "<br /><span style='color:#32CD32;text-align:center;'>Port $port is <b>OPEN.</b></span>"; 

      if ($x) 

      { 

        @fclose($x); 

      } 

    } 

  } 

} 



 if(extension_loaded('ionCube Loader')) {

  echo "<br /><span style='color:#32CD32;text-align:center;'>ionCube Loader <b>installed</b></span>";

}       

else{

  echo "<br /><span style='color:#FF0000;text-align:center;'>ionCube Loader <b>NOT</b> installed</span>";

}



if(extension_loaded('mbstring')) {

  echo "<br /><span style='color:#32CD32;text-align:center;'>mbstring  <b>installed</b></span>";

}       

else{

  echo "<br /><span style='color:#FF0000;text-align:center;'>mbstring  <b>NOT</b> installed</span>";

}



if(extension_loaded('curl')) {

  echo "<br /><span style='color:#32CD32;text-align:center;'>curl  <b>installed</b></span>";

}       

else{

  echo "<br /><span style='color:#FF0000;text-align:center;'>curl  <b>NOT</b> installed</span>";

}

if(extension_loaded('mysql')) {

  echo "<br /><span style='color:#32CD32;text-align:center;'>mysql <b>installed</b></span>";

}       

else{

  echo "<br /><span style='color:#FF0000;text-align:center;'>mysql <b>NOT</b> installed</span>";

} 

if(extension_loaded('mysqli')) {

  echo "<br /><span style='color:#32CD32;text-align:center;'>mysqli <b>enabled</b></span>";

}       

else{

  echo "<br /><span style='color:#FF0000;text-align:center;'>mysqli <b>NOT</b> installed</span>";

}



if( ini_get('allow_url_fopen') ) {

  echo "<br /><span style='color:#32CD32;text-align:center;'>allow_url_fopen <b>OPEN</b></span>";

} 

else{

 echo "<br /><span style='color:#FF0000;text-align:center;'>allow_url_fopen <b>NOT</b> OPEN</span>";

}

if( ini_get('short_open_tag') ) {

  echo "<br /><span style='color:#32CD32;text-align:center;'>short_open_tag <b>ON</b></span>";

} 

else{

 echo "<br /><span style='color:#FF0000;text-align:center;'>short_open_tag <b>OFF</b></span>";

}





//print_r(get_loaded_extensions());

 //print_r(mysql_get_server_info());

        

phpinfo();

?>

