<html>
  <head>
    <script src="https://cdn.jsdelivr.net/npm/epubjs@0.3.85/dist/epub.min.js"></script>
  </head>
  <body>
    <div id="area"></div>
  </body>
</html>
<script src="<?php echo base_url();?>Assets/devexpress/jquery.min.js"></script>
    <script>window.jQuery || document.write(decodeURIComponent('%3Cscript src="js/jquery.min.js"%3E%3C/script%3E'))</script>
<script type="text/javascript">
$(function () {
	// $(document).ready(function() {
	// 	console.log("Data");
	//   var book = ePub("http://localhost/spiritweb/localData/epub/100005.epub");
	//   console.log(book);
	//   var rendition = book.renderTo("area", {width: '100%', height: '100%'});
	//   var displayed = rendition.display(); 
	//   console.log(book);
	// });
	$(document).ready(function () {
      var where_field = '';
      var where_value = '';
      var table = 'users';

      $.ajax({
        type: "post",
        url: "<?=base_url()?>C_PaymentMethod/Read",
        data: {'id':''},
        dataType: "json",
        success: function (response) {
          // bindGrid(response.data);
          console.log(response);
        }
      });
    });
})
</script>