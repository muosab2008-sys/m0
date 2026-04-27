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
  <h1>Settings</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item">Settings</li>
	</ol>
  </nav>
</div>
<section class="section">
  <div class="row">
    <?php echo $errMessage; ?>
	<div class="col-lg-6">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Proxycheck Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="proxycheck" class="form-label">Proxycheck API Key</label>
			  <input type="text" class="form-control" name="set[proxycheck]" value="<?=$config['proxycheck']?>" id="proxycheck" required>
			</div>
			<div class="col-12">
			  <label for="proxycheck_status" class="form-label">Proxycheck Status</label>
			  <select name="set[proxycheck_status]" class="form-control">
				<option value="0">Disabled</option>
				<option value="1"<?=($config['proxycheck_status'] == 1 ? ' selected' : '')?>>Enabled</option>
			  </select>
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
		  <p>1) Go to <a href="https://proxycheck.io/" target="_blank">ProxyCheck.io</a> and signup using your email address</p>
		  <p>2) Complete <i>API Key</i> field with api key received by email from ProxyCheck.io</p>
		  <p><small><a href="https://proxycheck.io/" target="_blank">ProxyCheck.io</a> provides 1000 free queries / day. If you have more than 1000 active users daily, we recommend to upgrade from free plan do another plan who fits your requirements.</small></p>
		</div>
	  </div>
	</div>
  </div>
</section>
</main>