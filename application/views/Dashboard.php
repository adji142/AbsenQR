<?php
    require_once(APPPATH."views/parts/Header.php");
    require_once(APPPATH."views/parts/Sidebar.php");
    $active = 'dashboard';
?>
<!-- page content -->
<div class="right_col" role="main">
  <div class="row">
    <div class="col-md-12">
      <div class="">
        <div class="x_content">
          <div class="row">

            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
              <div class="tile-stats">
                <div class="icon"><i class="fa fa-caret-square-o-right"></i>
                </div>
                <div class="count">179</div>

                <h3>Manual Payment</h3>
                <p>Payment Not Yet Confirmed.</p>
              </div>
            </div>

            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
              <div class="tile-stats">
                <div class="icon"><i class="fa fa-caret-square-o-right"></i>
                </div>
                <div class="count">179</div>

                <h3>Auto Payment</h3>
                <p>Incoming Auto Payment.</p>
              </div>
            </div>

            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
              <div class="tile-stats">
                <div class="icon"><i class="fa fa-caret-square-o-right"></i>
                </div>
                <div class="count">179</div>

                <h3>New Message</h3>
                <p>New Message From User.</p>
              </div>
            </div>

            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6  ">
              <div class="tile-stats">
                <div class="icon"><i class="fa fa-caret-square-o-right"></i>
                </div>
                <div class="count">179</div>

                <h3>New Transaction</h3>
                <p>New Transaction Today.</p>
              </div>
            </div>

            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->
<?php
  require_once(APPPATH."views/parts/Footer.php");
?>

<!-- <script type="text/javascript">
  $(function () {
    $(document).ready(function () {
      setInterval(function () {
        $.ajax({
          type: "post",
          url: "<?=base_url()?>C_Transaksi/getSaldoPerAccount",
          data: {'UserID':''},
          dataType: "json",
          xhrFields: {
            onprogress: function(e){
              var response = e.currentTarget.response;
              console.log(response);
            }
          }
        });
      },10000);
    });
  });
</script> -->