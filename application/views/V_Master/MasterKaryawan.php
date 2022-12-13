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
                    <h2>Roles</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="row">
                      <div class="col-md-6 col-sm-6  ">
                        <button class='btn btn-success' id="btDownloadAll">Download QR Code</button>
                        <a href="<?php echo base_url();?>Home/printLayout" target="_blank" class='btn btn-danger'>Download Card ID</a>
                      </div>
                      <div class="col-md-12 col-sm-12  ">
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
        </div>
        <!-- /page content -->

        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" id="modal_">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Master Karyawan</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="post_" data-parsley-validate class="form-horizontal form-label-left">
                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">NIK <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <input type="text" name="Nik" id="Nik" required="" placeholder="Nomer Induk Karyawan" class="form-control ">
                    </div>
                  </div>

                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Nama Karyawan <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <input type="text" name="NamaKaryawan" id="NamaKaryawan" required="" placeholder="Nama Jabatan" class="form-control ">
                      <input type="hidden" name="formtype" id="formtype" value="add">
                    </div>
                  </div>

                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Jabatan <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <select class="form-control " name="KodeJabatan" id="KodeJabatan">
                        <option value="">Pilih Jabatan</option>
                        <?php
                          $rs = $this->ModelsExecuteMaster->GetData('tjabatan')->result();

                          foreach ($rs as $key) {
                            echo "<option value = '".$key->KodeJabatan."'>".$key->NamaJabtan."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Departement <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <select class="form-control " name="KodeDepartment" id="KodeDepartment">
                        <option value="">Pilih Departement</option>
                        <?php
                          $rs = $this->ModelsExecuteMaster->GetData('tdepartement')->result();

                          foreach ($rs as $key) {
                            echo "<option value = '".$key->KodeDepartment."'>".$key->NamaDepartement."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Gambar <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 ">
                      <input type="file" id="Attachment" name="Attachment" accept=".jpg" />
                      <img src="" id="profile-img-tag" width="200" />
                      <!-- <textarea id="picture_base64" name="picture_base64"></textarea> -->
                      <textarea id="picture_base64" name="picture_base64" style="display: none;"></textarea>
                      <input type="hidden" name="ImageLink" id="ImageLink">
                    </div>
                  </div>

                  <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Active ? <span class="required">*</span>
                    </label>
                    <div class="col-md-5 col-sm-5 ">
                      <input type="checkbox" name="Active" id="Active" class="form-control" value="0">
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
  var _URL = window.URL || window.webkitURL;
  var _URLePub = window.URL || window.webkitURL;

  $(function () {
    $(document).ready(function () {
      var where_field = '';
      var where_value = '';
      var table = 'users';

      $.ajax({
        type: "post",
        url: "<?=base_url()?>C_MasterKaryawan/Read",
        data: {'Nik':''},
        dataType: "json",
        success: function (response) {
          bindGrid(response.data);
        }
      });
    });

    $('#Active').click(function () {
      if ($("#Active").prop("checked") == true) {
        $('#Active').val("1");
      }
      else{
        $('#Active').val("0"); 
      }
      console.log($('#Active').val());
    });

    $('#btDownloadAll').click(function () {
      $.ajax({
        type: "post",
        url: "<?=base_url()?>C_MasterKaryawan/Read",
        data: {'Nik':''},
        dataType: "json",
        success: function (response) {
          // bindGrid(response.data);

          $.each(response.data,function (k,v) {
            forceDownload('https://chart.apis.google.com/chart?cht=qr&chs=200x200&chld=H|0&chl='+v.Nik,v.Nik)
          });

        }
      });
    })
    $('#post_').submit(function (e) {
      $('#btn_Save').text('Tunggu Sebentar.....');
      $('#btn_Save').attr('disabled',true);

      // var Nik = $('#Nik').val();
      // var NamaKategori = $('#NamaKategori').val();
      // var ShowHomePage = $('#ShowHomePagex').val();
      // var ImageLink = $('#Attachment').prop('files')[0];
      // var ImageBase64 = $('#picture_base64').val();
      // var formtype = $('#formtype').val();

      e.preventDefault();
      // var me = $(this);

      var form_data = new FormData(this);
      // 'id':$('#id').val(),'NamaKategori':$('#NamaKategori').val(),'ShowHomePage':$('#ShowHomePagex').val(),'formtype':$('#formtype').val()
      $.ajax({
          type    :'post',
          url     : '<?=base_url()?>C_MasterKaryawan/CRUD',
          data    : form_data,
          dataType: 'json',
          processData: false,
          contentType: false,
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
            url: "<?=base_url()?>C_MasterKaryawan/Read",
            data: {'Nik':id},
            dataType: "json",
            success: function (response) {
              $.each(response.data,function (k,v) {
                // $('#KodePenyakit').val(v.KodePenyakit).change;
                $('#Nik').val(v.Nik);
                $("#Nik").attr("readonly","");

                $('#NamaKaryawan').val(v.NamaKaryawan);
                $('#KodeJabatan').val(v.KodeJabatan).change();
                $('#KodeDepartment').val(v.KodeDepartement).change();

                $('#Active').val(v.Active);

                if(v.Active == 1){
                  $('#Active').attr("checked",true);
                }
                else{
                  $('#Active').attr("checked",false);
                }

                $('#ImageLink').val(v.ImageLink);
                $('#picture_base64').val(v.ImageBase64);

                $('#profile-img-tag').attr("src",v.ImageBase64);
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
            keyExpr: "Nik",
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
                fileName: "Daftar Karyawan"
            },
            filterRow: { visible: true },
            columns: [
                {
                    dataField: "Nik",
                    caption: "NIK",
                    allowEditing:false
                },
                {
                    dataField: "NamaKaryawan",
                    caption: "Nama Karyawan",
                    allowEditing:false
                },
                {
                    dataField: "NamaJabtan",
                    caption: "Nama Jabatan",
                    allowEditing:false
                },
                {
                    dataField: "NamaDepartement",
                    caption: "Nama Departement",
                    allowEditing:false
                },
                {
                    dataField: "StatusKaryawan",
                    caption: "Status",
                    allowEditing:false
                },
                {
                    dataField: "FileItem",
                    caption: "Action",
                    allowEditing:false,
                    cellTemplate: function(cellElement, cellInfo) {
                        LinkAccess = "<a href = '<?=base_url()?>permissionrole/"+cellInfo.data.id+"' class='btn btn-warning'>Hak Akses</a>";
                        console.log(cellInfo.data.Nik)
                        LinkAccess = "<button class='btn btn-success' onclick =forceDownload('https://chart.apis.google.com/chart?cht=qr&chs=200x200&chld=H|0&chl="+cellInfo.data.Nik+"','"+cellInfo.data.Nik+".png"+"')>Download QR Code</button>";

                        // LinkAccess = "<a href='https://chart.apis.google.com/chart?cht=qr&chs=200x200&chld=L|0&chl="+cellInfo.data.Nik+"' download ='"+cellInfo.data.Nik+".png' class='btn btn-success'>Download QR Code</a>";
                        
                        cellElement.append(LinkAccess);
                    }
                }
            ],
            onEditingStart: function(e) {
                GetData(e.data.Nik);
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
              KodeJabatan = e.data.KodeJabatan;
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
                      url     : '<?=base_url()?>C_MasterKaryawan/CRUD',
                      data    : {'KodeJabatan':KodeJabatan,'formtype':'delete'},
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

    $("#Attachment").change(function(){
      var file = $(this)[0].files[0];
      img = new Image();
      img.src = _URL.createObjectURL(file);
      var imgwidth = 0;
      var imgheight = 0;
      img.onload = function () {
        imgwidth = this.width;
        imgheight = this.height;
        $('#width').val(imgwidth);
        $('#height').val(imgheight);
      }
      readURL(this);
      encodeImagetoBase64(this);
      // alert("Current width=" + imgwidth + ", " + "Original height=" + imgheight);
    });

    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
          
        reader.onload = function (e) {
            $('#profile-img-tag').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
    }
    function encodeImagetoBase64(element) {
      $('#picture_base64').val('');
        var file = element.files[0];
        var reader = new FileReader();
        reader.onloadend = function() {
          // $(".link").attr("href",reader.result);
          // $(".link").text(reader.result);
          $('#picture_base64').val(reader.result);
        }
        reader.readAsDataURL(file);
    }

  });

function downloadImage(url) {
    // alert(url);
  // fetch(url, {
  //   mode : 'no-cors',
  // })
  //   .then(response => response.blob())
  //   .then(blob => {
  //   let blobUrl = window.URL.createObjectURL(blob);
  //   let a = document.createElement('a');
  //   console.log(a);
  //   a.download = url.replace(/^.*[\\\/]/, '');
  //   a.href = blobUrl;
  //   document.body.appendChild(a);
  //   a.click();
  //   a.remove();
  // })

  // var qrCodeBaseUri = 'https://api.qrserver.com/v1/create-qr-code/?',
  //       params = {
  //           data: data,
  //           size: '200x200',
  //           margin: 0,
  //           // more configuration parameters ...
  //           download: 1
  //       };
    // var link = document.createElement('a');
    // console.log(link);
    // link.href = 'images.jpg';
    // link.download = 'Download.png';
    // document.body.appendChild(link);
    // link.click();
    // document.body.removeChild(link);
    var x = $('.class').find('tag').attr('src');
    console.log(x);
    window.location.href = url;
}

function forceDownload(url, fileName){
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.responseType = "blob";
    xhr.onload = function(){
        var urlCreator = window.URL || window.webkitURL;
        var imageUrl = urlCreator.createObjectURL(this.response);
        var tag = document.createElement('a');
        tag.href = imageUrl;
        tag.download = fileName;
        document.body.appendChild(tag);
        tag.click();
        document.body.removeChild(tag);
    }
    xhr.send();
}
</script>