<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SB-Mid-client-rpgeWLcFuJy3gWGf"></script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
  </head>
 
  <body>
    <input type="hidden" name="token" id="token" value="<?php echo $token; ?>">
  </body>
</html>
<script src="<?php echo base_url();?>Assets/devexpress/jquery.min.js"></script>
<script>window.jQuery || document.write(decodeURIComponent('%3Cscript src="js/jquery.min.js"%3E%3C/script%3E'))</script>
<script type="text/javascript">
  $(function () {

    $(document).ready(function () {

      snap.pay($('#token').val(),{
        onSuccess: function(result){
            console.log(result);   
        }
      });
    });
  });
  // var payButton = document.getElementById('pay-button');
  // // For example trigger on button clicked, or any time you need
  // payButton.addEventListener('click', function () {
  //   snap.pay('ba163e0b-e9fb-45b8-b6fc-307b0b5d8ad9',{
  //       onSuccess: function(result){
  //           console.log(result);   
  //       }
  //   }); // Replace it with your transaction token
  // });
</script>