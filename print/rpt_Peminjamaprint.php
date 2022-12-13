
<?php
//buka koneksi ke engine MySQL
    $Open = mysqli_connect("localhost","root","lagis3nt0s4","xlpfk_solo");
    //mysqli_connect("localhost","root","hsp123","dealsys");
    // $Open = mysqli_connect("localhost","root","lagis3nt0s4","dealsys");
    if (!$Open){
        die ("Koneksi ke Engine MySQL Gagal !<br>");
    }
    $id = $_GET['id'];
    $sql = "
      SELECT 
      a.notransaksi,
      a.tgltransaksi,
      a.namapeminjam,
      a.noktp,
      d.kodefasyankes,
      d.namafasyankes,
      d.alamat,
      d.email,
      d.nomertlf,
      b.kodemesin,
      c.nama_alat,
      c.merk,
      SUM(b.jumlah) jumlah
    FROM peminjaman a
    LEFT JOIN peminjamandetail b on a.notransaksi = b.headerid
    LEFT JOIN masteralat c on b.kodemesin = c.kode_alat
    LEFT JOIN masterfasyankes d on a.kodefasyankes = d.id
    WHERE a.notransaksi = '$id'
    GROUP BY 
    a.notransaksi,
    a.tgltransaksi,
    a.namapeminjam,
    a.noktp,
    d.kodefasyankes,
    d.namafasyankes,
    d.alamat,
    d.email,
    d.nomertlf,
    b.kodemesin,c.merk
    ";
    // var_dump($sql);
    $result = mysqli_query($Open,$sql) or die(mysql_error());
    $row_assoc = mysqli_fetch_assoc($result);// assoc 

    $row_array = mysqli_fetch_array($result);//array


    // get value

    $notransaksi = $row_assoc['notransaksi'];
    $tgltransaksi = $row_assoc['tgltransaksi'];
    $namapeminjam = $row_assoc['namapeminjam'];
    $noktp = $row_assoc['noktp'];
    $nama_fasyankes = $row_assoc['kodefasyankes'].' - '.$row_assoc['namafasyankes'];
    $alamat = $row_assoc['alamat'];
    $contact = 'Email: '.$row_assoc['email'].', Phone: '.$row_assoc['nomertlf'];;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Nama Instansi | Bukti Peminjaman Alat Kesehatan</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url();?>Assets/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>Assets/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url();?>Assets/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href=".<?php echo base_url();?>Assets/dist/css/AdminLTE.min.css">

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
<body>
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-xs-12">
        <h2 class="page-header">
          <i class="fa fa-globe"></i> BUKTI PEMINJAMAN ALAT KESEHATAN - NAMA INSTANSI
          <small class="pull-right"><?php echo date('dd-M-yyyy'); ?></small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
      <div class="col-sm-4 invoice-col">
        From
        <address>
          <strong>Nama Instansi</strong><br>
          Alamat Line 1<br>
          Lanjut Alamat Line 2<br>
          Phone: Nomer Tlp<br>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        To
        <address>
        <?php
          echo "<strong>".$nama_fasyankes."</strong><br>
          ".$alamat."<br>
          ".$contact."<br>
          ";
          ?>
        </address>
      </div>
      <!-- /.col -->
      <div class="col-sm-4 invoice-col">
        <?php
        echo "<h2><b> ASLI </b></h2><br>
        <b>Transaction #".$notransaksi."</b><br>
        <b>Transaction Date:</b> ".$tgltransaksi."<br>
        <b>PIC:</b> ".$namapeminjam." - ".$noktp."<br>";

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
            <th>Jumlah</th>
          </tr>
          </thead>
          <tbody>
          <?php
          $i = 1;
            while ($rsx = $row_array) {
              $kodemesin = stripslashes ($rsx['kodemesin']);
              $nama_alat = stripslashes ($rsx['nama_alat']);
              $merk = stripslashes ($rsx['merk']);
              $jumlah = stripslashes ($rsx['jumlah']);
              echo "
                <tr>
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
