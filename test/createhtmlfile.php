<?php
	$rssFeed = '
		<?xml version="1.0" encoding="utf-8"?>
		<html xmlns="http://www.w3.org/1999/xhtml">
		  <head>
		    <title>Page #1</title>
		    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		  <link rel="stylesheet" type="text/css" href="stylesheet.css"/>
		<link rel="stylesheet" type="text/css" href="page_styles.css"/>
		</head>
		  <body class="calibre">
		        <div class="calibre1">
		            <img src="0 - 0.jpg" alt="comic page #1" class="calibre2"/>
		        </div>
		    </body>
		</html>
	';

	$rssArray = explode('"', $rssFeed);

	// echo $rssArray[1];
	// var_dump($rssArray[27]);

	$driveResult = scandir('../localData/Books/100003/');
	// Step 1
	foreach ($driveResult as $key) {
		$ext = pathinfo('../localData/Books/100003/'.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File
			// $data = readfile("../localData/Books/100003/".$key);
			// $rssArray = explode('"', $data);
			// var_dump($rssArray[27]);

			$myfile = fopen("../localData/Books/100003/".$key, "r") or die("Unable to open file!");
			// echo fread($myfile,filesize("../localData/Books/100003/".$key));
			print(fread($myfile,filesize("../localData/Books/100003/".$key)));
			fclose($myfile);

			// $oldTitle = 'style="max-width:100%" "max-height:100%"';
			// $newTitle = 'style="max-width:100%;max-height:100%"';

			// $oldTitle = '<img src="cover.jpeg" alt="cover" style="max-width:100%" "max-height:100%"/>';
			// $newTitle = '';

			// $findBody = '<body>';
			// $replaceBody = 'background-image: url(cover.jpeg);height: 100%;max-width:100%;background-position: center;background-repeat: no-repeat;background-size: 100% 100%;margin:50px;';

			// $oldAyat1 = '<!--';
			// $newAyat1 = '';

			// $oldAyat2 = '-->';
			// $newAyat2 = '';

			// $str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			// $str=str_replace($oldTitle, $newTitle,$str);
			// $str=str_replace($oldAyat1, $newAyat1,$str);
			// $str=str_replace($oldAyat2, $newAyat2,$str);
			// $str=str_replace($findBody, $replaceBody,$str);
			// file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);


			// End Manipulate Epub File
		}
	}
?>