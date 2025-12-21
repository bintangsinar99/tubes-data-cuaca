<?php
function http_request_get($url) {
    // persiapkan curl
    $ch = curl_init(); 
 
    // set url 
    curl_setopt($ch, CURLOPT_URL, $url);
 
    // konversi hasil ke string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
 
     // deteksi user agent
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'); 
 
    // eksekusi
    $output = curl_exec($ch); 
 
    // tutup curl 
    curl_close($ch);      
 
    // mengembalikan hasil curl
    return $output;
}
?>