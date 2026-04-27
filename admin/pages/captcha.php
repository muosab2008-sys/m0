<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
	
	$errMessage = '';
	if(isset($_POST['submit']))
	{
		$posts = $db->EscapeString($_POST['set']);
		foreach ($posts as $key => $value)
		{
			if($config[$key] != $value){
				$db->Query("UPDATE `site_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
				$config[$key] = $value;
			}
		}
		
		$errMessage = '<div class="alert alert-success mt-2"><b>SUCCESS:</b> Settings were successfully changed!</div>';
	}
?>
<main id="main" class="main">
<div class="pagetitle margin-top">
  <h1>Captcha Settings</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item">Captcha Settings</li>
	</ol>
  </nav>
</div>
<section class="section">
  <div class="row">
    <?php echo $errMessage; ?>
	<div class="col-lg-6">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">reCaptcha Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="recaptcha_pub" class="form-label">Recaptcha Public Key</label>
			  <input type="text" class="form-control" name="set[recaptcha_pub]" value="<?=$config['recaptcha_pub']?>" id="recaptcha_pub" required>
			</div>
			<div class="col-12">
			  <label for="recaptcha_sec" class="form-label">Recaptcha Secret Key</label>
			  <input type="text" class="form-control" name="set[recaptcha_sec]" value="<?=$config['recaptcha_sec']?>" id="recaptcha_sec" required>
			</div>
			<div class="text-center">
			  <button type="submit" name="submit" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
	<div class="col-lg-6 margin-top">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Instructions</h5>
		  <p><b>1)</b> <a href="https://www.google.com/recaptcha/admin" target="_blank">Click Here</a>, select <i>reCAPTCHA v2 - Checkbox</i>, complete on <i>Domains</i> with your domain name and click on <i>Register</i></p>
		  <p><b>2)</b> Copy generated "Site Key" and paste this key on "ReCaptcha Site Key"</p>
		  <p><b>3)</b> Copy generated "Secret Key" and paste this key on "ReCaptcha Secret Key"</p>
		  <p><b>4)</b> Press on "Submit" and you're done</p>
		</div>
	  </div>
	</div>
  </div>
</section>
</main>