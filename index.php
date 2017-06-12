<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit image</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/jquery-ui.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Edit image</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Home</a></li>
        <li><a href="#">About us</a></li>
        <li><a href="#">Help</a></li>
      </ul>
    </div>
  </div>
</nav>
  
<div class="container">
  <div class="row">
    <div class="col-sm-12">
	  <h3 class="text-center">Upload Image:</h3>
      <form id="upload-form" class="form-horizontal row">
	  	<div class="input-group upload-image col-sm-6 col-sm-offset-3">
			<input type="file" id="inputFile" name="inputFile">
	  	</div>
	  </form>
	  <div class="col-sm-6 centered" id="uploadImage">
		<?php if(isset($_SESSION['uploadImage'])) { ?>
			<button type="button" data-toggle="modal" data-target="#imageModal">Edit</button>
			<img src='<?php echo $_SESSION["uploadImage"]["url"]; ?>'>
		<?php } ?>
	  </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit picture</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
      		<div class="col-md-1 col-sm-2">
      			<div id="imageTool">
	      			<ul class="row">
	      				<li class="col-sm-12 col-sm-offset-0 col-xs-2 col-xs-offset-1">
	      					<a class="rotate-right" href="javascript:void(0);" title="Rotate-right"><i class="fa fa-repeat" aria-hidden="true"></i></a>
	      				</li>
	      				<li class="col-sm-12 col-xs-2">
	      					<a class="rotate-left" href="javascript:void(0);" title="Rotate-left"><i class="fa fa-undo" aria-hidden="true"></i></a>
	      				</li>
	      				<li class="col-sm-12 col-xs-2">
	      					<a class="zoom-in" href="javascript:void(0);" title="Zoom-in"><span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span></a>
	      				</li>
	      				<li class="col-sm-12 col-xs-2">
	      					<a class="zoom-out" href="javascript:void(0);" title="Zoom-out"><span class="glyphicon glyphicon-zoom-out" aria-hidden="true"></span></a>
	      				</li>
	      				<li class="col-sm-12 col-xs-2">
	      					<a class="reset" href="javascript:void(0);" title="Reset"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></a>
	      				</li>
	      			</ul>
	      		</div>
      		</div>
      		<div class="col-md-10 col-sm-8">
      			<div id="editImageArea">
				<?php if(isset($_SESSION['uploadImage'])) { ?>
	        		<img src='<?php echo $_SESSION["uploadImage"]["url"]; ?>'>
	        		<div id="cropArea" class="ui-widget-content"></div>
				<?php } ?>
				</div>
		    </div>
      		<div class="col-md-1 col-sm-2" >
      			<div id="guideArea">
      				<p><span class="redcolor"></span> Crop area</p>
      			</div>
      		</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveImage">Save changes</button>
      </div>
    </div>
  </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script type="text/javascript">
var crd  = 0, 		// currentRotateDegree
 	eias = [],		// editImageArea size 				: 	aias[w,h]
	nis  = [],		// natural image size 				: 	nis[w,h]
	dis  = [],		// Default image size in display	: 	dis[w,h,rw,rh]
	cis  = [],		// Current image size in display 	: 	cis[w,h,rw,rh]
	ulis = false,	// Upload image status				
	aAx	 = "width",	// Auto Axis
	rera = 1,		// Resize rate
	step = 0.05,	// Resize rate step
	crPrs = [],		// Crop area post to server 		: 	crPrs[w,h,t,l]
	//crot=0, 		// Crop top possition 
	//crol=0,			// Crop left possition 
	smst=false;		// Show edit image model status
	radst=false;	// Enable resize & drag crop area status

crPrs['width']	= 0;
crPrs['height'] = 0;
crPrs['top'] 	= 0;
crPrs['left'] 	= 0;

<?php if(isset($_SESSION['uploadImage'])) { ?>
	nis["width"] 	= <?php echo $_SESSION["uploadImage"]["size"]["width"]; ?>;
	nis["height"]	= <?php echo $_SESSION["uploadImage"]["size"]["height"]; ?>;
	ulis = true;
<?php } ?>
var $icobj;	// Container of Image 
//var $iobj;	// Image object
$(document).ready(function() {
	$icobj 	= $("#editImageArea");
	//$iobj 	= $("#editImageArea img");
	$('#imageModal').on('shown.bs.modal', function (e) {
		eias["width"]	= $icobj.width();
		eias["height"]	= $icobj.height();
		if(!smst) {
			dis["top"] 	= 0;
			dis["left"] = 0;
			cis["top"] 	= 0;
			cis["left"] = 0;
		}
		smst = true;
		if(ulis) {
			setImgSize();
		}
	});

	$( window ).resize(function() {
		eias["width"] = $icobj.width();
		eias["height"] = $icobj.height();
		if(ulis && eias["width"]!=100) {
			setImgSize();
		}
	});

    $("#upload-form").on('change', '#inputFile', function (e) {
        var querytype = $('.support-query').val();
        var file_data = $(this).prop('files')[0];

        var form_data = new FormData();

        form_data.append('inputFile', file_data);
        form_data.append('action', 'upload_design_image');

        $("#uploadImage").html('<img src="img/img-loader.gif">');

        $.ajax({
            url: 'upload.php',
            type: 'post',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {
            	var imgRes = jQuery.parseJSON(response);
                $("#uploadImage").html('<button type="button" data-toggle="modal" data-target="#imageModal">Edit</button><img src="'+imgRes.url+'">');
                $icobj.html('<img src="'+imgRes.url+'"><div id="cropArea" class="ui-widget-content"></div>');
                ulis = true;
                rera = 1;
				nis["width"]  = imgRes.size.width;
				nis["height"] = imgRes.size.height;

				smst = false;
				dis["top"] 	= 0;
				dis["left"] = 0;
				cis["top"] 	= 0;
				cis["left"] = 0;
				setImgSize();
            },
            error: function (response) {
             	console.log('error');
            }

        });
    });
    /*
    $("#upload-form #uploadImage").on('click', 'button', function (e) {

    });
    */
    var ncrd = 0;
    $("#imageTool").on('click', '.rotate-right', function () {
    	if(crd==270)	ncrd = 0;
    	else	ncrd = crd + 90;
    	$("#editImageArea img").removeClass('rotate-'+crd).addClass('rotate-'+ncrd);
    	crd = ncrd;
    });

    $("#imageTool").on('click', '.rotate-left', function () {
    	if(crd==0)	ncrd = 270;
    	else	ncrd = crd - 90;
    	$("#editImageArea img").removeClass('rotate-'+crd).addClass('rotate-'+ncrd);
    	crd = ncrd;
    });

    $("#imageTool").on('click', '.zoom-in', function () {
    	if(rera < 5)	rera += step;
    	updateImgSize();
    });
    
    $("#imageTool").on('click', '.zoom-out', function () {
    	if(rera > 0.1)	rera -= step;
    	updateImgSize();
    });

    $("#imageTool").on('click', '.reset', function () {
    	crd = 0;
    	$("#editImageArea img").removeAttr('class').removeAttr("style");
    	rera = 1;
    	setImgSize();
    });

    $("#imageModal").on('click', "#saveImage", function () {
        var form_data = new FormData();

        form_data.append('crPosLt', crPrs['left']);
        form_data.append('crPosTp', crPrs['top']);
        form_data.append('crPosW', crPrs['width']);
        form_data.append('crPosH', crPrs['height']);

        //form_data.append('imgPosLt', crol);
        //form_data.append('imgPosTo', crot);

        form_data.append('imgSiW', dis['rwidth']);
        form_data.append('imgSiH', dis['rheight']);
        form_data.append('imgSiTp', dis['top']);
        form_data.append('imgSiLt', dis['left']);

        form_data.append('imgCiW', cis['rwidth']);
        form_data.append('imgCiH', cis['rheight']);
        form_data.append('imgCiTp', cis['top']);
        form_data.append('imgCiLt', cis['left']);

        form_data.append('edImW', eias['width']);
        form_data.append('edImH', eias['height']);

        form_data.append('imgRd', crd);
        form_data.append('imgReRa', rera);
        form_data.append('action', 'update_design_image');

    	$.ajax({
            url: 'update.php',
            type: 'post',
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {
            	var imgRes = jQuery.parseJSON(response);
                $("#uploadImage").html('<button type="button" data-toggle="modal" data-target="#imageModal">Edit</button><img src="'+imgRes.url+'">');
                $icobj.html('<img src="'+imgRes.url+'"><div id="cropArea" class="ui-widget-content"></div>');
                ulis = true;
                rera = 1;
				nis["width"]  = imgRes.size.width;
				nis["height"] = imgRes.size.height;

				smst = false;
				dis["top"] 	= 0;
				dis["left"] = 0;
				cis["top"] 	= 0;
				cis["left"] = 0;
				setImgSize();
				$('#imageModal').modal('hide');
				//location.reload();
            },
            error: function (response) {
             	console.log('error');
            }

        });
    });
});
/*
jQuery(window).on("load", function() {
    console.log($icobj.width());
});
*/
function setImgSize() {
	if((nis["width"]/nis["height"]) >= (eias["width"]/eias["height"])) {
		dis["width"] 	=	dis["rwidth"]	=	eias["width"];
		cis["width"]	=	cis["rwidth"]	= 	rera*dis["width"];
		dis["height"]	=	cis["height"] 	=	"auto";
		dis["rheight"]	= 	nis["height"] * eias["width"] / nis["width"];
		cis["rheight"]	=	rera*dis["rheight"];
		aAx 			= "height";
		if(smst) {
			crPrs['top']=(eias["height"]-dis["rheight"])/2;
			crPrs['left']	=	0;
			if(!dis["top"])  dis["top"] = (eias["height"]-dis["rheight"])/2;
		}
	} else {
		dis["width"]	=	cis["width"]	=	"auto";
		dis["rwidth"]	= 	nis["width"] * eias["height"] / nis["height"];
		cis["rwidth"]	= 	rera*dis["rwidth"];
		dis["height"]	=	dis["rheight"]	= eias["height"];
		cis["height"]	=	cis["rheight"]	= rera*dis["height"];
		if(smst) {
			crPrs['left']	=	(rera*eias["width"]-cis["rwidth"])/2;
			crPrs['top']	= 	0;
			if(!dis["left"])
				dis["left"] = (eias["width"]-dis["rwidth"])/2;
		}
	}
	setImgPos();
}

function updateImgSize() {
	if(!isNaN(dis["width"]))	cis["width"]  	= rera*dis["width"];
	if(!isNaN(dis["height"]))	cis["height"] 	= rera*dis["height"];
	if(!isNaN(dis["rwidth"]))	cis["rwidth"] 	= rera*dis["rwidth"];
	if(!isNaN(dis["rheight"]))	cis["rheight"]	= rera*dis["rheight"];
	setImgPos();
}

function setImgPos() {
	//$("#cropArea").css({"width": (cis["rwidth"]), "height": (cis["rheight"]), "top":t, "left":l});
	$("#editImageArea img").css({"width":  cis["width"], "height": cis["height"], "margin-top": -(cis["rheight"]/2), "margin-left": -(cis["rwidth"]/2)});

	if(smst) {
		//if(!crPrs['width'])		
			crPrs['width']	= cis["rwidth"];
		//if(!crPrs['height'])	
			crPrs['height']	= cis["rheight"];
	}

	//$("#cropArea").css({"width": (cis["rwidth"]), "height": (cis["rheight"]), "top":t, "left":l});
	if(smst) {
		cis["top"] = (eias["height"]-cis["rheight"])/2;
		cis["left"] = (eias["width"]-cis["rwidth"])/2;

		if(radst)	$("#cropArea").resizable("destroy").draggable("destroy");

		$("#cropArea").resizable({
			containment: "#editImageArea",
		    //aspectRatio: true,
		    handles: "n, e, s, w, se",
		    //handles: {"n", "e", "s", "w", "se":$("#se-resize")},
		    create: function( event, ui ) {
		    	$(this).css({
		            'top': 		crPrs['top'],
		            'left': 	crPrs['left'],
		            'width': 	crPrs["width"],
		            'height': 	crPrs["height"]
		        });
		        $('.ui-icon-gripsmall-diagonal-se').html('<i class="fa fa-expand" aria-hidden="true"></i>');
		    },
		    resize: function(event, ui) {
		    	var radst = true;
		    	var axis = $(event.target).data('uiResizable').axis;
		    	var ct = 0;
		    	var cl = 0;
				if(axis == "se") {
					ct = parseInt(ui.position.top, 10) + ((ui.originalSize.height - ui.size.height)) / 2;
					cl = parseInt(ui.position.left, 10) + ((ui.originalSize.width - ui.size.width)) / 2;
			        $(this).css({
			            'top': ct,
			            'left': cl
			        });
				    crPrs['top'] 	= ct;
				    crPrs['left']	= cl;
			    } else {
				    crPrs['top'] 	= ui.position.top;
				    //crPrs['top'] 	= $(this).css('top');
				    crPrs['left'] 	= ui.position.left;
				    //crPrs['left'] 	= $(this).css('left');
			    }
			    crPrs['width']	= ui.size.width;
			    crPrs['height']	= ui.size.height;
		    }
		})
		.draggable({ 
			containment: "parent",
			drag: function( event, ui ) {
			    crPrs['top'] 	= ui.position.top;
			    crPrs['left'] 	= ui.position.left;
			}
		});
	}
}
</script>
</body>
</html>
