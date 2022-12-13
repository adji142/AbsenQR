<?php
    require_once(APPPATH."views/parts/Header.php");
    require_once(APPPATH."views/parts/Sidebar.php");
    $active = 'dashboard';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.card {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
  max-width: 300px;
  margin: auto;
  text-align: center;
  font-family: arial;
}

.title {
  color: grey;
  font-size: 18px;
}

button {
  border: none;
  outline: 0;
  display: inline-block;
  padding: 8px;
  color: white;
  background-color: #000;
  text-align: center;
  cursor: pointer;
  width: 100%;
  font-size: 18px;
}

a {
  text-decoration: none;
  font-size: 22px;
  color: black;
}

button:hover, a:hover {
  opacity: 0.7;
}
</style>

<!-- page content -->
  <div class="right_col" role="main">
    <div class="">

      <div class="clearfix"></div>

      <div class="row">

        <?php 
          $SQL = "
          SELECT a.*, b.NamaJabtan, c.NamaDepartement,
          CASE WHEN a.Active = 1 THEN 'Aktif' ELSE 'InAktif' END StatusKaryawan
          FROM masterkaryawan a
          LEFT JOIN tjabatan b on a.KodeJabatan = b.KodeJabatan
          LEFT JOIN tdepartement c on KodeDepartement = c.KodeDepartment
          where Active = 1
        ";

        $rs = $this->db->query($SQL)->result();

        // var_dump($rs);

        $html = '';
        foreach ($rs as $key) {
          $html .= '<div class="col-md-4 col-sm-4  "> ';
          $html .= '    <div class="card">';

          $html .= '      <img src="'.$key->ImageBase64.'" style="height:300px; ">';
          $html .= '      <h2>'.$key->NamaKaryawan.'</h2>';
          $html .= '      <p class="title">'.$key->NamaJabtan.' ,'.$key->NamaDepartement.'</p>';
          $html .= '      <p>'.$key->Nik.'</p>';
          $html .= '      <div style="margin: 12px 0;">';
          $html .= '      <center><img src="https://chart.apis.google.com/chart?cht=qr&chs=200x200&chld=L|0&chl='.$key->Nik.'" style="width:50%"></center>';
          $html .= '      </div>';
          $html .= '    </div>';
          $html .= '</div> ';
        }

        echo $html;
        ?>

        <!-- <div class="col-md-4 col-sm-4  ">
          <div class="card">
            <img src="https://pict.sindonews.net/dyn/620/pena/news/2022/01/13/39/655579/harga-fotofoto-ghozali-bikin-melongo-ada-yang-laku-rp42-miliiar-fwx.jpg" alt="John" style="width:100%">
            <h1>John Doe</h1>
            <p class="title">CEO & Founder, Example</p>
            <p>Harvard University</p>
            <div style="margin: 24px 0;">
              <a href="#"><i class="fa fa-dribbble"></i></a> 
              <a href="#"><i class="fa fa-twitter"></i></a>  
              <a href="#"><i class="fa fa-linkedin"></i></a>  
              <a href="#"><i class="fa fa-facebook"></i></a> 
            </div>
            <p><button>Contact</button></p>
          </div>
        </div> -->

        
      </div>
    </div>
  </div>
  <!-- /page content -->
<?php
  require_once(APPPATH."views/parts/Footer.php");
?>