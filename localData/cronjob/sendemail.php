<?php
    include_once("koneksi.php");
    include "classes/class.phpmailer.php";
    $mail = new PHPMailer;
    // mysqli_query($mysqli, "INSERT INTO testCron VALUES(now())");

    $SQL = "";
    $penerimaNotifikasi = "";
    $body = '
    	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <meta name="viewport" content="width=320, initial-scale=1" />
		  <title>Airmail Confirm</title>
		  <style type="text/css">

		    /* ----- Client Fixes ----- */

		    /* Force Outlook to provide a "view in browser" message */
		    #outlook a {
		      padding: 0;
		    }

		    /* Force Hotmail to display emails at full width */
		    .ReadMsgBody {
		      width: 100%;
		    }

		    .ExternalClass {
		      width: 100%;
		    }

		    /* Force Hotmail to display normal line spacing */
		    .ExternalClass,
		    .ExternalClass p,
		    .ExternalClass span,
		    .ExternalClass font,
		    .ExternalClass td,
		    .ExternalClass div {
		      line-height: 100%;
		    }


		     /* Prevent WebKit and Windows mobile changing default text sizes */
		    body, table, td, p, a, li, blockquote {
		      -webkit-text-size-adjust: 100%;
		      -ms-text-size-adjust: 100%;
		    }

		    /* Remove spacing between tables in Outlook 2007 and up */
		    table, td {
		      mso-table-lspace: 0pt;
		      mso-table-rspace: 0pt;
		    }

		    /* Allow smoother rendering of resized image in Internet Explorer */
		    img {
		      -ms-interpolation-mode: bicubic;
		    }

		     /* ----- Reset ----- */

		    html,
		    body,
		    .body-wrap,
		    .body-wrap-cell {
		      margin: 0;
		      padding: 0;
		      background: #ffffff;
		      font-family: Arial, Helvetica, sans-serif;
		      font-size: 14px;
		      color: #464646;
		      text-align: left;
		    }

		    img {
		      border: 0;
		      line-height: 100%;
		      outline: none;
		      text-decoration: none;
		    }

		    table {
		      border-collapse: collapse !important;
		    }

		    td, th {
		      text-align: left;
		      font-family: Arial, Helvetica, sans-serif;
		      font-size: 14px;
		      color: #464646;
		      line-height:1.5em;
		    }

		    b a,
		    .footer a {
		      text-decoration: none;
		      color: #464646;
		    }

		    a.blue-link {
		      color: blue;
		      text-decoration: underline;
		    }

		    /* ----- General ----- */

		    td.center {
		      text-align: center;
		    }

		    .left {
		      text-align: left;
		    }

		    .body-padding {
		      padding: 24px 40px 40px;
		    }

		    .border-bottom {
		      border-bottom: 1px solid #D8D8D8;
		    }

		    table.full-width-gmail-android {
		      width: 100% !important;
		    }


		    /* ----- Header ----- */
		    .header {
		      font-weight: bold;
		      font-size: 16px;
		      line-height: 16px;
		      height: 16px;
		      padding-top: 19px;
		      padding-bottom: 7px;
		    }

		    .header a {
		      color: #464646;
		      text-decoration: none;
		    }

		    /* ----- Footer ----- */

		    .footer a {
		      font-size: 12px;
		    }
		  </style>

		  <style type="text/css" media="only screen and (max-width: 650px)">
		    @media only screen and (max-width: 650px) {
		      * {
		        font-size: 16px !important;
		      }

		      table[class*="w320"] {
		        width: 320px !important;
		      }

		      td[class="mobile-center"],
		      div[class="mobile-center"] {
		        text-align: center !important;
		      }

		      td[class*="body-padding"] {
		        padding: 20px !important;
		      }

		      td[class="mobile"] {
		        text-align: right;
		        vertical-align: top;
		      }
		    }
		  </style>

		</head>
		<body style="padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
		 <td valign="top" align="left" width="100%" style="background:repeat-x url(https://www.filepicker.io/api/file/al80sTOMSEi5bKdmCgp2) #f9f8f8;">
		 <center>

		   <table class="w320 full-width-gmail-android" bgcolor="#f9f8f8" background="https://www.filepicker.io/api/file/al80sTOMSEi5bKdmCgp2" style="background-color:transparent" cellpadding="0" cellspacing="0" border="0" width="100%">
		      <tr>
		        <td width="100%" height="48" valign="top">
		              <table class="full-width-gmail-android" cellspacing="0" cellpadding="0" border="0" width="100%">
		                <tr>
		                  <td class="header center" width="100%">
		                    <a href="#">
		                      Spirit Booksfield
		                    </a>
		                  </td>
		                </tr>
		              </table>
		        </td>
		      </tr>
		    </table>
    ';
     // Get Email Config

    $SQL .= "SELECT * FROM temailsetting WHERE id = 2";

    $rs = mysqli_query($mysqli, $SQL);

    while($data = mysqli_fetch_array($rs)){
    	$penerimaNotifikasi = $data['emailAdminReciept'];
		$mail->IsSMTP();
		$mail->SMTPSecure = $data['smtp_crypto'];
		$mail->Host = $data['smtp_host']; //hostname masing-masing provider email
		$mail->SMTPDebug = 2;
		$mail->Port = $data['smtp_port'];
		$mail->SMTPAuth = true;
		$mail->Username = $data['smtp_user']; //user email
		$mail->Password = $data['smtp_pass']; //password email
		$mail->SetFrom($data['smtp_user'],$data['AliasName']); //set email pengirim
		$mail->isHTML(true);
    }
     // Cek Calon Penerima

    /*
		$mail->Subject = "Pemberitahuan Email dari Website"; //subyek email
		$mail->AddAddress("admin@namadomain","Nama penerima yang muncul"); //tujuan email
		$mail->MsgHTML("Pengiriman Email Dari Website");
		// if($mail->Send()) echo "Message has been sent";
		// else echo "Failed to sending message";
    */
    $SQL = "SELECT * FROM tpushemail WHERE `Status` = 0 AND (COALESCE(ReceipedEmail,'') != '' OR NotificationType = 'notification' ) ";

    $rs = mysqli_query($mysqli, $SQL);

    // var_dump($rs);
     // Jenis Jenis Email
     /*
		'payment'		=> PaymentRequest,
		'payment_done'	=> PaymentComplate,
		'password_reset'=> ResetPassword,
		'new_release'	=> New Books Release
		'notification'	=> InternalEmailNotfication
     */
     while($data_penerima = mysqli_fetch_array($rs)) { 
     	$body = '
    	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <meta name="viewport" content="width=320, initial-scale=1" />
		  <title>Airmail Confirm</title>
		  <style type="text/css">

		    /* ----- Client Fixes ----- */

		    /* Force Outlook to provide a "view in browser" message */
		    #outlook a {
		      padding: 0;
		    }

		    /* Force Hotmail to display emails at full width */
		    .ReadMsgBody {
		      width: 100%;
		    }

		    .ExternalClass {
		      width: 100%;
		    }

		    /* Force Hotmail to display normal line spacing */
		    .ExternalClass,
		    .ExternalClass p,
		    .ExternalClass span,
		    .ExternalClass font,
		    .ExternalClass td,
		    .ExternalClass div {
		      line-height: 100%;
		    }


		     /* Prevent WebKit and Windows mobile changing default text sizes */
		    body, table, td, p, a, li, blockquote {
		      -webkit-text-size-adjust: 100%;
		      -ms-text-size-adjust: 100%;
		    }

		    /* Remove spacing between tables in Outlook 2007 and up */
		    table, td {
		      mso-table-lspace: 0pt;
		      mso-table-rspace: 0pt;
		    }

		    /* Allow smoother rendering of resized image in Internet Explorer */
		    img {
		      -ms-interpolation-mode: bicubic;
		    }

		     /* ----- Reset ----- */

		    html,
		    body,
		    .body-wrap,
		    .body-wrap-cell {
		      margin: 0;
		      padding: 0;
		      background: #ffffff;
		      font-family: Arial, Helvetica, sans-serif;
		      font-size: 14px;
		      color: #464646;
		      text-align: left;
		    }

		    img {
		      border: 0;
		      line-height: 100%;
		      outline: none;
		      text-decoration: none;
		    }

		    table {
		      border-collapse: collapse !important;
		    }

		    td, th {
		      text-align: left;
		      font-family: Arial, Helvetica, sans-serif;
		      font-size: 14px;
		      color: #464646;
		      line-height:1.5em;
		    }

		    b a,
		    .footer a {
		      text-decoration: none;
		      color: #464646;
		    }

		    a.blue-link {
		      color: blue;
		      text-decoration: underline;
		    }

		    /* ----- General ----- */

		    td.center {
		      text-align: center;
		    }

		    .left {
		      text-align: left;
		    }

		    .body-padding {
		      padding: 24px 40px 40px;
		    }

		    .border-bottom {
		      border-bottom: 1px solid #D8D8D8;
		    }

		    table.full-width-gmail-android {
		      width: 100% !important;
		    }


		    /* ----- Header ----- */
		    .header {
		      font-weight: bold;
		      font-size: 16px;
		      line-height: 16px;
		      height: 16px;
		      padding-top: 19px;
		      padding-bottom: 7px;
		    }

		    .header a {
		      color: #464646;
		      text-decoration: none;
		    }

		    /* ----- Footer ----- */

		    .footer a {
		      font-size: 12px;
		    }
		  </style>

		  <style type="text/css" media="only screen and (max-width: 650px)">
		    @media only screen and (max-width: 650px) {
		      * {
		        font-size: 16px !important;
		      }

		      table[class*="w320"] {
		        width: 320px !important;
		      }

		      td[class="mobile-center"],
		      div[class="mobile-center"] {
		        text-align: center !important;
		      }

		      td[class*="body-padding"] {
		        padding: 20px !important;
		      }

		      td[class="mobile"] {
		        text-align: right;
		        vertical-align: top;
		      }
		    }
		  </style>

		</head>
		<body style="padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
		 <td valign="top" align="left" width="100%" style="background:repeat-x url(https://www.filepicker.io/api/file/al80sTOMSEi5bKdmCgp2) #f9f8f8;">
		 <center>

		   <table class="w320 full-width-gmail-android" bgcolor="#f9f8f8" background="https://www.filepicker.io/api/file/al80sTOMSEi5bKdmCgp2" style="background-color:transparent" cellpadding="0" cellspacing="0" border="0" width="100%">
		      <tr>
		        <td width="100%" height="48" valign="top">
		              <table class="full-width-gmail-android" cellspacing="0" cellpadding="0" border="0" width="100%">
		                <tr>
		                  <td class="header center" width="100%">
		                    <a href="#">
		                      Spirit Booksfield
		                    </a>
		                  </td>
		                </tr>
		              </table>
		        </td>
		      </tr>
		    </table>
    ';

     	switch ($data_penerima['NotificationType']) {
     		case "payment":
     			$mail->Subject = "Notifikasi Pembayaran";
     		break;
     		case "payment_done":
     			$mail->Subject = "Notifikasi Pembayaran Selesai";
     		break;
     		case "password_reset":
     			$mail->Subject = "Reset Password Aplikasi";
     		break;
     		case "new_release":
     			$mail->Subject = "Buku Baru Release";
     		break;
     		case "notification":
     			$mail->Subject = "<important> ".$data_penerima['BaseRef']." Pemberitahuan Pembayaran Masuk <important>";
     		break;
     	}

     	if ($data_penerima['NotificationType'] == "notification") {
     		$mail->AddAddress($penerimaNotifikasi,"Admin");	
     	}
     	else{
     		$mail->AddAddress($data_penerima['ReceipedEmail'],"User");	
     	}

     	switch ($data_penerima['NotificationType']) {
     		case "payment":
     			$SQLpayment  = "SELECT a.*, b.userid FROM topuppayment a
						INNER JOIN thistoryrequest b on a.NoTransaksi = b.NoTransaksi 
						where a.NoTransaksi='".$data_penerima['BaseRef']."' ";
				$rsdetailPayment = mysqli_query($mysqli, $SQLpayment);
				$xdata = mysqli_fetch_assoc($rsdetailPayment);

				$tglOrder = "";
				$nominalOrder = "";
				$userid = "";

				if ($xdata) {
					$tglOrder = $xdata['TglTransaksi'];
					$nominalOrder = $xdata['GrossAmt'];
					$userid = $xdata['userid'];
				}
				// var_dump($tglOrder);
     			$body .= '
     				<table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff">
				      <tr>
				        <td align="center">
				          <center>
				            <table class="w320" cellspacing="0" cellpadding="0" width="500">
				              <tr>
				                <td class="body-padding mobile-padding">

				                <table cellpadding="0" cellspacing="0" width="100%">
				                  <tr>
				                    <td style="text-align:center; font-size:30px; padding-bottom:20px;">
				                      Reciept
				                    </td>
				                  </tr>
				                  <tr>
				                    <td style="padding-bottom:20px;">
				                      '.$userid.'<br>
				                      <br>
				                        Permintaan Top Up anda kami terima Silahkan Melakukan Pembayaran.
				                        Detail Informasi silahkan menuju Menu <b> Riwayat Transaksi </b>
				                      <br>
				                      <br>
				                    </td>
				                  </tr>
				                </table>

				                <table cellspacing="0" cellpadding="0" width="100%">
				                  <tr>
				                    <td class="left" style="padding-bottom:20px; text-align:left;">
				                      Date: 12/30/2013<br>
				                      <b>Order Number:</b> '.$data_penerima["BaseRef"].'
				                    </td>
				                  </tr>
				                </table>

				                <table cellspacing="0" cellpadding="0" width="100%">
				                  <tr>
				                    <td>
				                      <b>Deskripsi</b>
				                    </td>
				                    <td>
				                      <b>Jumlah</b>
				                    </td>
				                  </tr>
				                  <tr>
				                    <td class="border-bottom" height="5"></td>
				                    <td class="border-bottom" height="5"></td>
				                  </tr>
				                  <tr>
				                    <td style="padding-top:5px; vertical-align:top;">
				                      Top Up SpiritPay
				                    </td>
				                    <td style="padding-top:5px;" class="mobile">
				                      Rp. '.number_format($nominalOrder).'
				                    </td>
				                  </tr>
				                </table>
				                <br>
				                <table cellspacing="0" cellpadding="0" width="100%">
				                  <tr>
				                    <td class="left" style="text-align:left;">
				                      Thanks so much,
				                    </td>
				                  </tr>
				                </table>
				                </td>
				              </tr>
				            </table>
				          </center>
				        </td>
				      </tr>
				    </table>

				    <table class="w320" bgcolor="#E5E5E5" cellpadding="0" cellspacing="0" border="0" width="100%">
				      <tr>
				        <td style="border-top:1px solid #B3B3B3;" align="center">
				          <center>
				            <table class="w320" cellspacing="0" cellpadding="0" width="500" bgcolor="#E5E5E5">
				              <tr>
				                <td>
				                  <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#E5E5E5">
				                    <tr>
				                      <td class="center" style="padding:25px; text-align:center;">
				                        <b><a href="#">Get in touch</a></b> if you have any questions or feedback
				                      </td>
				                    </tr>
				                  </table>
				                </td>
				              </tr>
				            </table>
				          </center>
				        </td>
				      </tr>
				      <tr>
				        <td style="border-top:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3;" align="center">
				          <center>
				            <table class="w320" cellspacing="0" cellpadding="0" width="500" bgcolor="#E5E5E5">
				              <tr>
				                <td align="center" style="padding:25px; text-align:center">
				                  <table cellspacing="0" cellpadding="0" width="100%" bgcolor="#E5E5E5">
				                    <tr>
				                      <td class="center footer" style="font-size:12px;">
				                        <a href="#">Contact Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                        <span class="footer-group">
				                          <a href="#">Facebook</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                          <a href="#">Twitter</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                          <a href="#">Support</a>
				                        </span>
				                      </td>
				                    </tr>
				                  </table>
				                </td>
				              </tr>
				            </table>
				          </center>
				        </td>
				      </tr>
				    </table>

				  </center>
				  </td>
				</tr>
				</table>
				</body>
				</html>
     			';
     		break;
     		case "payment_done":
     			$SQLpayment  = "SELECT a.*, b.userid FROM topuppayment a
						INNER JOIN thistoryrequest b on a.NoTransaksi = b.NoTransaksi 
						where a.NoTransaksi='".$data_penerima['BaseRef']."' ";
				$rsdetailPayment = mysqli_query($mysqli, $SQLpayment);
				$xdata = mysqli_fetch_assoc($rsdetailPayment);

				$tglOrder = "";
				$nominalOrder = "";
				$userid = "";

				if ($xdata) {
					$tglOrder = $xdata['TglTransaksi'];
					$nominalOrder = $xdata['GrossAmt'];
					$userid = $xdata['userid'];
				}
				// var_dump($tglOrder);
     			$body .= '
     				<table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff">
				      <tr>
				        <td align="center">
				          <center>
				            <table class="w320" cellspacing="0" cellpadding="0" width="500">
				              <tr>
				                <td class="body-padding mobile-padding">

				                <table cellpadding="0" cellspacing="0" width="100%">
				                  <tr>
				                    <td style="text-align:center; font-size:30px; padding-bottom:20px;">
				                      Reciept
				                    </td>
				                  </tr>
				                  <tr>
				                    <td style="padding-bottom:20px;">
				                      '.$userid.'<br>
				                      <br>
				                        Pembayran Anda sudah kami terima, Saldo SpiritPay anda bertambah sebesar <b> Rp. '.number_format($nominalOrder).' </b>
				                      <br>
				                      <br>
				                    </td>
				                  </tr>
				                </table>

				                <table cellspacing="0" cellpadding="0" width="100%">
				                  <tr>
				                    <td class="left" style="padding-bottom:20px; text-align:left;">
				                      Date: 12/30/2013<br>
				                      <b>Order Number:</b> '.$data_penerima["BaseRef"].'
				                    </td>
				                  </tr>
				                </table>
				                <br>
				                <table cellspacing="0" cellpadding="0" width="100%">
				                  <tr>
				                    <td class="left" style="text-align:left;">
				                      Thanks so much,
				                    </td>
				                  </tr>
				                </table>
				                </td>
				              </tr>
				            </table>
				          </center>
				        </td>
				      </tr>
				    </table>

				    <table class="w320" bgcolor="#E5E5E5" cellpadding="0" cellspacing="0" border="0" width="100%">
				      <tr>
				        <td style="border-top:1px solid #B3B3B3;" align="center">
				          <center>
				            <table class="w320" cellspacing="0" cellpadding="0" width="500" bgcolor="#E5E5E5">
				              <tr>
				                <td>
				                  <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#E5E5E5">
				                    <tr>
				                      <td class="center" style="padding:25px; text-align:center;">
				                        <b><a href="#">Get in touch</a></b> if you have any questions or feedback
				                      </td>
				                    </tr>
				                  </table>
				                </td>
				              </tr>
				            </table>
				          </center>
				        </td>
				      </tr>
				      <tr>
				        <td style="border-top:1px solid #B3B3B3; border-bottom:1px solid #B3B3B3;" align="center">
				          <center>
				            <table class="w320" cellspacing="0" cellpadding="0" width="500" bgcolor="#E5E5E5">
				              <tr>
				                <td align="center" style="padding:25px; text-align:center">
				                  <table cellspacing="0" cellpadding="0" width="100%" bgcolor="#E5E5E5">
				                    <tr>
				                      <td class="center footer" style="font-size:12px;">
				                        <a href="#">Contact Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                        <span class="footer-group">
				                          <a href="#">Facebook</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                          <a href="#">Twitter</a>&nbsp;&nbsp;|&nbsp;&nbsp;
				                          <a href="#">Support</a>
				                        </span>
				                      </td>
				                    </tr>
				                  </table>
				                </td>
				              </tr>
				            </table>
				          </center>
				        </td>
				      </tr>
				    </table>

				  </center>
				  </td>
				</tr>
				</table>
				</body>
				</html>
     			';
     		break;
     		case "password_reset":
     			$SQLuser = "SELECT * FROM users where username = '".$data_penerima['BaseRef']."'";

     			$body .= '
	        	<h3><center><b>Spirit Booksfield</b></center></h3><br>
	            <p>
	            Berikut detaik akun anda di <a href="https://renungan-spirit.com/">renungan-spirit.com</a><br>
	            <b>Jangan berikan email ini ke siapapun termasuk staff dari pengelola aplikasi</b>
	            <br>
	            </p>
	            <pre>
	            	Email 		: '.$param.' <br>
	            	Username 	: '.$username.'<br>
	            	Password 	: '.substr($password, 0,6).'
	            <p>
	            <br>
	            Best Regards<br><br>
	            <a href="renungan-spirit.com">renungan-spirit.com</a>
	            </p>
	            </body>
				</html>
	        ';
     		break;
     		case "new_release":
     			
     		break;
     		case "notification":
     			$body .= '
	        	<h3><center><b>Spirit Booksfield</b></center></h3><br>
	            <p>
	            Ada Pembayaran Baru Masuk, Silahkan cek di : <a href="https://renungan-spirit.com/">renungan-spirit.com</a><br>
	            <b>dan Segera lakukan konfirmasi</b>
	            <br>
	            </p>
	            <p>
	            <br>
	            Best Regards<br><br>
	            <a href="renungan-spirit.com">renungan-spirit.com</a>
	            </p>
	            </body>
				</html>';
     		break;

     	}

     	// $mail->MsgHTML($body);
     	$mail->Body = $body;
     	if ($mail->Send()) {
     		$SQL = "UPDATE tpushemail set `Status` = 1 where id =".$data_penerima['id'];
     		mysqli_query($mysqli, $SQL);
     	}
     	else{
     		echo "Failed to sending message";
     	}
		// if($mail->Send()) echo "Message has been sent";
		// else echo "Failed to sending message";

     }
?>