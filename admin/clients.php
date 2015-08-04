<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>:: Romeo Admin :: </title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php include('inc/js.php') ;?>
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
<script>
$( document ).ready(function() {
  getClients();
  getProjectNames();

});

</script>
<script type="text/javascript" charset="utf-8">

</script>

</head>
<body>
<!--layout-container start-->
<div id="layout-container"> 
  <!--Left navbar start-->
  
<?php
  include('inc/left.php');
?>
  <!--main start-->
<!-- Modal -->
<div class="modal fade" id="addclient" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<input type="hidden" id="client_id" value="">
  <div class="modal-dialog" >
    <div class="modal-content" >
      <div class="modal-header">
          <div class="notification-bar" id="msg" style="display: none;"></div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><span id="mode">Add </span> Client</h4>
      </div>
      <div class="modal-body">


<div class="container">
  <div class="row clearfix">
    <div class="col-md-10 column">
      <form class="form-horizontal" role="form" onsubmit="return false;">
        <div class="form-group">
           <label for="inputEmail3" class="col-sm-2 control-label">Client Name</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" id="client_name" />
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="container">
  <div class="row clearfix">
    <div class="col-md-10 column">
      <form class="form-horizontal" role="form" onsubmit="return false;">
        <div class="form-group">
           <label for="inputEmail3" class="col-sm-2 control-label">Logo</label>
          <div class="col-sm-5">
<!--             <input type="text" class="form-control" id="logo" /> -->

        <input type="file" id="image" name="image" data-url="../api/client_upload" class="file-pos">

        <img src="images/logoplaceholder.png" id="temp_pic" width="150" height="75">
        <span>Preferred size: 150 X 75</span>
        <input type="hidden" value="" id="path">

          </div>
        </div>
      </form>
    </div>
  </div>
</div>

    <div class="container">
  <div class="row clearfix">
    <div class="col-md-10 column">
      <form class="form-horizontal" role="form" onsubmit="return false;">
        <div class="form-group">
           <label for="inputEmail3" class="col-sm-2 control-label">Project</label>
          <div class="col-sm-4">
          <select id="project_id">
                           
          </select>

          </div>
        </div>
      </form>
    </div>
  </div>
</div>
    
    <div class="container">
  <div class="row clearfix">
    <div class="col-md-10 column">
      <form class="form-horizontal" role="form" onsubmit="return false;">
        <div class="form-group">
           <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
          <div class="col-sm-4" >
          <textarea class="form-control textarea"  style="width:354px; height:139px;" rows="2" id="description"></textarea>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>



      </div>
      <div class="modal-footer">
        <img src="images/spinner.gif" id="spinner" style="position:absolute; right:150px; display:none;">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" onclick="addUpdateClient();" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

  <div id="main">



  <?php
    include('inc/nav.php');
  ?>
    <!--margin-container start-->
    <div class="margin-container">
    <!--scrollable wrapper start-->
      <div class="scrollable wrapper">
      <!--row start-->
        <div class="row">
         <!--col-md-12 start-->
          <div class="col-md-12">
            <div class="page-heading">
              <h1>Clients  <button type="button" data-toggle="modal" data-target="#addclient" onclick="showAddClientPopup();" class="btn btn-primary">Add Client</button>  </h1>
            </div>

          <div class="notification-bar" id="jobmsg" style="display: none;"></div>

          </div><!--col-md-12 end-->
          <div class="col-sm-6 col-md-12">
            <div class="box-info">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th >Name</th>
                    <th>Logo</th>
                    <th>Project</th>
                    <th>Description</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="clientsbody">

                </tbody>
              </table>
            </div>
          </div>

        </div><!--row end-->
      </div><!--scrollable wrapper end--> 
    </div><!--margin-container end--> 
  </div><!--main end--> 
</div><!--layout-container end--> 
<script>
   $('#image').fileupload({
        dataType: 'json',
        done: function (e, data) {
          $('#image_spinner').hide();          
       $('#path').val(data.result.file_name);
       $('#temp_pic').attr('src',data.result.web_url);
        },
        send: function (e, data) {
          $('#image_spinner').show();
        }
    });
</script>

</body>
</html>