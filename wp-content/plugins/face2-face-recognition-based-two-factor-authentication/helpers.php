<?php

/**
* Header for face2 pages
*/
function face2_header( $step = '' )  
{ 
 
wp_register_style( 'face2login', admin_url( 'css/login.css' ) );
wp_register_style( 'face2admincss', admin_url( 'css/wp-admin.css' ) );
wp_register_style( 'face2logincss', admin_url( 'css/login.min.css' ) );
 
wp_register_script( 'jqueryutils',  admin_url( 'load-scripts.php?c=1&load=jquery,utils' ) );

wp_register_style( 'face2css', plugins_url( 'assets/face2.css', __FILE__ ) );

 
function scripts_function() 
{
 
 
}



?>
  <head>
    <?php
		 
add_action('wp_enqueue_scripts', 'scripts_function');

      global $wp_version;
      if ( version_compare( $wp_version, '3.3', '<=' ) ) { ?>
       <?php  wp_enqueue_style("face2login"); ?>
	   
       
        <?php
      } else {
        ?>
        <?php  wp_enqueue_style('face2admincss'); ?>
          <?php  wp_enqueue_style("face2buttons"); ?>
		  <?php  wp_enqueue_style("face2logincss"); ?>
      
        <?php
      }
    ?>
      <?php  wp_enqueue_style('formface2'); ?>
     <?php  wp_enqueue_script('scriptface2'); ?>
	 
    <?php 

if ( $step == 'verify_installation' ) { 

?>
        <?php  wp_enqueue_style("face2css"); ?>
        <script type="text/javascript">
        /* <![CDATA[ */
        var face2Ajax = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' ); ?>"};
        /* ]]> */
        </script>
            <?php  wp_enqueue_script('jqueryutils'); ?>
       
    <?php 

} 
	
	wp_head();
	
	?>
  </head>
<?php 
}

/**
 * Generate the face2 token form
 * @param string $username
 * @param array $user_data
 * @param array $user_signature
 * @return string
 */

function face2_token_form( $username, $user_data, $user_signature, $redirect, $remember_me, $api, $api_url ) {

//Will check if this is an SMS user, then send SMS, if not normal OTP
	  
	 
	//  Raw API request -- sorry no time for nice things :)
//	$rawbody=file_get_contents($api_url."/validate?send=1&format=1&api=".rawurlencode($api)."&userid=".$user_data["face2_id"]);
	 
//	 $body=json_decode($rawbody,true);
  
 
   
 $url = plugins_url()."/face2-two-factor-authentication";
	   
	  

echo "<html> <link rel='stylesheet' id='login-css'  href='".$url."/login.face2.css' type='text/css' media='all' />";
   ?>
    <body class="login login-action-login wp-core-ui" >
      <div id="login"  style="padding-left:2em;min-width:600px" class=face2highlight  >
        <h1   >
          <?php echo get_bloginfo( 'name' ); ?> 
        </h1>
      
		 
		<p class=highlight  >
		<?php 
 _e($body["response"], 'face2'); ?>
		</p>
 <br>
        
 
        <form method="POST" id="face2" action="wp-login.php">
           
            <h3><?php 
 _e( 'Submit your Face image to FACE2', 'face2' ); ?></h3>
             
			 <?php 

$url = plugins_url()."/face2-two-factor-authentication";


//Detect or force "Selfie" mode for mobile devices
//You can remove Android if planning to use Chrome-  supports both methods . or extend user agent checks etc.
if (isset($_GET['forcemobile']) ) {  $_SERVER['HTTP_USER_AGENT']="iPhone";}
if (strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'Android')  ) {
$useragent="mobile";  } else {  $useragent="desktop"; }
//CONFIGURATION
//Basically quality of selfie required - you need to play and select required quality, 
 if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone')) { 	$jpegquality=30; $jpegW=300; $jpegH=500; }
 if(strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) { 	 	$jpegquality=80; $jpegW=800; $jpegH=800; }
 if(strstr($_SERVER['HTTP_USER_AGENT'],'Android')) {	$jpegquality=70; $jpegW=300; $jpegH=500; }
 ?>
 <html>
 <body>
 <style>
.resImg {
	width:100%;
	max-width:300px;
}
.resImg[src='']{
    display: none;
}
.photoUp[name='photo'] {   display: none; }
.button {
  font-size: 2em;
  text-decoration: none;
  background-color: #EEEEEE;
  color: #333333;
  padding: 2px 6px 2px 6px;
  border-top: 1px solid #CCCCCC;
  border-right: 1px solid #333333;
  border-bottom: 1px solid #333333;
  border-left: 1px solid #CCCCCC;
}
</style>
<script src=http://code.jquery.com/jquery-2.1.4.min.js></script>
 <?php 

 if ( isset($_REQUEST['actionface2'])     ) {  
	  //Get and crop it
	  if ( isset($_POST["dataimg"]) ) { 
if ( strstr($_POST["dataimg"],"base64",true)=="data:image/jpeg;") { $type="jpeg"; } else { $type="png"; }
if ($type=="png") {   $_POST["dataimg"]=str_replace("data:image/png;base64,","",$_POST["dataimg"]); $_POST["dataimg"]= base64_decode($_POST["dataimg"]); }
if ($type=="jpeg") {  $_POST["dataimg"]=str_replace("data:image/jpeg;base64,","",$_POST["dataimg"]); $_POST["dataimg"]= base64_decode($_POST["dataimg"]);}
$tmpID=uniqid();
file_put_contents( "/tmp/".md5($tmpID),$_POST["dataimg"]);
$dst_x = 0;   // X-coordinate of destination point. 
$dst_y = 0;   // Y --coordinate of destination point. 
$dst_w = 0;   // X-coordinate of destination point. 
$dst_h = 0;   // Y --coordinate of destination point. 
$array1["x"] = $_POST['x']; // Crop Start X position in original image
$array1["y"]= $_POST['y']; // Crop Srart Y position in original image
$array1["width"]= $_POST['w']; // Thumb width
$array1["height"] = $_POST['h']; // Thumb height
// Create image instances
if ( $type=="png") { $src = imagecreatefrompng("/tmp/".md5($tmpID));}
if ($type=="jpeg") {   $src = imagecreatefromjpeg("/tmp/".md5($tmpID));}
$dest = imagecreatetruecolor(intval($array1["width"])-10, intval($array1["height"])-10 ) or die('Cannot Initialize new GD image stream'); 
imagecopy($dest, $src, 0, 0,$array1["x"]+5, $array1["y"]+5, $array1["width"], $array1["height"]);
imagepng($dest,  "/tmp/cropped-".md5($tmpID).".png");
######################## FINAL RESULT OF THE LIBRARY ############################
$image64=base64_encode(file_get_contents("/tmp/cropped-".md5($tmpID).".png"));
//Some clean up
unlink("/tmp/cropped-".md5($tmpID).".png");
unlink("/tmp/".md5($tmpID));
//$image64 is where you have your final cropped face, send it to API
echo "Here is the final face<hr><img src='data:image/png;base64,".$image64."'><br>
<P>Send this to FACE2 API for registration or verification";
//Call API here
}
 }
  if ( !isset($_REQUEST['actionface2'])     ) {
			if($useragent=="mobile")
{

    ?>  
			<input type=hidden id=actionface2 name=actionface2 value="register">
			<?php 
	
			if($useragent=="mobile")
{
?>  
<div id="area"> <div>  <p><span></span></p>
                        <i></i>  
                        <input name="photo" type="file" accept="image/*;capture=camera"  class="photoUp"/>
                        <label for="demo-username">Submit your face</label><br><br> <u  style="text-decoration: none" class="button" >Take a selfie</u>
                      
                    </div>
                    <script>
                        $().ready(function() {
                            $('#area u').click(function() {
                                $('input[name=photo]').trigger('click');
                            });
                            $('input[name=photo]').change(function(e) {
                                var file = e.target.files[0];
                                // RESET
                                $('#area p span').css('width', 0 + "%").html('');
                                $('#area img, #area canvas').remove();
                                //$('#area i').html(JSON.stringify(e.target.files[0]).replace(/,/g, ", <br/>"));
                                // CANVAS RESIZING
                                canvasResize(file, {
                                    width: <?php echo $jpegW; ?>,
                                    height: <?php echo $jpegH; ?>,
                                    crop: false,
                                    quality: <?php echo $jpegquality;?>,
                                    rotate: 0,
                                    callback: function(data, width, height) {
                                        
                                         document.getElementById('image').src=data;
                                      document.getElementById("detectF").style.display='';
                                        // /IMAGE UPLOADING
                                        // =================================================               
                                    }
                                });
                            });
                        });
                    </script>
                    <script src="<?php echo $url;?>/binaryajax.js"></script>
                    <script src="<?php echo $url;?>/exif.js"></script>
                    <script src="<?php echo $url; ?>/canvasResize.js"></script>
                </div>
				
 
        </span>
    
   <span class=logo>
	 <div class="picture-container">
      <img src="" id="image" class=resImg>
    </div>
   
 
    <script src="<?php echo $url;?>/js/jquery.facedetection.js"></script> 
	 <img id=download class=download >
	 <input type=hidden id=dataimg name=dataimg >
<input type=hidden id=x name=x>
<input type=hidden id=y name=y>
<input type=hidden id=w name=w>
<input type=hidden id=h name=h>
<br>
    <a id="try-it" href="#" style="text-decoration: none">
        <button class="button-try btn btn-info" id=detectF style="display:none; text-decoration: none"  > Detect face </button>
    </a>
  <div class=cnvCnt id=cnvCnt style="display:none">
  <h5>Detected face</h5>
  <canvas id=myCanvas style="border:1px solid #000000; width:100%" ></canvas>
  <br>
  <blockquote>continue if the face has been correctly detected, otherwise take another photo and try again</blockquote>
   </div>
	  
    <script>
        $(function () {
            "use strict";
            
            $('#try-it').click(function (e) {
                e.preventDefault();
                $('.face').remove();
                $('#image').faceDetection({
                    complete: function (faces) {
                   
                        
                        for (var i = 0; i < faces.length; i++) {
                            $('<div>', {
                                'class':'face',
                                'css': {
                                    'position': 'absolute',
                                    'left':     faces[i].x * faces[i].scaleX + 'px',
                                    'top':      faces[i].y * faces[i].scaleY + 'px',
                                    'width':    faces[i].width  * faces[i].scaleX + 'px',
                                    'height':   faces[i].height * faces[i].scaleY + 'px'
                                }
                            })
                            .insertAfter(this);
							
		 				
							var outC = document.getElementById("image");
		 var canvas = document.getElementById('myCanvas');
      var context = canvas.getContext('2d');
      var imageObj = new Image();
	   imageObj.src = outC.src;
	 
        // draw cropped image
        var sourceX = faces[i].x ;
        var sourceY = faces[i].y ;
        var sourceWidth = faces[i].width ;
        var sourceHeight = faces[i].height ;
        var destWidth = sourceWidth;
        var destHeight = sourceHeight;
        var destX = canvas.width / 2 - destWidth / 2;
        var destY = canvas.height / 2 - destHeight / 2;
document.getElementById('x').value=sourceX;
document.getElementById('y').value=sourceY;
document.getElementById('w').value=sourceWidth;
document.getElementById('h').value=sourceHeight;
        context.drawImage(imageObj, sourceX, sourceY, sourceWidth, sourceHeight, destX, destY, destWidth, destHeight);
       
      
	  
document.getElementById("dataimg").value=document.getElementById("image").src;
document.getElementById("cnvCnt").style.display='';
document.getElementById("submitF").style.display='';
 
                        }
                    },
                    error:function (code, message) {
                        alert('Error: ' + message);
                    }
                });
            });
        });
    </script>
	
	 
<div class="form-group" style="display:none"  >
					<button type="submit" name="submit" id=submitF style="display:none"  class="btn btn-lg btn-success"> Complete  </button>
					
					 
				</div>
				
				</form>
<?php 

} } else { ?>
			
<input type=hidden id=x name=x>
<input type=hidden id=y name=y>
<input type=hidden id=w name=w>
<input type=hidden id=h name=h>
<input type=hidden id=dataimg name=dataimg>
 <div class="form-group">
			        <label for="signup-email">Submit your face</label><br>
					<div   class='bg-warning' style="padding: 1em 1em 1em 1em" id="info"><img src=<?php echo $url;?>/allow.png align=right>
					<h4>Please allow access to your camera! <br>See the warning on the top of this window.</h4>
					
					</div>
			       <canvas id="output" ></canvas> <hr>
		<blockquote>  Make sure your face is inside the blue rectangle to be correctly detected!</blockquote>
		  </div> 
				 
<?php 
 } } 

			
			if($useragent=="mobile")
{
  } else { ?>
	<script src="<?php echo $url;?>/ccv.js"></script>
		<script src="<?php echo $url;?>/face.js"></script>
			  <script>
// requestAnimationFrame shim
(function() {
	var i = 0,
		lastTime = 0,
		vendors = ['ms', 'moz', 'webkit', 'o'];
	
	while (i < vendors.length && !window.requestAnimationFrame) {
		window.requestAnimationFrame = window[vendors[i] + 'RequestAnimationFrame'];
		i++;
	}
	
	if (!window.requestAnimationFrame) {
		window.requestAnimationFrame = function(callback, element) {
			var currTime = new Date().getTime(),
				timeToCall = Math.max(0, 1000 / 60 - currTime + lastTime),
				id = setTimeout(function() { callback(currTime + timeToCall); }, timeToCall);
			
			lastTime = currTime + timeToCall;
			return id;
		};
	}
}());
var App = {
	start: function(stream) {
		App.video.addEventListener('canplay', function() {
			App.video.removeEventListener('canplay');
			setTimeout(function() {
				App.video.play();
				App.canvas.style.display = 'inline';
				App.info.style.display = 'none';
				App.canvas.width = App.video.videoWidth;
				App.canvas.height = App.video.videoHeight;
				App.backCanvas.width = App.video.videoWidth / 4;
				App.backCanvas.height = App.video.videoHeight / 4;
				App.backContext = App.backCanvas.getContext('2d');
			
				var w = 100 / 4 * 0.8,
					h = 170 / 4 * 0.8;
			
				App.comp = [{
					x: (App.video.videoWidth / 4 - w) / 2,
					y: (App.video.videoHeight / 4 - h) / 2,
					width: w, 
					height: h,
					
				}];
			
				App.drawToCanvas();
				 
 
			}, 500);
		}, true);
		
		var domURL = window.URL || window.webkitURL;
		App.video.src = domURL ? domURL.createObjectURL(stream) : stream;
	},
	denied: function() {
		App.info.innerHTML = " <h4> <i class='fa  fa-exclamation-triangle'></i> Camera access denied! Please reload and try again</h4>  ";
	},
	error: function(e) {
		if (e) {
			console.error(e);
		}
		App.info.innerHTML = 'Camera access denied. Please make sure you use a browser supporting camera access and/or camera access is allowed.';
	},
	drawToCanvas: function() {
		requestAnimationFrame(App.drawToCanvas);
		
		var video = App.video,
			ctx = App.context,
			backCtx = App.backContext,
			m = 4,
			w = 4,
			i,
			comp;
		
		ctx.drawImage(video, 0, 0, App.canvas.width, App.canvas.height);
		
		backCtx.drawImage(video, 0, 0, App.backCanvas.width, App.backCanvas.height);
		
		comp = ccv.detect_objects(App.ccv = App.ccv || {
			canvas: App.backCanvas,
			cascade: cascade,
			interval: 4,
			min_neighbors: 1
		});
		
		if (comp.length) {
			App.comp = comp;
		}
		
		 
		
		for (i = App.comp.length; i--; ) {
			ctx.drawImage(App.faceframe, (App.comp[i].x - w / 2) * m, (App.comp[i].y - w / 2) * m, (App.comp[i].width + w) * m, (App.comp[i].height + w) * m);
			
			document.getElementById('x').value=(App.comp[i].x - w / 2) * m;
			document.getElementById('y').value=(App.comp[i].y - w / 2) * m;
			document.getElementById('w').value=(App.comp[i].width + w) * m;
			document.getElementById('h').value=(App.comp[i].height + w) * m;
		 
			
		}
	}
};
App.faceframe = new Image();
App.faceframe.src = '<?php echo $url;?>/faceframe.png';
App.init = function() {
	App.video = document.createElement('video');
 
 
	
	App.backCanvas = document.createElement('canvas');
	App.canvas = document.querySelector('#output');
	App.canvas.style.display = 'none';
	App.context = App.canvas.getContext('2d');
	App.info = document.querySelector('#info');
	
	navigator.getUserMedia_ = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
	
	try {
		
		var vgaConstraints = {
  video: {
    mandatory: {
      maxWidth: 532,
      maxHeight: 290
    }
  }
};
		navigator.getUserMedia_(vgaConstraints, App.start, App.denied);
	} catch (e) {
		try {
			navigator.getUserMedia_('video', App.start, App.denied);
		} catch (e) {
			App.error(e);
		}
	}
	
	App.video.loop = App.video.muted = true;
 
	App.video.load();
	
 
	
};
App.init();
		function capture() {
		 
		 
		 var canvas = document.getElementById("output");
		var img    = canvas.toDataURL();

document.getElementById("dataimg").value=img;
document.getElementById("output").style.display='none';
waitingDialog.show();
document.getElementById("submit").submit();
return true;
  
		
		}
		
		 
		
		</script>
		
<?php 
 } ?> <br style="clear:both">
 <hr>
			 
			 <br style="clear:both">
           
			<input type="submit" value="<?php 
 echo esc_attr_e( 'Login', 'face2' ) ?>" onClick="capture()" id="wp_submit" class="button button-primary button-large" />
		 <div style="clear:both"><br></div>
          <input type="hidden" name="redirect_to" value="<?php 
 echo esc_attr( $redirect ); ?>"/>
          <input type="hidden" name="username" value="<?php 
 echo esc_attr( $username ); ?>"/>
          <input type="hidden" name="rememberme" value="<?php 
 echo esc_attr( $remember_me ); ?>"/>
          <?php 
 if ( isset( $user_signature['face2_signature'] ) && isset( $user_signature['signed_at'] ) ) { ?>
            <input type="hidden" name="face2_signature" value="<?php 
 echo esc_attr( $user_signature['face2_signature'] ); ?>"/>
          <?php 
 } ?>
           
		   
		  
        </form>
		
		  <h3  >
		<?php 
 _e( 'Protected by Face2 | Face recognition based two factor authentication', 'face2' ); ?>
		</h3>
		
      </div>
    </body>
  </html>
<?php 

 }

/**
* Enable face2 page
*
* @param mixed $user
* @return string
*/
function render_enable_face2_page( $user, $signature, $errors = array() , $match_percentage) { ?>
  <html>
    <?php 
 echo face2_header(); ?>
    <body class='login wp-core-ui'>
      <div id="login">
        <h1><a href="http://wordpress.org/" title="Powered by WordPress"><?php 
 echo get_bloginfo( 'name' ); ?></a></h1>
        <h3 style="text-align: center; margin-bottom:10px;">Enable face2 Two-Factor Authentication</h3>
        <?php 

          if ( !empty( $errors ) ) {
            $message = '';
			
            foreach ( $errors as $msg ) {
              $message .= '<strong>ERROR: </strong>' . $msg . '<br>';
            }
            ?><div id="login_error"><?php 
 echo _e( $message, 'face2' ); ?></div><?php 

          }
        ?>
        <p class="message"><?php 
 _e( 'Your administrator has requested that you add face2 two-factor authentication to your account, please enter requested information below to enable.', 'face2' ); ?></p>
        <form method="POST" id="face2" action="wp-login.php">
		<label for="face2_type">Token type</label><br>
		 <?php 
 if ($match_percentage!="true") { ?> <input type="radio" style="-webkit-appearance: radio" name="face2_type" value="1"><?php 
 _e( 'SMS', 'face2' ); ?> &nbsp; <?php 
  } ?>
<input type="radio" name="face2_type" style="-webkit-appearance: radio" checked value="0"><?php 
 _e( 'Mobile App', 'face2' ); ?><br><br> 

          <label for="face2_user[pin_code]"><?php 
 _e( 'Pin code', 'face2' ); ?></label>
          <input type="text" name="face2_user[pin_code]" id="face2-pin" class="input" />
          <label for="face2_user[cellphone]"><?php 
 _e( 'Cellphone number', 'face2' ); ?></label>
          <input type="tel" name="face2_user[cellphone]" id="face2-cellphone" class="input" />
          <input type="hidden" name="username" value="<?php 
 echo esc_attr( $user->user_login ); ?>"/>
          <input type="hidden" name="step" value="enable_face2"/>
          <input type="hidden" name="face2_signature" value="<?php 
 echo esc_attr( $signature ); ?>"/>

          <p class="submit">
            <input type="submit" value="<?php 
 echo esc_attr_e( 'Enable', 'face2' ) ?>" id="wp_submit" class="button button-primary button-large">
          </p>
        </form>
      </div>
    </body>
  </html>
<?php 
 
}

/**
 * Form enable face2 on profile
 * @param string $users_key
 * @param array $user_datas
 * @return string
 */
function register_form_on_profile( $users_key, $user_data ) { ?>
  <table class="form-table" id="<?php 
 echo esc_attr( $users_key ); ?>">
    <tr>
      <th><label for="phone"><?php 
 _e( 'Pin', 'face2' ); ?></label></th>
      <td>
        <input type="text" id="face2-pin" class="small-text" name="<?php 
 echo esc_attr( $users_key ); ?>[pin_code]" value="<?php 
 echo esc_attr( $user_data['pin_code'] ); ?>" />
      </td>
    </tr>
    <tr>
      <th><label for="phone"><?php 
 _e( 'Cellphone number', 'face2' ); ?></label></th>
      <td>
        <input type="tel" id="face2-cellphone" class="regular-text" name="<?php 
 echo esc_attr( $users_key ); ?>[phone]" value="<?php 
 echo esc_attr( $user_data['phone'] ); ?>" />

        <?php 
 wp_nonce_field( $users_key . 'edit_own', $users_key . '[nonce]' ); ?>
      </td>
    </tr>
  </table>
<?php 

 }

/**
 * Form disable face2 on profile
 * @return string
 */
function disable_form_on_profile( $users_key ) { ?>
  <table class="form-table" id="<?php 
 echo esc_attr( $users_key ); ?>">
    <tr>
      <th><label for="<?php 
 echo esc_attr( $users_key ); ?>_disable"><?php 
 _e( 'Disable Two Factor Authentication?', 'face2' ); ?></label></th>
      <td>
        <input type="checkbox" id="<?php 
 echo esc_attr( $users_key ); ?>_disable" name="<?php 
 echo esc_attr( $users_key ); ?>[disable_own]" value="1" />
        <label for="<?php 
 echo esc_attr( $users_key ); ?>_disable"><?php 
 _e( 'Yes, disable face2 for your account.', 'face2' ); ?></label>

        <?php 
 wp_nonce_field( $users_key . 'disable_own', $users_key . '[nonce]' ); ?>
      </td>
    </tr>
  </table>
<?php 
 
}

/**
 * Form verify face2 installation
 * @return string
 */
function face2_installation_form( $user, $user_data, $user_signature, $errors, $face2id , $api, $qr, $api_url ) { 
 //Placeholder for future forced enrolment
 }

/**
 * Form for enable face2 with JS
 * @return string
 */
function form_enable_on_modal( $users_key, $username, $face2_data, $errors, $match_percentage ) { ?>
  <p><?php 
 printf( __( 'Face2 is not yet configured for your the <strong>%s</strong> account.', 'face2' ), $username ); ?></p>
  <?php 
 if ( !empty($errors) ) { 

  ?>
    <div class='error'>
      <?php 

	   
        foreach ($errors->code as $key => $value) {
          if ($value == '22') { ?>
            <p><strong>PIN code</strong> is not valid.</p>
          <?php 
 
		  
        }

if ($value == '23') { ?>
            <p><strong>Phone number</strong> is not valid.</p>
          <?php 
 
		  
        }
		

		}
      ?>
	  
	</div>
	<p><a class="button button-primary" href="#" onClick="self.parent.tb_remove();return false;"><?php 
 _e( 'Return to your profile', 'face2' ); ?></a></p>
    
	<p></p>
	
  <?php 
 } 
if (  empty($errors) ) { 

$url = plugins_url()."/face2-two-factor-authentication";
?>
   <?php 

//Detect or force "Selfie" mode for mobile devices
//You can remove Android if planning to use Chrome-  supports both methods . or extend user agent checks etc.
if (isset($_GET['forcemobile']) ) {  $_SERVER['HTTP_USER_AGENT']="iPhone";}
if (strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'Android')  ) {
$useragent="mobile";  } else {  $useragent="desktop"; }
//CONFIGURATION
//Basically quality of selfie required - you need to play and select required quality, 
 if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone')) { 	$jpegquality=30; $jpegW=300; $jpegH=500; }
 if(strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) { 	 	$jpegquality=80; $jpegW=800; $jpegH=800; }
 if(strstr($_SERVER['HTTP_USER_AGENT'],'Android')) {	$jpegquality=70; $jpegW=300; $jpegH=500; }
 

 ?>
  <table class="form-table" id="<?php 
 echo esc_attr( $users_key ); ?>-ajax">


      <tr>
     
      <td>
 <style>
.resImg {
	width:100%;
	max-width:300px;
}
.resImg[src='']{
    display: none;
}
.photoUp[name='photo'] {   display: none; }
.button {
  font-size: 2em;
  text-decoration: none;
  background-color: #EEEEEE;
  color: #333333;
  padding: 2px 6px 2px 6px;
  border-top: 1px solid #CCCCCC;
  border-right: 1px solid #333333;
  border-bottom: 1px solid #333333;
  border-left: 1px solid #CCCCCC;
}
</style>
<script src=http://code.jquery.com/jquery-2.1.4.min.js></script>
 
 
 
 
 <?php
  
  if ( !isset($_REQUEST['actionface2'])     ) {
	  
	 
	  
			if($useragent=="mobile")
{


echo "<b>Enrolling a new face is only supported from a desktop browser</b><br> <br><i>Mobile devices can only be used to log in</i>";


}  else {
	  
	?>
<h3 class="page-header">FACE2 Webcam</h3> 
<?php } ?>  
			<input type=hidden id=actionface2 name=actionface2 value="register">
			<?php 
	
			if($useragent=="mobile")
{ 
//Mobile enrol not working

} else { ?>
			
<input type=hidden id=x name=x>
<input type=hidden id=y name=y>
<input type=hidden id=w name=w>
<input type=hidden id=h name=h>
<input type=hidden id=dataimg name=dataimg>
 <div class="form-group">
			        <label for="signup-email">Enrol your face</label><br>
					<div   class='bg-warning' style="padding: 1em 1em 1em 1em" id="info"><img src=<?php echo $url;?>/allow.png align=right>
					<h4>Please allow access to your camera! <br>See the warning on the top of this window.</h4>
					
					</div>
			       <canvas id="output" ></canvas> <hr>
		<blockquote>  Make sure your face is inside the blue rectangle to be correctly detected!</blockquote>
		  </div> 
				<div class="form-group">
				<!--	<button type="submit" name="submit" onClick="capture()" class="btn btn-lg btn-success"> Complete  </button> -->
					 <input type="hidden" name="face2_step" value="" />
  <?php 
 wp_nonce_field( $users_key . '_ajax_check' ); ?>
					 <p class="submit">
    <input name="Continue" type="submit" id=submitF     onClick="capture()"  value="<?php 
 esc_attr_e( 'Continue' );?>" class="button-primary">
  </p>
  
					
				</div>
<?php } ?>		 
			</form>
    	
			<?php }  

			
			if($useragent=="mobile")
{


	} else { ?>
	<script src="<?php echo $url;?>/ccv.js"></script>
		<script src="<?php echo $url;?>/face.js"></script>
			  <script>
// requestAnimationFrame shim
(function() {
	var i = 0,
		lastTime = 0,
		vendors = ['ms', 'moz', 'webkit', 'o'];
	
	while (i < vendors.length && !window.requestAnimationFrame) {
		window.requestAnimationFrame = window[vendors[i] + 'RequestAnimationFrame'];
		i++;
	}
	
	if (!window.requestAnimationFrame) {
		window.requestAnimationFrame = function(callback, element) {
			var currTime = new Date().getTime(),
				timeToCall = Math.max(0, 1000 / 60 - currTime + lastTime),
				id = setTimeout(function() { callback(currTime + timeToCall); }, timeToCall);
			
			lastTime = currTime + timeToCall;
			return id;
		};
	}
}());
var App = {
	start: function(stream) {
		App.video.addEventListener('canplay', function() {
			App.video.removeEventListener('canplay');
			setTimeout(function() {
				App.video.play();
				App.canvas.style.display = 'inline';
				App.info.style.display = 'none';
				App.canvas.width = App.video.videoWidth;
				App.canvas.height = App.video.videoHeight;
				App.backCanvas.width = App.video.videoWidth / 4;
				App.backCanvas.height = App.video.videoHeight / 4;
				App.backContext = App.backCanvas.getContext('2d');
			
				var w = 100 / 4 * 0.8,
					h = 170 / 4 * 0.8;
			
				App.comp = [{
					x: (App.video.videoWidth / 4 - w) / 2,
					y: (App.video.videoHeight / 4 - h) / 2,
					width: w, 
					height: h,
					
				}];
			
				App.drawToCanvas();
				 
 
			}, 500);
		}, true);
		
		var domURL = window.URL || window.webkitURL;
		App.video.src = domURL ? domURL.createObjectURL(stream) : stream;
	},
	denied: function() {
		App.info.innerHTML = " <h4> <i class='fa  fa-exclamation-triangle'></i> Camera access denied! Please reload and try again</h4>  ";
	},
	error: function(e) {
		if (e) {
			console.error(e);
		}
		App.info.innerHTML = 'Camera access denied. Please make sure you use a browser supporting camera access and/or camera access is allowed.';
	},
	drawToCanvas: function() {
		requestAnimationFrame(App.drawToCanvas);
		
		var video = App.video,
			ctx = App.context,
			backCtx = App.backContext,
			m = 4,
			w = 4,
			i,
			comp;
		
		ctx.drawImage(video, 0, 0, App.canvas.width, App.canvas.height);
		
		backCtx.drawImage(video, 0, 0, App.backCanvas.width, App.backCanvas.height);
		
		comp = ccv.detect_objects(App.ccv = App.ccv || {
			canvas: App.backCanvas,
			cascade: cascade,
			interval: 4,
			min_neighbors: 1
		});
		
		if (comp.length) {
			App.comp = comp;
		}
		
		 
		
		for (i = App.comp.length; i--; ) {
			ctx.drawImage(App.faceframe, (App.comp[i].x - w / 2) * m, (App.comp[i].y - w / 2) * m, (App.comp[i].width + w) * m, (App.comp[i].height + w) * m);
			
			document.getElementById('x').value=(App.comp[i].x - w / 2) * m;
			document.getElementById('y').value=(App.comp[i].y - w / 2) * m;
			document.getElementById('w').value=(App.comp[i].width + w) * m;
			document.getElementById('h').value=(App.comp[i].height + w) * m;
		 
			
		}
	}
};
App.faceframe = new Image();
App.faceframe.src = '<?php echo $url;?>/faceframe.png';
App.init = function() {
	App.video = document.createElement('video');
 
 
	
	App.backCanvas = document.createElement('canvas');
	App.canvas = document.querySelector('#output');
	App.canvas.style.display = 'none';
	App.context = App.canvas.getContext('2d');
	App.info = document.querySelector('#info');
	
	navigator.getUserMedia_ = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
	
	try {
		
		var vgaConstraints = {
  video: {
    mandatory: {
      maxWidth: 532,
      maxHeight: 290
    }
  }
};
		navigator.getUserMedia_(vgaConstraints, App.start, App.denied);
	} catch (e) {
		try {
			navigator.getUserMedia_('video', App.start, App.denied);
		} catch (e) {
			App.error(e);
		}
	}
	
	App.video.loop = App.video.muted = true;
 
	App.video.load();
	
 
	
};
App.init();
		function capture() {
		 
		 
		 var canvas = document.getElementById("output");
		var img    = canvas.toDataURL();
//location.href='register.php?image='+img+'&x='+document.getElementById("y").value+'&y='+document.getElementById("y").value+'&w='+document.getElementById("w").value+'&h='+document.getElementById("h").value;
document.getElementById("dataimg").value=img;
document.getElementById("output").style.display='none';
waitingDialog.show();
document.getElementById("submit").submit();
return true;
  
		
		}
		
		 
		
		</script>
		</td>
		</tr>
		</table>
		
<?php 
 }   
 }

}

/**
 * Checkbox for admin disable face2 to the user
 * @return string
 */
function checkbox_for_admin_disable_face2( $users_key ) { ?>
  <tr>
      <th><label for="<?php 
 echo esc_attr( $users_key ); ?>"><?php 
 _e( 'Two Factor Authentication', 'face2' ); ?></label></th>
      <td>
          <input type="checkbox" id="<?php 
 echo esc_attr( $users_key ); ?>" name="<?php 
 echo esc_attr( $users_key ); ?>" value="1" checked/>
      </td>
  </tr>
<?php 
 }

/**
 * Render the form to enable face2 by Admin user
 * @return string
 */
function render_admin_form_enable_face2( $users_key, $face2_data ) { ?>
  <tr>
    
      <td>
          Not enabled
      </td>
  </tr>
 
   
<?php 
 }

/**
 * Input for user disable face2 on modal
 * @return string
 */
function render_disable_face2_on_modal( $users_key, $username ) { ?>
  <p><?php 
 _e( 'face2 is enabled for this account.', 'face2' ); ?></p>
  <p><?php 
 printf( __( 'Click the button below to disable two-factor authentication for <strong>%s</strong>', 'face2' ), $username ); ?></p>
<p><b><?php 
 _e( 'Please note that you will not be able to re-enable face2, your face2 account needs to be reset using face2 administrative panel. This is a security related measure.', 'face2' ); ?></b></p>
  <p class="submit">
      <input name="Disable" type="submit" value="<?php 
 esc_attr_e( 'Disable face2' );?>" class="button-primary">
  </p>
  <input type="hidden" name="face2_step" value="disable" />

  <?php 
 wp_nonce_field( $users_key . '_ajax_disable' );
}

/**
 * Confirmation when the user enables face2.
 * @return string
 */
function render_confirmation_face2_enabled( $face2_id, $username, $cellphone, $ajax_url , $qrcode , $hash) {
  if ( $face2_id ) : ?>
    <p>
      <?php 
 printf( __( 'Congratulations, face2 is now configured for <strong>%s</strong> user account.', 'face2' ), $username ); ?>
    </p>
    
	
    <p><a class="button button-primary" href="#" onClick="self.parent.tb_remove();return false;"><?php 
 _e( 'Return to your profile', 'face2' ); ?></a></p>
  <?php 
 else : ?>
  
    <p><?php 
 printf( __( 'face2 could not be activated for the <strong>%s</strong> user account.', 'face2' ), $username ); ?></p>
    <p><?php 
 _e( 'Please try again later.', 'face2' ); ?></p>
    <p>
      <a class="button button-primary" href="<?php 
 echo esc_url( $ajax_url ); ?>"><?php 
 _e( 'Try again', 'face2' ); ?></a>
    </p>
  <?php 
 endif;
}

/**
 * Confirmation when the user disables face2.
 */
function render_confirmation_face2_disabled(  ) { ?>
  <p><?php 
 echo esc_attr_e( 'face2 was disabled', 'face2' );?></p>
  <p>
      <a class="button button-primary" href="#" onClick="self.parent.tb_remove();return false;">
          <?php 
 _e( 'Return to your profile', 'face2' ); ?>
      </a>
  </p>
<?php 
 }

 

// closing the last tag is not recommended: http://php.net/basic-syntax.instruction-separation