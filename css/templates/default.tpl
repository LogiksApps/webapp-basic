{$PAGE.DOCTYPE}

<html {$PAGE.HTML_ATTRIBUTES}>
  <head {$PAGE.HEAD_ATTRIBUTES}>
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
	<meta name="msapplication-tap-highlight" content="no">
	<meta name="google" value="notranslate">
	<meta id="win8Icon" name="msapplication-TileImage" content="">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	
	{pluginComponent src='seomanager.headers'}
    
    {$PAGE.CSS}
	{$PAGE.JS_PRELOAD}
	
	{logikscripts}
	
	{hook src='postHTMLHead'}
  </head>
  <body {$PAGE.BODY_CLASS} {$PAGE.BODY_ATTRIBUTES} >
  	<div id="wrapper" class='wrapper'>
  		<div id='header'>
		{component src='header'}
		</div>
		<div id='sidebarLeft' class='sidebarContainer left'>
	    {component src='sidebarLeft'}
	    </div>
	    <div id="page-wrapper">
	        {viewpage}
	    </div>
	</div>
	{hook src='postHTMLBody'}
  </body>
	{$PAGE.JS_POSTLOAD}
</html>
{hook src='postHTML'}
