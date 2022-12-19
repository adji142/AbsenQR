<?php
    require_once(APPPATH."views/parts/Header.php");
    require_once(APPPATH."views/parts/Sidebar.php");
    $active = 'dashboard';
?>
<!-- page content -->
<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12 col-sm-12  ">
        <div class="x_panel">
          <div class="x_title">
            <h2>Attandance Log</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="col-md-2">
              <label class="col-form-label" for="first-name">Tanggal Awal</label>
              <input type="date" name="TglAwal" id="TglAwal" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="col-form-label" for="first-name">Tanggal Akhir</label>
              <input type="date" name="TglAkhir" id="TglAkhir" class="form-control">
            </div>
            <div class="col-md-2">
              <br><br>
              <button class="rounded btn btn-success" id="btProcess">Proses</button>
            </div>
            <div class="col-md-12">
              <div class="dx-viewport demo-container">
                <div id="data-grid-demo">
                  <div id="gridContainer">
                  </div>
                </div>
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
<script type="text/javascript">
  $(function () {
    $(document).ready(function () {
      var today = new Date();

      $('#TglAwal').val(today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + '01');
      $('#TglAkhir').val(today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2))
      
      var datasource = 'rsp_rekap_absensi';
      var parameter = "'"+$('#TglAwal').val()+"','"+$('#TglAkhir').val()+"'";

      $.ajax({
        type: "post",
        url: "<?=base_url()?>C_Laporan/Read",
        data: {'DataSource':datasource,'Parameter':parameter},
        dataType: "json",
        success: function (response) {
          console.log(response.data);
          bindGrid(response.data);
        }
      });
    });
    $('.close').click(function() {
      location.reload();
    });
    $('#btProcess').click(function () {
      var datasource = 'rsp_rekap_absensi';
      var parameter = "'"+$('#TglAwal').val()+"','"+$('#TglAkhir').val()+"'";
      
      $.ajax({
        type: "post",
        url: "<?=base_url()?>C_Laporan/Read",
        data: {'DataSource':datasource,'Parameter':parameter},
        dataType: "json",
        success: function (response) {
          bindGrid(response.data);
        }
      });
    })
    function bindGrid(data) {

      $("#gridContainer").dxDataGrid({
        allowColumnResizing: true,
            dataSource: data,
            keyExpr: "NikKaryawan",
            showBorders: true,
            allowColumnReordering: true,
            allowColumnResizing: true,
            columnAutoWidth: true,
            showBorders: true,
            paging: {
                enabled: false
            },
            searchPanel: {
                visible: true,
                width: 240,
                placeholder: "Search..."
            },
            export: {
                enabled: true,
                fileName: "Attandance Log"
            },
            onEditingStart: function(e) {
                GetData(e.data.KodeDepartment);
            },
            onInitNewRow: function(e) {
                // logEvent("InitNewRow");
                $('#modal_').modal('show');
            },
            onRowInserting: function(e) {
                // logEvent("RowInserting");
            },
            onRowInserted: function(e) {
                // logEvent("RowInserted");
                // alert('');
                // console.log(e.data.onhand);
                // var index = e.row.rowIndex;
            },
            onRowUpdating: function(e) {
                // logEvent("RowUpdating");
                
            },
            onRowUpdated: function(e) {
                // logEvent(e);
            },
            onRowRemoved: function(e) {
              // console.log(e);
            },
          onEditorPrepared: function (e) {
            // console.log(e);
          }
        });

        // add dx-toolbar-after
        // $('.dx-toolbar-after').append('Tambah Alat untuk di pinjam ');
    }

  });

  function LoadImage(Nik,TglLog) {
    $.ajax({
      type: "post",
      url: "<?=base_url()?>C_AttLog/getImageAttLog",
      data: {'Nik':Nik,'TglLog':TglLog},
      dataType: "json",
      success: function (response) {
        // bindGrid(response.data);
        console.log(response);
        if (response.success == true) {
          $.each(response.data,function (k,v) {
            if (v.LogType == 1) {
              $('#imgCheckIn').attr('src',v.ImageBase64)
            }
            else if (v.LogType == 0) {
              $('#imgCheckOut').attr('src',v.ImageBase64)
            }
          });

          $('#modal_').modal('show');
        }
      }
    });
  }
</script>