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

        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" id="modal_">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Gambar Absen</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="item form-group">
                  <div class="col-md-12">
                    <center>
                      <h4 class="modal-title" id="myModalLabel">Check in</h4>
                      <!-- <video id="preview" width="50%"></video> -->
                      <img src="" width="50%" id="imgCheckIn">
                    </center>
                  </div>
                </div>

                <div class="item form-group">
                  <div class="col-md-12">
                    <center>
                      <h4 class="modal-title" id="myModalLabel">Check Out</h4>
                      <!-- <video id="preview" width="50%"></video> -->
                      <img src="" width="50%" id="imgCheckOut">
                    </center>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                
              </div> -->

            </div>
          </div>
        </div>
<?php
  require_once(APPPATH."views/parts/Footer.php");
?>
<script type="text/javascript">
  $(function () {
    $(document).ready(function () {
      var today = new Date();

      $('#TglAwal').val(today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + '01');
      $('#TglAkhir').val(today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2))
      
      console.log(today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + '01')
      var where_field = '';
      var where_value = '';
      var table = 'users';

      $.ajax({
        type: "post",
        url: "<?=base_url()?>C_AttLog/Read",
        data: {'TglAwal':$('#TglAwal').val(),'TglAkhir':$('#TglAkhir').val()},
        dataType: "json",
        success: function (response) {
          bindGrid(response.data);
        }
      });
    });
    $('.close').click(function() {
      location.reload();
    });
    $('#btProcess').click(function () {
      $.ajax({
        type: "post",
        url: "<?=base_url()?>C_AttLog/Read",
        data: {'TglAwal':$('#TglAwal').val(),'TglAkhir':$('#TglAkhir').val()},
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
            editing: {
                mode: "row",
                allowAdding:false,
                allowUpdating: false,
                allowDeleting: false,
                texts: {
                    confirmDeleteMessage: ''  
                }
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
            columns: [
                {
                    dataField: "NikKaryawan",
                    caption: "NIK",
                    allowEditing:false
                },
                {
                    dataField: "NamaKaryawan",
                    caption: "Nama Karywan",
                    allowEditing:false
                },
                {
                    dataField: "Jabatan",
                    caption: "Jabatan",
                    allowEditing:false
                },
                {
                    dataField: "Departement",
                    caption: "Departement",
                    allowEditing:false
                },
                {
                    dataField: "TglAbsen",
                    caption: "Tanggal",
                    allowEditing:false
                },
                {
                    dataField: "CheckIn",
                    caption: "CheckIn",
                    allowEditing:false
                },
                {
                    dataField: "CheckOut",
                    caption: "CheckOut",
                    allowEditing:false
                },
                {
                    dataField: "ImageBase64",
                    caption: "ImageBase64",
                    allowEditing:false,
                    visible: false
                },
                {
                    dataField: "FileItem",
                    caption: "Link",
                    allowEditing:false,
                    cellTemplate: function(cellElement, cellInfo) {
                      LinkAccess = "<button class='btn btn-success' onclick=LoadImage('"+cellInfo.data.NikKaryawan+"','"+cellInfo.data.TglAbsen+"') >View Image</button>";
                      console.log();
                      cellElement.append(LinkAccess);
                  }
                },
                // {
                //     dataField: "Nilai",
                //     caption: "Nilai",
                //     allowEditing:false
                // },
            ],
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
            onRowRemoving: function(e) {
              KodeDepartment = e.data.KodeDepartment;
              Swal.fire({
                title: 'Apakah anda yakin?',
                text: "anda akan menghapus data di baris ini !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
                if (result.value) {

                  $.ajax({
                      type    :'post',
                      url     : '<?=base_url()?>C_Departement/CRUD',
                      data    : {'KodeDepartment':KodeDepartment,'formtype':'delete'},
                      dataType: 'json',
                      success : function (response) {
                        if(response.success == true){
                          Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                      ).then((result)=>{
                            location.reload();
                          });
                        }
                        else{
                          Swal.fire({
                            type: 'error',
                            title: 'Woops...',
                            text: response.message,
                            // footer: '<a href>Why do I have this issue?</a>'
                          }).then((result)=>{
                            location.reload();
                          });
                        }
                      }
                    });
                  
                }
                else{
                  location.reload();
                }
              })
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