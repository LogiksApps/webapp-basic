{$PAGE.DOCTYPE}

<html {$PAGE.HTML_ATTRIBUTES}>
  <head {$PAGE.HEAD_ATTRIBUTES}>
    <title>{$APPS_NAME}</title>
    
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
		<meta name="msapplication-tap-highlight" content="no">
		<meta name="google" value="notranslate">
		<meta id="win8Icon" name="msapplication-TileImage" content="">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-title" content="">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		
		<link id="favicon" rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link id="icon57" rel="apple-touch-icon" href="./media/logos/icon57.png">
		<link id="icon72" rel="apple-touch-icon" sizes="72x72" href="./media/logos/icon72.png">
		<link id="icon76" rel="apple-touch-icon" sizes="76x76" href="./media/logos/icon76.png">
		<link id="icon114" rel="apple-touch-icon" sizes="114x114" href="./media/logos/icon114.png">
		<link id="icon120" rel="apple-touch-icon" sizes="120x120" href="./media/logos/icon120.png">
		<link id="icon152" rel="apple-touch-icon" sizes="152x152" href="./media/logos/icon152.png">
		<link id="icon167" rel="apple-touch-icon" sizes="167x167" href="./media/logos/icon167.png">
		<link id="icon180" rel="apple-touch-icon" sizes="180x180" href="./media/logos/icon180.png">
		<link id="icon144" rel="icon" type="image/png" sizes="144x144" href="./media/logos/icon144.png"  />
		<link id="icon192" rel="icon" type="image/png" sizes="192x192" href="./media/logos/icon192.png"/>
    
    <!-- start: META -->
    <meta name='description' content='{$PAGE.DESCRIPTION}' />
	<meta name='keywords' content='{$PAGE.KEYWORDS}' />
	<meta name='robots' content='{$PAGE.ROBOTS}' />
	
	{pageseometa}
	<!-- end: META -->
	
	{$PAGE.CSS}
	{$PAGE.JS_PRELOAD}
	
	{logikscripts}
	
	{hook src='postHTMLHead'}
  </head>
  <body {$PAGE.BODY_CLASS} {$PAGE.BODY_ATTRIBUTES} >
  	<div id="wrapper" class='wrapper'>
  		<div id="page-wrapper">
	        {viewpage}
	    </div>
	</div>
	{hook src='postHTMLBody'}
  </body>
	{$PAGE.JS_POSTLOAD}
</html>
{hook src='postHTML'}
