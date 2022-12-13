<?php
//buka koneksi ke engine MySQL
    $Open = mysqli_connect("localhost","root","hsp123","xlpfk_solo");
    // $Open = mysqli_connect("localhost","aistrick_root","lagis3nt0s4","aistrick_xlpfk_solo");
    // mysqli_connect("localhost","root","hsp123","xlpfk_solo");
    // $Open = mysqli_connect("localhost","root","lagis3nt0s4","dealsys");
    if (!$Open){
        die ("Koneksi ke Engine MySQL Gagal !<br>");
    }
    $id = $_GET['id'];
    $sql = "
      SELECT 
      a.notransaksi,
      a.tgltransaksi,
      a.penerimabarang,
      c.kode_alat,
      c.nama_alat,
      c.merk,
      d.namapeminjam,
      CASE WHEN d.statustransaksi = 0 then 'Belum Lengkap' ELSE 'Lengkap' END sts,
      SUM(b.jumlahkembali) jumlah
    FROM pengembalian a
    LEFT JOIN pengembaliandetail b on a.notransaksi = b.headerid
    LEFT JOIN peminjaman d on a.nopinjam = d.notransaksi
    LEFT JOIN masteralat c on b.kodealat = c.id
    WHERE a.notransaksi = '$id' AND b.jumlahkembali <> 0
    GROUP BY 
    a.notransaksi,
    a.tgltransaksi,
    a.penerimabarang,
    b.kodealat,
    c.merk,
    d.namapeminjam
    ";
    // var_dump($sql);
    $result = mysqli_query($Open,$sql);
    $row_assoc = mysqli_fetch_assoc($result);// assoc 

    // get value

    $notransaksi = $row_assoc['notransaksi'];
    $tgltransaksi = $row_assoc['tgltransaksi'];
    $namapeminjam = $row_assoc['namapeminjam'];
    $penerimabarang = $row_assoc['penerimabarang'];
    $sts = $row_assoc['sts'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>BUKTI PENGEMBALIAN ALAT KESEHATAN - NAMA INSTANSI</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<!-- onload="window.print();" -->
<body >
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <i><img src="../Assets/img/logo_2.png" width="10%"></i> BUKTI PENGEMBALIAN ALAT KESEHATAN - NAMA INSTANSI.
          <small class="pull-right"><?php echo date('d-M-Y'); ?></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-4 invoice-col">
        To
        <address>
          <strong>Nama Instansi</strong><br>
          Alamat Line 1<br>
          Lanjut Alamat Line 2<br>
          Phone: Nomer Tlp<br>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        From
        <address>
        <?php
          echo " Peminjam :<strong>".$namapeminjam."</strong><br>";
          ?>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        <?php
        echo "
        <b>Transaction #".$notransaksi."</b><br>
        <b>Transaction Date:</b> ".$tgltransaksi."<br>
        <b>PIC:</b> ".$namapeminjam."<br>
        <b>Penerima :".$penerimabarang;

        ?>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>No</th>
            <th>Kode Alat</th>
            <th>Nama Alat</th>
            <th>Merk</th>
            <th>Jumlah Kembali</th>
          </tr>
          </thead>
          <tbody>
          <?php
          $i = 1;
            $result_x = mysqli_query($Open,$sql);

            while ($rsx = mysqli_fetch_array($result_x)) {
              $kodemesin = stripslashes ($rsx['kode_alat']);
              $nama_alat = stripslashes ($rsx['nama_alat']);
              $merk = stripslashes ($rsx['merk']);
              $jumlah = stripslashes ($rsx['jumlah']);
              echo "
                <tr>
                  <td>".$i."</td>
                  <td>".$kodemesin."</td>
                  <td>".$nama_alat."</td>
                  <td>".$merk."</td>
                  <td>".$jumlah."</td>
                </tr>
              ";
              $i++;
            }
          ?>
          </tbody>
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
      <!-- accepted payments column -->
      <div class="col-xs-6">
        <p class="lead"><b><?php echo $sts; ?> </b></p>
        <p class="lead">Term & Condition:</p>

        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
          Keterangan disini
        </p>
      </div>
      <!-- /.col -->
      <!-- <div class="col-xs-6">
        <p class="lead">Amount Due <?php echo $now;?></p>

        <div class="table-responsive">
          <table class="table">
            <tr>
              <th style="width:50%">Subtotal:</th>
              <td><?php echo $hrgotr; ?></td>
            </tr>
            <tr>
              <th>Tax (10%)</th>
              <td>Included</td>
            </tr>
            <tr>
              <th>Shipping:</th>
              <td>Free</td>
            </tr>
            <tr>
              <th>Total:</th>
              <td><?php echo $hrgotr; ?></td>
            </tr>
          </table>
        </div>
      </div> -->
      <!-- /.col -->
    </div>

    <div class="row">
      <div class="col-xs-12">
        <table class="table table-striped">
          <thead>
          <tr>
            <td align="center">Admin</td>
            <td align="center">Peminjam</td>
          </tr>
          </thead>
          <tr>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td align="center">(....................)</td>
            <td align="center">(....................)</td>
          </tr>
        </table>
      </div>
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
