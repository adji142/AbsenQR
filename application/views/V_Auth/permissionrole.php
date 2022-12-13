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
            <h2>Hak Akses User : <?php echo $rolename ?></h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <input type="hidden" name="roleid" id="roleid" value="<?php echo $roleid; ?>">
            <?php
              $SQL = "SELECT a.*,COALESCE(b.roleid,'') roleid FROM permission a
                      LEFT JOIN permissionrole b on a.id = b.permissionid and b.roleid = ".$roleid."
                      order by a.order";
              $rs = $this->db->query($SQL);
              $Check = '<label>Hak Akses untuk User : '.$rolename.'</label><br>';
              if ($rs->num_rows() > 0) {
                foreach ($rs->result() as $key) {
                  if ($key->roleid != '') {
                    if ($key->menusubmenu == 0) {
                      $Check .= '<input type="checkbox" name="P'.$key->id.'" id="P'.$key->id.'" value="'.$key->id.'" data-parsley-mincheck="2" required class="flat" checked/>'.$key->permissionname.'<br />';
                    }
                    else{
                      $Check .= '++++'.'<input type="checkbox" name="P'.$key->id.'" id="P'.$key->id.'" value="'.$key->id.'" data-parsley-mincheck="2" required class="flat" checked/>'.$key->permissionname.'<br />';
                    }
                  }
                  else{
                    if ($key->menusubmenu == 0) {
                      $Check .= '<input type="checkbox" name="P'.$key->id.'" id="P'.$key->id.'" value="'.$key->id.'" data-parsley-mincheck="2" required class="flat" />'.$key->permissionname.'<br />';
                    }
                    else{
                      $Check .= '++++'.'<input type="checkbox" name="P'.$key->id.'" id="P'.$key->id.'" value="'.$key->id.'" data-parsley-mincheck="2" required class="flat" />'.$key->permissionname.'<br />';
                    }
                  }
                }
                echo $Check;
              }
            ?>
            <button class="btn btn-success" id="Simpan">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  require_once(APPPATH."views/parts/Footer.php");
?>
<script type="text/javascript">
  $(function () {
    $(document).ready(function () {
      var where_field = '';
      var where_value = '';
      var table = 'users';

      $.ajax({
        type: "post",
        url: "<?=base_url()?>C_Atribut/read",
        data: {'id':''},
        dataType: "json",
        success: function (response) {
          bindGrid(response.data);
        }
      });
    });
    $('#Simpan').click(function () {
      $('#Simpan').text('Tunggu Sebentar.....');
      $('#Simpan').attr('disabled',true);
      var roleid = $('#roleid').val();
      $.ajax({
        async:false,
        type: "post",
        url: "<?=base_url()?>C_UserManagement/ReadPermission",
        data: {'id':''},
        dataType: "json",
        success: function (response) {
          if (response.success == true) {
            $.ajax({
              type: "post",
              url: "<?=base_url()?>C_UserManagement/RemovePermissionRole",
              data: {'roleid':roleid},
              dataType: "json",
              success: function (responseRemove) {
                $.each(response.data,function (k,v) {
                  if ($("#P"+v.id+":checked").val()) {
                    $.ajax({
                      async:false,
                      type: "post",
                      url: "<?=base_url()?>C_UserManagement/AddPermissionRole",
                      data: {'roleid':roleid,'permissionid':$("#P"+v.id+":checked").val()},
                      dataType: "json",
                      success: function (responseinput) {
                        if (responseinput.success == true) {
                          Swal.fire({
                            type: 'success',
                            title: 'Horay..',
                            text: 'Data Berhasil disimpan!',
                            // footer: '<a href>Why do I have this issue?</a>'
                          }).then((result)=>{
                            window.location.href = "<?php echo base_url() ?>role";
                          });
                        }
                      }
                    });
                  }
                  else{
                    console.log('Kosong')
                  }
                }); 
              }
            });
          }
        }
      });
    })
    $('.close').click(function() {
      location.reload();
    });
  });
</script>