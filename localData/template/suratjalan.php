<?php 
  $NoTransaksi = $_GET['NoTransaksi'];

  $servername = "localhost";
  $username   = "comk5683_root";
  $password   = "lagis3nt0s4";
  $dbname   = "comk5683_estock";

  $cndb = mysqli_connect($servername, $username, $password, $dbname);
  if (!$cndb) {
    die("Database Connection Failed");
  }

  $sql = "SELECT a.*, b.NamaCustomer,b.Email,b.ContactPerson,b.NoTlp FROM suratjalanheader a
            LEFT JOIN masterpelanggan b on a.KodeCustomer = b.KodeCustomer 
            WHERE a.NoTransaksi = '$NoTransaksi'
          ";
  $dt = mysqli_query($cndb, $sql);
  // var_dump($sql);
  $Header = mysqli_fetch_assoc($dt);

  $TglTrx=date_create($Header["TglTransaksi"]);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo $NoTransaksi ?></title>
    <link rel="stylesheet" href="style.css" media="all" />
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="logo.png">
      </div>
      <h1>SURAT JALAN <?php echo $NoTransaksi ?></h1>
      <div id="company" class="clearfix">
        <div>Nama Perusahaan</div>
        <div>AIS System,<br /> Surakarta, INA</div>
        <div>(+62) 81325058258</div>
        <div><a href="mailto:prasetyoajiw@gmail.com">prasetyoajiw@gmail.com</a></div>
      </div>
      <div id="project">
        <div><span>Customer</span> <?php echo $Header["KodeCustomer"].' - '.$Header["NamaCustomer"]; ?></div>
        <div><span>Alamat</span> <?php echo $Header["AlamatKirim"] ?> , ID</div>
        <div><span>CP</span> <?php echo $Header["ContactPerson"].' / '.$Header["NoTlp"].' - '.$Header["Email"] ?></div>
        <div><span>DATE</span> <?php echo date_format($TglTrx,"d/m/Y"); ?></div>
        <div><span>XPDC</span> <?php echo $Header["NamaExpedisi"].' - '. $Header["NoPol"] ?></div>
      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <th class="service">SERVICE</th>
            <th class="desc">DESCRIPTION</th>
            <th>QTY</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $sql = "SELECT * FROM suratjalandetail a
                      WHERE a.NoTransaksi = '$NoTransaksi' ORDER BY RowID
                    ";
            $dt = mysqli_query($cndb, $sql);
            $SubTotal = 0;
            $TotalDisc = 0;
            while($row = mysqli_fetch_assoc($dt)) {
              echo '
                <tr>
                  <td class="service">'.$row["KodeItem"].'</td>
                  <td class="desc">'.$row["NamaItem"].'</td>
                  <td class="unit">'.number_format($row["Qty"]).'</td>
                </tr>
              ';
            }
          ?>
          <!-- <tr>
            <td colspan="5">SUBTOTAL</td>
            <td class="total"><?php echo number_format($SubTotal); ?></td>
          </tr>
          <tr>
            <td colspan="5">TOTAL DISC</td>
            <td class="total"><?php echo number_format($TotalDisc); ?></td>
          </tr>
          <tr>
            <td colspan="5" class="grand total">GRAND TOTAL</td>
            <td class="grand total"><?php echo number_format($SubTotal - $TotalDisc) ?></td>
          </tr> -->
        </tbody>
      </table>
      <!-- <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div> -->
    </main>
    <!-- <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer> -->
  </body>
</html>