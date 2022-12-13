<?php
    require_once(APPPATH."views/parts/Header.php");
    require_once(APPPATH."views/parts/Sidebar.php");
    $active = 'dashboard';
?>
<style type="text/css">
  .select2-container {
  width: 50% !important;
  }
</style>
<!-- page content -->
        <div class="right_col" role="main">
          <div class="">

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>User</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="col-md-12 col-sm-12  form-group">
                      <form class="form-horizontal form-label-left">
                        <div class="form-group row">
                          <label class="control-label col-md-1 col-sm-1 ">Roles</label>
                            <div class="col-md-4 col-sm-4 ">
                              <select class="form-control" id="fil_roles" name="fil_roles">
                                <option value="">Semua Role</option>
                                <?php
                                  $rs = $this->db->query("select * from roles")->result();
                                  foreach ($rs as $key) {
                                    echo "<option value = '".$key->id."'>".$key->rolename."</option>";
                                  }
                                ?>
                              </select>
                            </div>
                            <div class="col-md-3 col-sm-3 ">
                              <button type="button" class="btn btn-primary" id="filter_">Filter</button>
                            </div>
                        </div>
                      </form>
                    </div>
                    <div class="col-md-12 col-sm-12  form-group">
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
                <h4 class="modal-title" id="myModalLabel">Modal User</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="post_" data-parsley-validate class="form-horizontal form-label-left">
                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Username <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <input type="text" name="uname" id="uname" required="" placeholder="Username" class="form-control ">
                      <input type="hidden" name="id" id="id">
                      <input type="hidden" name="formtype" id="formtype" value="add">
                    </div>
                  </div>

                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Nama User <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <input type="text" name="nama" id="nama" required="" placeholder="Nama User" class="form-control ">
                    </div>
                  </div>

                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Password <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <input type="Password" name="pass" id="pass" required="" placeholder="Password" class="form-control ">
                    </div>
                  </div>
                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Role <span class="required">*</span>
                    </label>
                    <div class="col-md-12 col-sm-12 ">
                      <select class="form-control col-md-6" id="roles" name="roles" >
                        <?php
                          $rs = $this->db->query("select * from roles")->result();
                          foreach ($rs as $key) {
                            echo "<option value = '".$key->id."'>".$key->rolename."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="item" form-group>
                    <button class="btn btn-primary" id="btn_Save">Save</button>
                  </div>
                </form>
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
          // $('#roles').select2({
          //   width : 'resolve'
          // });

          var kriteria = '';
          var skrip = '';
          var userid = '';
          var roleid = $('#fil_roles').val();

          $.ajax({
            type: "post",
            url: "<?=base_url()?>Auth/read",
            data: {kriteria:kriteria,skrip:skrip,userid:userid,roleid:roleid},
            dataType: "json",
            success: function (response) {
              bindGrid(response.data);
            }
          });
        });
        $('#filter_').click(function () {
          var kriteria = '';
          var skrip = '';
          var userid = '';
          var roleid = $('#fil_roles').val();

          $.ajax({
            type: "post",
            url: "<?=base_url()?>Auth/read",
            data: {kriteria:kriteria,skrip:skrip,userid:userid,roleid:roleid},
            dataType: "json",
            success: function (response) {
              bindGrid(response.data);
            }
          });
        })
        $('#post_').submit(function (e) {
          $('#btn_Save').text('Tunggu Sebentar.....');
          $('#btn_Save').attr('disabled',true);

          e.preventDefault();
          var me = $(this);

          $.ajax({
                type    :'post',
                url     : '<?=base_url()?>Auth/RegisterUser',
                data    : me.serialize(),
                dataType: 'json',
                success : function (response) {
                  if(response.success == true){
                    $('#modal_').modal('toggle');
                    Swal.fire({
                      type: 'success',
                      title: 'Horay..',
                      text: 'Data Berhasil disimpan!',
                      // footer: '<a href>Why do I have this issue?</a>'
                    }).then((result)=>{
                      location.reload();
                    });
                  }
                  else{
                    $('#modal_').modal('toggle');
                    Swal.fire({
                      type: 'error',
                      title: 'Woops...',
                      text: response.message,
                      // footer: '<a href>Why do I have this issue?</a>'
                    }).then((result)=>{
                      $('#modal_').modal('show');
                      $('#btn_Save').text('Save');
                      $('#btn_Save').attr('disabled',false);
                    });
                  }
                }
              });
            });
        $('.close').click(function() {
          location.reload();
        });
    function GetData(id) {
      var where_field = 'id';
      var where_value = id;
      var table = 'users';
      $.ajax({
            type: "post",
            url: "<?=base_url()?>Auth/ReadUser",
            data: {'id':id},
            dataType: "json",
            success: function (response) {
              $.each(response.data,function (k,v) {
                console.log(response.data);
                // $('#KodePenyakit').val(v.KodePenyakit).change;
                $('#uname').val(v.username);
                $('#nama').val(v.nama);
                $('#pass').val(response.decript);
                $('#roles').val(v.roleid).trigger('change');
                $('#id').val(v.id);
                // $('#Nilai').val(v.Nilai);

                $('#formtype').val("edit");

                $('#modal_').modal('show');
              });
            }
          });
    }
    function bindGrid(data) {

      $("#gridContainer").dxDataGrid({
        allowColumnResizing: true,
            dataSource: data,
            keyExpr: "UserId",
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
                allowAdding:true,
                allowUpdating: true,
                allowDeleting: true,
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
                fileName: "Daftar Penyakit"
            },
            columns: [
                {
                    dataField: "UserId",
                    caption: "#",
                    allowEditing:false
                },
                {
                    dataField: "username",
                    caption: "Username",
                    allowEditing:false
                },
                {
                    dataField: "nama",
                    caption: "Nama",
                    allowEditing:false
                },
                {
                    dataField: "rolename",
                    caption: "Level Akses",
                    allowEditing:false
                }
                // {
                //     dataField: "NamaPenyakit",
                //     caption: "Nama Penyakit",
                //     allowEditing:false
                // },
                // {
                //     dataField: "Nilai",
                //     caption: "Nilai",
                //     allowEditing:false
                // },
            ],
            onEditingStart: function(e) {
                GetData(e.data.UserId);
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
              id = e.data.id;
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
                  var table = 'app_setting';
                  var field = 'id';
                  var value = id;

                  $.ajax({
                      type    :'post',
                      url     : '<?=base_url()?>Auth/RegisterUser',
                      data    : {'id':id,'formtype':'delete'},
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
</script>