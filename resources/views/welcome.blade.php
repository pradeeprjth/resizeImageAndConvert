<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Image Processor</title>
   <meta name="description" content="">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
   <!-- script -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

<body>
   <nav class="navbar navbar-light bg-light">
      <a class="navbar-brand " href="#">Image Processor</a>
   </nav>
   <h3 class="text-center">Image Proccessor</h3>
   <div class="container">
      <label class="form-label" for="customFile">Select an image file</label>
      <form method="post" action="" enctype="multipart/form-data" id="myform">
         @csrf
         <div class="row">
            <div class="col 6">
               <input id="uploadImage" type="file" name="file" class="form-control-file" onchange="PreviewImage();" />
               <br>
               <ul id="imageInfo">

               </ul>
            </div>
            <div class="col 6" id="previewDiv">
               <img id="uploadPreview" style="width: 200px; height: 200px;" />
            </div>
         </div>
         <br>
         <br>
         <div class="form-row">
            <div class="form-group col-md-3">
               <label for="inputCity">Width</label>
               <input type="number" name="modifiedHeight" class="form-control" id="inputHeight">
            </div>
            <div class="form-group col-md-3">
               <label for="inputState">Height</label>
               <input type="number" name="modifiedWidth" class="form-control" id="inputWidth">
            </div>
            <div class="form-group col-md-6">

            </div>
         </div>
         <br>
         <div class="form-check">
            <input class="form-check-input" name="changeTypeTo" type="checkbox" value="" id="changeType">
            <label class="form-check-label" id="typeLabel" for="defaultCheck1">
            </label>
         </div>
         <br>
         <button type="button" class="submitButton btn-sm btn btn-primary" onclick="resubmitForm();">Apply
            Changes</button>
      </form>
   </div>

   <!-- Modal -->
   <div class="modal fade" id="proccessedImage" tabindex="-1" aria-labelledby="proccessedImageLabel" aria-hidden="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="proccessedImageLabel">Modal title</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-6"> <img id="downloadPreview" style="width: 200px; height: 200px;" /></div>
                  <div class="col-6">
                     <div id="proccessedimageInfo"></div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btnclose btn btn-secondary" data-dismiss="modal">Close</button>
               <a href="" id="downloadLink" download><button type="button" class="btnclose btn btn-primary">Download</button></a>
            </div>
         </div>
      </div>
   </div>

   <script type="text/javascript">
   $(document).ready(function() {

      $("#previewDiv").hide();
      $(".submitButton").hide();
      $(".download").hide();
   });

   function PreviewImage() {
      $("#previewDiv").show();
      var oFReader = new FileReader();
      oFReader.readAsDataURL(document.getElementById("uploadImage").files[0]);
      const file = document.getElementById("uploadImage").files[0];
      const fileType = file['type'];
      const validImageTypes = ['image/jpeg', 'image/png'];
      if (!validImageTypes.includes(fileType)) {
         alert("Only .jpeg and .png images are supported");
         location.reload();
      }
      oFReader.onload = function(oFREvent) {
         document.getElementById("uploadPreview").src = oFREvent.target.result;
      };
      checkImage();
   };

   function resubmitForm() {
      checkImage();
   }

   function checkImage() {
      let myForm = $('#myform')[0];
      var fd = new FormData(myForm);

      $.ajax({
         url: 'checkImage',
         type: 'post',
         data: fd,
         contentType: false,
         processData: false,
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
         beforeSend: function() {
            $(".submitButton").text("Please wait..");
         },
         success: function(response) {
            $(".submitButton").text("Apply Changes");
            if (response.status == 'checking') {
               $("#aboutImage").append('<li class="text-success">Your image height is : <b>' + response
                  .height + '</b></li>' +
                  '<li class="text-success">Your image width is : <b>' + response.width + '</b></li>' +
                  '<li class="text-success">Your image type is: <b>' + response.mimeType + '</b></li>');
               $("#inputHeight").val(response.height);
               $("#inputWidth").val(response.width);
               const myArray = response.mimeType.split("/");
               let type = myArray[1];
               $("#changeType").val(type);
               if (type == "jpeg") {
                  $("#typeLabel").html("Convert from " + type + " to png");
               } else {
                  $("#typeLabel").html("Convert from " + type + " to jpg");
               }
               $(".submitButton").show();
            } else if (response.status == 'newJpg') {
               console.log(response);
               $('#proccessedImage').modal('show');
               $("#downloadPreview").attr('src', 'images/processedImages/' + response.NewImage);
               $("#downloadLink").attr('href', 'images/processedImages/' + response.NewImage);
               $("#proccessedimageInfo").append('<ul>' +
                  '<li>New width of image : ' + response.NewWidth + '</li>' +
                  '<li>New height of image : ' + response.NewHeight + '</li>' +
                  '<li>Type of image : ' + response.NewMimeType + '</li>' +
                  '</ul>');
            } else if (response.status == 'newPng') {
               console.log(response);
               $('#proccessedImage').modal('show');
               $("#downloadPreview").attr('src', 'images/processedImages/' + response.NewImage);
               $("#downloadLink").attr('href', 'images/processedImages/' + response.NewImage);
               $("#proccessedimageInfo").append('<ul>' +
                  '<li>New width of image : ' + response.NewWidth + '</li>' +
                  '<li>New height of image : ' + response.NewHeight + '</li>' +
                  '<li>Type of image : ' + response.NewMimeType + '</li>' +
                  '</ul>');
            } else if (response.status == 'CRImgage') {
               console.log(response);
               $('#proccessedImage').modal('show');
               $("#downloadPreview").attr('src', 'images/convertedResizedImages/' + response.NewImagePath);
               $("#downloadLink").attr('href', 'images/convertedResizedImages/' + response.NewImagePath);
               $("#proccessedimageInfo").append('<ul>' +
                  '<li>New width of image : ' + response.NewWidth + '</li>' +
                  '<li>New height of image : ' + response.NewHeight + '</li>' +
                  '<li>Type of image : ' + response.NewMimeType + '</li>' +
                  '</ul>');
            } else if (response.status == 'FileFormatError') {
               alert("File Format not supported (Only *png and *jpg)");
            }
         },
      });
   }
   $(".btnclose").on("click", function(e) {
      setTimeout(function() {
         location.reload();
      }, 2000)
   });
   </script>

</body>

</html>

<!-- model -->