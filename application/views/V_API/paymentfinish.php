<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
    <script src="<?php echo base_url();?>Assets/sweetalert2-8.8.0/package/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url();?>Assets/sweetalert2-8.8.0/package/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="<?php echo base_url();?>Assets/devexpress/bootstrap-select.min.css" />
  </head>
 
  <body>
    <input type="hidden" name="NoTransaksi" id="NoTransaksi" value="<?php echo $order_id ?>">
  </body>
</html>
<script src="<?php echo base_url();?>Assets/devexpress/jquery.min.js"></script>
<script>window.jQuery || document.write(decodeURIComponent('%3Cscript src="js/jquery.min.js"%3E%3C/script%3E'))</script>
<script type="text/javascript">
  $(function () {

    $(document).ready(function () {
      var NoTransaksi = $('#NoTransaksi').val();
      console.log(NoTransaksi);
      $.ajax({
        type: "post",
        url: "<?=base_url()?>APIPaymentResult",
        data: {'NoTransaksi':NoTransaksi},
        dataType: "json",
        success: function (response) {
          // bindGrid(response.data);
          if (response.success == true) {
            console.log(response);
            // console.log($('#NoTransaksi').val());
            var NoTransaksi = $('#NoTransaksi').val();;
            var TglTransaksi = response.data.transaction_time;
            var TglPencatatan = Date.now();
            var MetodePembayaran = 'AUTO'; //response.data.payment_type
            var GrossAmt = response.data.gross_amount;
            var AdminFee = 0;
            var Mid_PaymentType = response.data.payment_type;
            var Mid_TransactionID = response.data.transaction_id;
            var Mid_MechantID = response.data.merchant_id;
            // console.log(typeof(response.data.va_numbers));
            if(typeof(response.data.va_numbers) == "undefined"){
                console.log("masuk undefinde");
            }
            // response.data.payment_type = "bank_transfer"
            if (typeof(response.data.va_numbers) != "undefined") {
              var Mid_Bank = response.data.va_numbers[0]["bank"];
              var Mid_VANumber = response.data.va_numbers[0]["va_number"];
            }
            else{
              var Mid_Bank = "";
              var Mid_VANumber = "";
            }
            var Mid_SignatureKey = response.data.signature_key;
            var Mid_TransactionStatus = response.data.transaction_status;
            var Mid_FraudStatus = response.data.fraud_status;

            $.ajax({
              type: "post",
              url: "<?=base_url()?>APIAddTransaksi",
              data: {NoTransaksi:NoTransaksi,TglTransaksi:TglTransaksi,TglPencatatan:TglPencatatan,MetodePembayaran:MetodePembayaran,GrossAmt:GrossAmt,AdminFee:AdminFee,Mid_PaymentType:Mid_PaymentType,Mid_TransactionID:Mid_TransactionID,Mid_MechantID:Mid_MechantID,Mid_Bank:Mid_Bank,Mid_VANumber:Mid_VANumber,Mid_SignatureKey:Mid_SignatureKey,Mid_TransactionStatus:Mid_TransactionStatus,Mid_FraudStatus:Mid_FraudStatus},
              dataType: "json",
              success: function (response_add) {
                // bindGrid(response.data);
                console.log(response_add.success);
                if (response_add.success == true) {
                  if(typeof(response.data.va_numbers) == "undefined"){
                    Swal.fire({
                      type: 'success',
                      title: 'Horay..',
                      text: 'Transaksi Kamu Berhasil silahkan melakukan Pembayaran! Untuk Informasi detail silahkan menuju menu Riwayat Transaksi. ',
                      // footer: '<a href>Why do I have this issue?</a>'
                    }).then((result)=>{
                      console.log("Done");
                      window.close();
                    });
                  }
                  else{
                    Swal.fire({
                      type: 'success',
                      title: 'Horay..',
                      text: 'Transaksi Kamu Berhasil silahkan melakukan Pembayaran! <br> No. Rekening <b>'+Mid_VANumber+'</b> <br> Bank : <b>'+Mid_Bank+'</b><br> Untuk Informasi detail silahkan menuju menu Riwayat Transaksi. ',
                      // footer: '<a href>Why do I have this issue?</a>'
                    }).then((result)=>{
                      console.log("Done");
                      // window.close();
                    });
                  }
                }
                else{
                  Swal.fire({
                    type: 'error',
                    title: 'Gagal melakukan pemrosesan data !',
                    text: response_add.message,
                  }).then((result)=>{
                    window.close();
                  });;
                }
              }
            });
          }
          else{
            Swal.fire({
              type: 'error',
              title: 'Gagal melakukan pemrosesan data',
              text: response.message,
              // footer: '<a href>Why do I have this issue?</a>'
            }).then((result)=>{
              window.close();
            });;
          }
        }
      });
      // Cek Transaksi

      // Swal.fire({
      //   type: 'success',
      //   title: 'Horay..',
      //   text: 'Transaksi Kamu Berhasil silahkan melakukan Pembayaran!',
      //   // footer: '<a href>Why do I have this issue?</a>'
      // });


    });
  });
</script>