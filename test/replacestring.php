<?php
	$data = array('success' => false ,'message'=>array(),'data' => array());

	// Get Epub File Name -> Generate by GET Req from Front End
	header('Content-Type: text/html; charset=utf-8');
	$folderName = $_GET['folder'];
	$fullFolderName = '../localData/epub/'.$folderName;
	$unzipDestination = '../localData/Books/'.$folderName.'/';
	// Get Meta Data

	// End Get Meta Data

	require('util.php');
	require('epub.php');
	require 'TPEpubCreator.php';

	// try{
 //        // $book = $_REQUEST['book'];
 //        // $book = str_replace('..','',$book); // no upper dirs, lowers might be supported later
 //        $epub = new EPub($fullFolderName.'.epub');
 //        var_dump($epub->Authors())."<br>";
 //        var_dump($epub->Cover());
 //        var_dump($epub->Subjects()[0]);
 //        // echo $epub->Authors()."<br>";
 //        // echo $epub->Cover()."<br>";
 //        echo $epub->Description()."<br>";
 //        // echo $epub->Subjects()."<br>";
 //        echo $epub->Publisher()."<br>";
 //        echo $epub->Copyright()."<br>";
 //        echo $epub->Language()."<br>";
 //        echo $epub->ISBN()."<br>";
 //    }catch (Exception $e){
 //        $error = $e->getMessage();
 //    }


	// Extract Epub File
	$zip = new ZipArchive;
    if ($zip->open($fullFolderName.'.epub') === TRUE) {
  
	    
	// End Extract File// Unzip Path
	    $zip->extractTo($unzipDestination);
	    $zip->close();
	    // echo 'Unzipped Process Successful!';
	} else {
	    // echo 'Unzipped Process failed';
	    $data['success'] = false;
	    $data['message'] = 'Unzipped Process failed';
	}

	// Scaning Extraction Directory
	$driveResult = scandir($unzipDestination);
	// Step 1
	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			// $oldTitle = 'style="max-width:100%" "max-height:100%"';
			// $newTitle = 'style="max-width:100%;max-height:100%"';

			$oldTitle = '<img src="cover.jpeg" alt="cover" style="max-width:100%" "max-height:100%"/>';
			$newTitle = '';

			$findBody = '<body>';
			$replaceBody = '<body style= "background-image: url(cover.jpeg);max-height: 100%;max-width:100%;background-position: center;background-repeat: no-repeat;background-size:contain;margin:50px;">';

			$oldAyat1 = '<!--';
			$newAyat1 = '';

			$oldAyat2 = '-->';
			$newAyat2 = '';


			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			$str=str_replace($oldAyat1, $newAyat1,$str);
			$str=str_replace($oldAyat2, $newAyat2,$str);
			$str=str_replace($findBody, $replaceBody,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File

			// Cek firstline
			$file = file('../localData/Books/'.$folderName.'/'.$key);

			$output = $file[0];
			if (trim($output) == '') {
	            unset($file[0]);
        		file_put_contents('../localData/Books/'.$folderName.'/'.$key, $file);
	        }
			// End First line

			// Check if not ended <p>
	        $oldText = '<p class="block_"><img';
			$newText = '<p class="block_"/><img';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldText, $newText,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Check if not ended <p>
		}
	}


	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = "background-image: url(cover.jpeg)";
			$newTitle = "background-image: url('cover.jpeg')";

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 2
	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<html>';
			$newTitle = '<?xml version="1.0" encoding="utf-8"?> 
<html xmlns="http://www.w3.org/1999/xhtml">';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 3

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<meta http-equiv=Content-Type';
			$newTitle = '<!--<meta http-equiv=Content-Type';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 4

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<meta name=Generator content="Microsoft Word 15 (filtered)">';
			$newTitle = '<meta name="Generator" content="Microsoft Word 15 (filtered)"/>';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<meta name="Generator"';
			$newTitle = '--><meta name="Generator"';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 5

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<style>';
			$newTitle = '<style><![CDATA[';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 6

	foreach ($driveResult as $key) {
		if ($key != 'titlepage.xhtml') {
			$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
			if ($ext == 'html' || $ext == 'xhtml') {
				// Manipulate Epub File

				$oldTitle = '</style>';
				$newTitle = ']]></style>';

				$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
				$str=str_replace($oldTitle, $newTitle,$str);
				file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
				// End Manipulate Epub File
			}
		}
		else{
			$oldTitle = '<div>';
			$newTitle = '';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
		}
	}

	// Step 7

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<body lang=EN-US link="#0563C1" vlink="#954F72">';
			$newTitle = '<body lang="EN-US" link="#0563C1" vlink="#954F72">';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 8

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<div class=WordSection1>';
			$newTitle = '<div class="WordSection1">';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 9

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<p class=MsoNormal';
			$newTitle = '<p class="MsoNormal"';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 10

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<span lang=IN';
			$newTitle = '<span lang="IN"';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 11

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = 'font-family:"Arial","sans-serif"';
			$newTitle = 'font-family:&quot;Arial&quot;,&quot;sans-serif&quot;;';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File

			// Update x
			$oldText = 'lang=IN';
			$newText = 'lang="IN"';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldText, $newText,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Update x

			// Update x
			$oldText = 'lang=EN-US';
			$newText = 'llang="EN-US"';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldText, $newText,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Update x
		}
	}

	// Step 12

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '&#9668;';
			$newTitle = '◄';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 13

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = 'font-family:"Times New Roman","serif";';
			$newTitle = 'font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 14

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '&nbsp;';
			$newTitle = '';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 15

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<![CDATA[<![CDATA[';
			$newTitle = '<![CDATA[';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 16

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = ']]>]]>';
			$newTitle = ']]>';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 17

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<p class="block_" lang="en">';
			$newTitle = '';

			$findTitle = '/></p>';
			$replaceTitle = '/>';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			$str=str_replace($findTitle, $replaceTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 18

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'css') {
			// Manipulate Epub File

			$oldTitle = '.block_2 {';
			$newTitle = '.block_2 {text-align:justify;';

			$oldTitle1 = '.block_1 {';
			$newTitle1 = '.block_1 {text-align:justify;';

			$oldTitle2 = '.block_3 {';
			$newTitle2 = '.block_3 {text-align:justify;';

			$oldTitle3 = '.block_4 {';
			$newTitle3 = '.block_4 {text-align:justify;';

			$oldTitle4 = '.block_5 {';
			$newTitle4 = '.block_5 {text-align:justify;';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			$str=str_replace($oldTitle1, $newTitle1,$str);
			$str=str_replace($oldTitle2, $newTitle2,$str);
			$str=str_replace($oldTitle3, $newTitle3,$str);
			$str=str_replace($oldTitle4, $newTitle4,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 19

	foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'css') {
			// Manipulate Epub File

			$oldTitle = 'class="calibre2"/>';
			$newTitle = '.block_2 {text-align:justify;';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 20 -- Remove Index
	foreach ($driveResult as $key) {
        $ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
        if ($ext == 'ncx') {
        	$oldTitle = '<navMap>';
			$newTitle = '<!--<navMap>';

			$oldTitle1 = '</navMap>';
			$newTitle1 = '</navMap>-->';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			$str=str_replace($oldTitle1, $newTitle1,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
        }
        // if (substr($key, 0,11) == 'index_split' ) {
        //     array_push($data, $key);
        // }
    }

    // Step 21 -- Add index
    $oldText = '</ncx>';
    $newText = '<navMap>';

    $dataIndex=[];
    $dataIndex2 = array();
    foreach ($driveResult as $key) {
    	$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
        if (substr($key, 0,11) == 'index_split' ) {
            array_push($dataIndex, $key);
        }
        if (substr($key, 0,4) == 'page' ) {
        	$extension = $ext;
            if ($ext == 'xhtml') {
                $split = explode('_', $key);
                $split2 = explode('.', $split[1]);
                // var_dump($split2);
                // $dataIndex['FileName'] = $key;
                // $dataIndex['index'] = $split2[0];
                $xdata = array(
                    'FileName' => $key,
                    'index'    => (int)$split2[0]
                );
                array_push($dataIndex2, $xdata);
            }
        }
    }

    if (count($dataIndex2) > 0) {
    	usort($dataIndex2, function($a, $b) {
	        return $a['index'] <=> $b['index'];
	    });
    	$index =1;
	    foreach ($dataIndex2 as $key) {
	    	$newText .= '<navPoint id="num_'.$index.'" playOrder="'.$index.'">';
	    	$newText .= '<navLabel><text>Baca: Tanggal '.$index.'</text></navLabel>';
	    	$newText .= '<content src="'.$key['FileName'].'"/></navPoint>';
	    	$index += 1;
	    }
    }
    else{
    	$index =1;
	    foreach ($dataIndex as $key) {
	    	$newText .= '<navPoint id="num_'.$index.'" playOrder="'.$index.'">';
	    	$newText .= '<navLabel><text>Baca: Tanggal '.$index.'</text></navLabel>';
	    	$newText .= '<content src="'.$key.'"/></navPoint>';
	    	$index += 1;
	    }
    }
    
    $newText .= "</navMap></ncx>";
    // var_dump($newText);
    foreach ($driveResult as $key) {
    	$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
        if ($ext == 'ncx') {
     		$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldText, $newText,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);       
        }
    }

    // Step 22

    foreach ($driveResult as $key) {
		$ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
		if ($ext == 'html' || $ext == 'xhtml') {
			// Manipulate Epub File

			$oldTitle = '<p class="block_8" lang="en"><img';
			$newTitle = '<img';

			$str=file_get_contents('../localData/Books/'.$folderName.'/'.$key);
			$str=str_replace($oldTitle, $newTitle,$str);
			file_put_contents('../localData/Books/'.$folderName.'/'.$key, $str);
			// End Manipulate Epub File
		}
	}

	// Step 23

	$dataIndex=[];
    $extension = '';
    foreach ($driveResult as $key) {
        if (substr($key, 0,11) == 'index_split' ) {
            $ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
            $extension = $ext;
            array_push($dataIndex, $key);
        }
    }
    // var_dump($dataIndex);
    $index = 1;
    $html = '<?xml version="1.0" encoding="utf-8"?>
        <html xmlns="http://www.w3.org/1999/xhtml" lang="id" xml:lang="id">
            <head>
                <title>Unknown</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            </head>
            <body style= "background-image: url(images/image.jpeg);max-height: 100%;max-width:100%;background-position: center;background-repeat: no-repeat;background-size:contain;margin:50px;">

            </body>
        </html>
    ';

    $xhtml = str_replace("url(images/image.jpeg)", "url('images/image.jpeg')", $html);
    foreach ($dataIndex as $key) {
        if (count($dataIndex) == $index) {
            // echo $key;
            // file_put_contents($key.'.'.$extension, "");
            file_put_contents('../localData/Books/'.$folderName.'/'.$key,$xhtml);
        }
        $index += 1;
    }

    // Step 24

    $dataIndex=array();
    $dataImage=array();
    $extension = '';
    foreach ($driveResult as $key) {
        $ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
        if (substr($key, 0,4) == 'page') {
            $extension = $ext;
            if ($ext == 'xhtml') {
                $split = explode('_', $key);
                $split2 = explode('.', $split[1]);
                // var_dump($split2);
                // $dataIndex['FileName'] = $key;
                // $dataIndex['index'] = $split2[0];
                $xdata = array(
                    'FileName' => $key,
                    'index'    => (int)$split2[0]
                );
                array_push($dataIndex, $xdata);
            }
        }
        if (($ext == 'jpg' || $ext =='jpeg' || $ext == 'png') && ($key != 'cover.jpeg' && $key != 'cover.jpg' && $key != 'cover.png')) {
            $splitimage = explode('-', $key);
            $ximage = array(
                'FileName' => $key,
                'index'    => (int)$splitimage[0]
            );
            array_push($dataImage, $ximage);
        }
    }
    // array_sort_by_column($dataIndex[1],SORT_ASC,$dataIndex);
    // var_dump($dataIndex);
    // var_dump($dataImage);
    // array_multisort($dataIndex,SORT_ASC,$dataIndex);
    usort($dataIndex, function($a, $b) {
        return $a['index'] <=> $b['index'];
    });
    usort($dataImage, function($a, $b) {
        return $a['index'] <=> $b['index'];
    });
    // echo json_encode($dataIndex);
    // echo '<br><br>';
    // echo json_encode($dataImage);

    // var_dump($dataIndex);
    $index = 0;
    
    foreach ($dataIndex as $key) {
        // var_dump($dataImage[$index]['FileName']);
        
        $html = '
            <html xmlns="http://www.w3.org/1999/xhtml" lang="id" xml:lang="id">
                <head>
                    <title>Unknown</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                </head>
                <body style= "background-image: url('.$dataImage[$index]['FileName'].');max-height: 100%;max-width:100%;background-position: center;background-repeat: no-repeat;background-size:contain;margin:50px;">

                </body>
            </html>
        ';

        $xhtml = str_replace("url(".$dataImage[$index]['FileName'].")", "url('".$dataImage[$index]['FileName']."')", $html);
        file_put_contents('../localData/Books/'.$folderName.'/'.$key["FileName"],$xhtml);
    
        $index += 1;
    }
    
	// End Scaning Extraction Directory

	
	// Packing to Epub File
	$rootPath = realpath('../localData/Books/'.$folderName);

	// Initialize archive object
	$zip->open($fullFolderName.'.epub', ZipArchive::CREATE | ZipArchive::OVERWRITE);

	// Create recursive directory iterator
	/** @var SplFileInfo[] $files */
	$files = new RecursiveIteratorIterator(
	    new RecursiveDirectoryIterator($rootPath),
	    RecursiveIteratorIterator::LEAVES_ONLY
	);

	foreach ($files as $name => $file)
	{
	    // Skip directories (they would be added automatically)
	    if (!$file->isDir())
	    {
	        // Get real and relative path for current file
	        $filePath = $file->getRealPath();
	        $relativePath = substr($filePath, strlen($rootPath) + 1);

	        // Add current file to archive
	        $zip->addFile($filePath, $relativePath);
	    }
	}

	// Zip archive will be created only after closing object
	$zip->close();
	// End Packing to Epub File

	$pathx = "../localData/Books/".$folderName;
	removeDirectory($pathx);

	function removeDirectory($path) {

		$files = glob($path . '/*');
		foreach ($files as $file) {
			is_dir($file) ? removeDirectory($file) : unlink($file);
		}
		rmdir($path);

		return;
	}

	$data['success'] = true;

	echo json_encode($data);
?>