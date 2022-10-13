<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("getRequestURI")) {
    function getRequestURI() {
        if (isHTTPS())
            $link = "https";
        else $link = "http";
          
        // Here append the common URL characters.
        $link .= "://";
          
        // Append the host(domain name, ip) to the URL.
        $link .= $_SERVER['HTTP_HOST'];
          
        // Append the requested resource location to the URL
        $link .= $_SERVER['REQUEST_URI'];
        
        $link = current(explode("?", $link));
        
        // Print the link
        return $link;
    }
}

$sqlData = _db()->_selectQ("do_redirector","*",["blocked"=>'false',"source_uri"=>[[getRequestURI(), "/".PAGE, current(explode("?", $_SERVER['REQUEST_URI']))], "IN"]])->_GET();

if($sqlData) {
    $sqlData = $sqlData[0];
    
    $targetURI = $sqlData['target_uri'];
    $redirectionType = $sqlData['redirection_type'];
    
    header("Location: {$targetURI}", true, $redirectionType);
    
    exit("Redirecting ...");
}
?>