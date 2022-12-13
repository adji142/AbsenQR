<?php
    header('Content-Type: text/html; charset=utf-8');
    // $folderName = $_GET['folder'];
    // $fullFolderName = '../localData/epub/'.$folderName;
    // $unzipDestination = '../localData/Books/'.$folderName.'/';

    // $driveResult = scandir($unzipDestination);
    // $dataIndex=array();
    // $dataImage=array();
    // $extension = '';
    // foreach ($driveResult as $key) {
    //     $ext = pathinfo($unzipDestination.$key, PATHINFO_EXTENSION);
    //     if (substr($key, 0,4) == 'page') {
    //         $extension = $ext;
    //         if ($ext == 'xhtml') {
    //             $split = explode('_', $key);
    //             $split2 = explode('.', $split[1]);
    //             // var_dump($split2);
    //             // $dataIndex['FileName'] = $key;
    //             // $dataIndex['index'] = $split2[0];
    //             $xdata = array(
    //                 'FileName' => $key,
    //                 'index'    => (int)$split2[0]
    //             );
    //             array_push($dataIndex, $xdata);
    //         }
    //     }
    //     if (($ext == 'jpg' || $ext =='jpeg' || $ext == 'png') && ($key != 'cover.jpeg' && $key != 'cover.jpg' && $key != 'cover.png')) {
    //         $splitimage = explode('-', $key);
    //         $ximage = array(
    //             'FileName' => $key,
    //             'index'    => (int)$splitimage[0]
    //         );
    //         array_push($dataImage, $ximage);
    //     }
    // }
    // // array_sort_by_column($dataIndex[1],SORT_ASC,$dataIndex);
    // // var_dump($dataIndex);
    // // var_dump($dataImage);
    // // array_multisort($dataIndex,SORT_ASC,$dataIndex);
    // usort($dataIndex, function($a, $b) {
    //     return $a['index'] <=> $b['index'];
    // });
    // usort($dataImage, function($a, $b) {
    //     return $a['index'] <=> $b['index'];
    // });
    // // echo json_encode($dataIndex);
    // // echo '<br><br>';
    // // echo json_encode($dataImage);

    // // var_dump($dataIndex);
    // $index = 0;
    
    // foreach ($dataIndex as $key) {
    //     // var_dump($dataImage[$index]['FileName']);
        
    //     $html = '
    //         <html xmlns="http://www.w3.org/1999/xhtml" lang="id" xml:lang="id">
    //             <head>
    //                 <title>Unknown</title>
    //                 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    //             </head>
    //             <body style= "background-image: url('.$dataImage[$index]['FileName'].');max-height: 100%;max-width:100%;background-position: center;background-repeat: no-repeat;background-size:contain;margin:50px;">

    //             </body>
    //         </html>
    //     ';

    //     $xhtml = str_replace("url(".$dataImage[$index]['FileName'].")", "url('".$dataImage[$index]['FileName']."')", $html);
    //     file_put_contents('../localData/Books/'.$folderName.'/'.$key["FileName"],$xhtml);
    
    //     $index += 1;
    // }

    // $firstline='head -n1 firstline.html';
    // echo $firstline;
    $f = fopen('firstline.html', 'r');
    $line = fgets($f);
    // var_dump($line);
    if ($line == " ") {
        var_dump('expression');
        $file = file('firstline.html');
        $output = $file[0];
        unset($file[0]);
        file_put_contents($filename, $file);
    }
    else {
        // var_dump('x');
        $file = file('firstline.html');
        // var_dump($file);
        $output = $file[0];
        var_dump(trim($output));
        if (trim($output) == '') {
            unset($file[0]);
            file_put_contents('firstline.html', $file);
        }
        
    }
    fclose($f);
    
?>