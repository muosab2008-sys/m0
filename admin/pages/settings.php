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
		  <h5 class="card-title">General Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-6">
			  <label for="site_logo" class="form-label">Website Name</label>
			  <input type="text" class="form-control" name="set[site_logo]" value="<?=$config['site_logo']?>" id="site_logo" required>
			</div>
			<div class="col-6">
			  <label for="site_name" class="form-label">Website Title</label>
			  <input type="text" class="form-control" name="set[site_name]" value="<?=$config['site_name']?>" id="site_name" required>
			</div>
			<div class="col-12">
			  <label for="site_description" class="form-label">Meta Description</label>
			  <textarea name="set[site_description]" class="form-control" id="site_description" required><?=$config['site_description']?></textarea>
			</div>
			<div class="col-12">
			  <label for="site_keywords" class="form-label">Meta Keywords</label>
			  <textarea name="set[site_keywords]" class="form-control" id="site_keywords" required><?=$config['site_keywords']?></textarea>
			</div>
			<div class="col-6">
			  <label for="site_url" class="form-label">Non-Secure Site URL (HTTP)</label>
			  <input type="text" class="form-control" name="set[site_url]" value="<?=$config['site_url']?>" id="site_url" required>
			</div>
			<div class="col-6">
			  <label for="secure_url" class="form-label">Secure Site URL (HTTPS)</label>
			  <input type="text" class="form-control" name="set[secure_url]" value="<?=$config['secure_url']?>" id="secure_url" required>
			</div>
			<div class="col-12">
			  <label for="force_secure" class="form-label">Redirect non-secure to secure URL</label>
			  <select name="set[force_secure]" class="form-control">
				<option value="0">Disabled</option>
				<option value="1"<?=($config['force_secure'] == 1 ? ' selected' : '')?>>Enabled</option>
			  </select>
			</div>
			<div class="col-12">
			  <label for="mod_rewrite" class="form-label">SEO Friendly URLs</label>
			  <select name="set[mod_rewrite]" class="form-control">
				<option value="0">Disabled</option>
				<option value="1"<?=($config['mod_rewrite'] == 1 ? ' selected' : '')?>>Enabled</option>
			  </select>
			</div>
			<div class="col-12">
			  <label for="analytics_id" class="form-label">Analytics ID</label>
			  <input type="text" class="form-control" name="set[analytics_id]" value="<?=$config['analytics_id']?>" id="analytics_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="submit" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
	<div class="col-lg-6">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Mailing Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-6">
			  <label for="site_email" class="form-label">Contact Email</label>
			  <input type="text" class="form-control" name="set[site_email]" value="<?=$config['site_email']?>" id="site_email" required>
			</div>
			<div class="col-6">
			  <label for="noreply_email" class="form-label">NoReply Email</label>
			  <input type="text" class="form-control" name="set[noreply_email]" value="<?=$config['noreply_email']?>" id="noreply_email" required>
			</div>
			<div class="col-6">
			  <label for="mail_delivery_method" class="form-label">Mail delivery method</label>
			  <select name="set[mail_delivery_method]" class="form-control">
				<option value="0">PHP Mail()</option>
				<option value="1"<?=($config['mail_delivery_method'] == 1 ? ' selected' : '')?>>SMTP</option>
			  </select>
			</div>
			<div class="col-6">
			  <label for="smtp_host" class="form-label">SMTP Host</label>
			  <input type="text" class="form-control" name="set[smtp_host]" value="<?=$config['smtp_host']?>" id="smtp_host" required>
			</div>
			<div class="col-6">
			  <label for="smtp_port" class="form-label">SMTP Port</label>
			  <input type="text" class="form-control" name="set[smtp_port]" value="<?=$config['smtp_port']?>" id="smtp_port" required>
			</div>
			<div class="col-6">
			  <label for="smtp_auth" class="form-label">SMTP Auth</label>
			  <select name="set[smtp_auth]" class="form-control">
				<option value="0">N/A</option>
				<option value="ssl"<?=($config['smtp_auth'] == 'ssl' ? ' selected' : '')?>>SSL</option>
				<option value="tls"<?=($config['smtp_auth'] == 'tls' ? ' selected' : '')?>>TLS</option>
			  </select>
			</div>
			<div class="col-6">
			  <label for="smtp_username" class="form-label">SMTP Username</label>
			  <input type="text" class="form-control" name="set[smtp_username]" value="<?=$config['smtp_username']?>" id="smtp_username" required>
			</div>
			<div class="col-6">
			  <label for="smtp_password" class="form-label">SMTP Password</label>
			  <input type="text" class="form-control" name="set[smtp_password]" value="<?=$config['smtp_password']?>" id="smtp_password" required>
			</div>
			<div class="text-center">
			  <button type="submit" name="submit" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Other Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="more_per_ip" class="form-label">Allow multiple accounts</label>
			  <select name="set[more_per_ip]" class="form-control">
				<option value="0">False</option>
				<option value="1"<?=($config['more_per_ip'] == 1 ? ' selected' : '')?>>True</option>
			  </select>
			</div>
			<div class="col-12">
			  <label for="reg_reqmail" class="form-label">Require email confirmation</label>
			  <select name="set[reg_reqmail]" class="form-control">
				<option value="0">False</option>
				<option value="1"<?=($config['reg_reqmail'] == 1 ? ' selected' : '')?>>True</option>
			  </select>
			</div>
			<div class="col-12">
			  <label for="cron_users" class="form-label">Delete Inactive Users after X days <small>(0 = disabled)</small></label>
			  <input type="text" class="form-control" name="set[cron_users]" value="<?=$config['cron_users']?>" id="cron_users" required>
			</div>
			<div class="text-center">
			  <button type="submit" name="submit" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
  </div>
</section>
</main>