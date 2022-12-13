<?php
//buka koneksi ke engine MySQL
    // $Open = mysqli_connect("localhost","root","hsp123","dbpos");
    $Open = mysqli_connect("localhost","dawn6835_root","lagis3nt0s4","dawn6835_dbpos");
    // mysqli_connect("localhost","root","hsp123","xlpfk_solo");
    // $Open = mysqli_connect("localhost","root","lagis3nt0s4","dealsys");
    if (!$Open){
        die ("Koneksi ke Engine MySQL Gagal !<br>");
    }
    $NoAwal = $_GET['NoAwal'];
    $NoAkhir = $_GET['NoAkhir'];

    // $NoAwal = substr($NoAwal, 6);
    // $NoAkhir = substr($NoAkhir, 6);

    $sql = "
      SELECT * FROM vw_penjualanalamat where NoTransaksi BETWEEN '".$NoAwal."' AND '".$NoAkhir."' 
      AND PaymentTerm <> 1
    ";
    
    $result = mysqli_query($Open,$sql);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="//code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="{{URL::to('/assets/lte/css/AdminLTE.css')}}" rel="stylesheet">
        <style>
            .pagebreak{page-break-after: always; page-break-before: always;}
        </style>
</head>
<script type="text/javascript">
  window.print();
</script>
<body >
<div class="wrapper">
  <!-- Main content -->
  <section class="content invoice pagebreak">
    <!-- title row -->
    <?php while($row = mysqli_fetch_assoc($result)) : ?>
      <div class="row">
        <div class="col-xs-12" style="border: 1px solid green;">
          <table>
            <tr>
              <td width="30%"><h5>Nama</h5></td>
              <td><h5>:</h5></td>
              <td><h5><?= $row['Nama_dest']?></h5></td>
            </tr>
            <tr>
              <td width="30%"><h5>Alamat</h5></td>
              <td><h5>:</h5></td>
              <td><h5><?= $row['Alamat_dest'].', '.$row['Kota'].', Provinsi '.$row['provinsi'].', Kelurahan '.$row['kelurahan'].' Kecamatan'.$row['Kecamatan'].' ,'.$row['KodePos_dest']?></h5></td>
            </tr>
            <tr>
              <td width="30%"><h5>No. Tlp</h5></td>
              <td><h5>:</h5></td>
              <td><h5><?= $row['Notlp_dest']?></h5></td>
            </tr>
            <tr>
              <td width="30%"><h5>Expedisi</h5></td>
              <td><h5>:</h5></td>
              <td><h5><?= $row['NamaExpdc'].' - '.explode('|', $row['Servicexpdc'])[0] . ' Ongkir : '.$row['T_Ongkir']. ' / No. Resi :'.$row['NoResi'] ?></h5></td>
            </tr>
            <tr>
              <td width="30%"><h5>Dari</h5></td>
              <td><h5>:</h5></td>
              <td><h5><?= $row['Nama_ori']?></h5></td>
            </tr>
            <tr>
              <td width="30%"><h5>NoTlp</h5></td>
              <td><h5>:</h5></td>
              <td><h5><?= $row['Notlp_Ori']?></h5></td>
            </tr>
            <tr>
              <td width="30%"><b><h5>Total</h5></b></td>
              <td><h5>:</h5></td>
              <td><h5><b><?='('.$row['Qty'].' pcs)' ?></b></h5></td>
            </tr>
          </table>
          <center>
            <h4 style="color: red;">
              JANGAN TERIMA PAKET INI JIKA ADA KERUSAKAN & JANGAN LUPA VIDEO UNBOXING PAKET INI JIKA SUDAH ANDA TERIMA.
              TERIMAKASIH SEHAT SELALU LANCAR REJEKINYA.
            </h4>
          </center>
        </div>
      </div>
      <!-- <br> -->
    <?php endwhile; ?>
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
