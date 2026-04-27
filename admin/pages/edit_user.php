<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	if(!isset($_GET['x']) || !is_numeric($_GET['x']))
	{
		redirect(GenerateURL('users', false, true));
	}
	else
	{
		$id = $db->EscapeString($_GET['x']);
		$user = $db->QueryFetchArray("SELECT a.*, b.country FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id = '".$id."' LIMIT 1");
	
		if(empty($user['id']))
		{
			redirect(GenerateURL('users', false, true));
		}
	}
	
	if(isset($_POST['update_profile']))
	{
		$username = $db->EscapeString($_POST['username']);
		$email = $db->EscapeString($_POST['email']);
		$gender = $db->EscapeString($_POST['gender']);
		$country = $db->EscapeString($_POST['country']);
		$disabled = $db->EscapeString($_POST['disabled']);
		$admin = $db->EscapeString($_POST['admin']);
		$account_balance = $db->EscapeString($_POST['account_balance']);
		
		if(empty($username) || ($username != $user['username'] && isUserID($username)))
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <b>ERROR:</b> Please complete a valid username!</div>';
		}
		elseif($username != $user['username'] && $db->QueryGetNumRows("SELECT `id` FROM `users` WHERE `username`='".$username."' LIMIT 1") > 0)
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <b>ERROR:</b> This username is already registered!</div>';
		}
		elseif(empty($email) || ($email != $user['email'] && isEmail($email)))
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <b>ERROR:</b> Please complete a valid email address!</div>';
		}
		elseif($email != $user['email'] && $db->QueryGetNumRows("SELECT `id` FROM `users` WHERE `email`='".$email."' LIMIT 1") > 0)
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <b>ERROR:</b> This email address is already registered!</div>';
		}
		else
		{
			$db->Query("UPDATE `users` SET `username`='".$username."', `email`='".$email."', `admin`='".$admin."', `gender`='".$gender."', `disabled`='".$disabled."', `account_balance`='".$account_balance."', `country_id`='".$country."' WHERE `id`='".$user['id']."'");
		
			$user = $db->QueryFetchArray("SELECT a.*, b.country FROM users a LEFT JOIN list_countries b ON b.id = a.country_id WHERE a.id = '".$user['id']."' LIMIT 1");
			$errMessage = '<div class="alert alert-success" role="alert"><i class="fa fa-check-circle"></i> <b>SUCCESS!</b> User informations were successfully updated!</div>';
		}
	}
	
	if(isset($_POST['update_password']))
	{
		$password = $db->EscapeString($_POST['password']);
		$renewpassword = $db->EscapeString($_POST['renewpassword']);
		
		if(!empty($password) && !validatePassword($password))
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <b>ERROR:</b> Password must contain at least 8 characters, 1 lowercase letter, 1 capital letter and 1 number!</div>';
		}
		elseif(!checkPwd($password,$renewpassword))
		{
			$errMessage = '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <b>ERROR:</b> Passwords don\'t match!</div>';
		}
		else
		{
			$password = securePassword($password);
			$db->Query("UPDATE `users` SET `password`='".$password."' WHERE `id`='".$user['id']."'");
		
			$errMessage = '<div class="alert alert-success" role="alert"><i class="fa fa-check-circle"></i> <b>SUCCESS!</b> Password was successfully updated!</div>';
		}
	}
	
	// Stats
	$user_offers = $db->QueryFetchArray("SELECT COUNT(*) AS `total`, SUM(`reward`) AS `coins`, SUM(`revenue`) AS `amount` FROM `completed_offers` WHERE `user_id`='".$user['id']."'");
	$referrals = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `ref`='".$user['id']."'");

	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
?> 
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>User #<?php echo $user['id']; ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo GenerateURL('users', false, true); ?>">Users</a></li>
          <li class="breadcrumb-item active">Edit User</li>
        </ol>
      </nav>
    </div>
    <section class="section profile">
      <div class="row">
        <div class="col-xl-4">
          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
              <img src="https://www.gravatar.com/avatar/<?=md5(strtolower(trim($user['email'])))?>?s=100" alt="Profile" class="rounded-circle">
              <h2><?php echo $user['username']; ?></h2>
              <h3><?php echo ($user['admin'] == 1 ? 'Admininstrator' : 'Member'); ?></h3>
            </div>
          </div>
        </div>
        <div class="col-xl-8">
          <div class="card">
            <div class="card-body pt-3">
              <ul class="nav nav-tabs nav-tabs-bordered">
                <li class="nav-item">
                  <button class="nav-link<?php echo (!isset($_POST['update_profile']) && !isset($_POST['update_password']) ? ' active' : ''); ?>" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-proxy">Proxy IP's</button>
                </li>
                <li class="nav-item">
                  <button class="nav-link<?php echo (isset($_POST['update_profile']) ? ' active' : ''); ?>" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Account</button>
                </li>
                <li class="nav-item">
                  <button class="nav-link<?php echo (isset($_POST['update_password']) ? ' active' : ''); ?>" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>
              </ul>
              <div class="tab-content pt-2" style="margin-top:20px;">
                <div class="tab-pane fade profile-overview<?php echo (!isset($_POST['update_profile']) && !isset($_POST['update_password']) ? ' show active' : ''); ?>" id="profile-overview">
                  <?php
					$proxyChecks = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users_proxy` WHERE `user_id`='".$user['id']."'");
					if($proxyChecks['total'] > 0)
					{
						echo '<div class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i>This user has been found using at least <strong>'.$proxyChecks['total'].' different VPN / Proxy IP Addresses</strong>.</div>';
					}
				  ?>
				  <h5 class="card-title">User Details</h5>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email Address</div>
                    <div class="col-lg-9 col-md-8"><?php echo $user['email']; ?></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Country</div>
                    <div class="col-lg-9 col-md-8"><?php echo $user['country']; ?></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Gender</div>
                    <div class="col-lg-9 col-md-8"><?php echo ($user['gender'] == 2 ? 'Female' : 'Male'); ?></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Account Balance</div>
                    <div class="col-lg-9 col-md-8"><?php echo number_format($user['account_balance'], 2); ?> Coins</div>
                  </div>
				  <h5 class="card-title">Offers Stats</h5>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Completed Offers</div>
                    <div class="col-lg-9 col-md-8"><?php echo number_format($user_offers['total']); ?> Offers</div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Earned From Offers</div>
                    <div class="col-lg-9 col-md-8"><?php echo number_format($user_offers['coins'], 2); ?> Coins</div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Offers Revenue Income</div>
                    <div class="col-lg-9 col-md-8">$<?php echo number_format($user_offers['amount'], 2); ?></div>
                  </div>
				  <h5 class="card-title">Other Details</h5>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Registration Time</div>
                    <div class="col-lg-9 col-md-8"><?php echo date('d M Y - H:i', $user['reg_time']); ?></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Last Activity</div>
                    <div class="col-lg-9 col-md-8"><?php echo date('d M Y - H:i', $user['last_activity']); ?></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Registration IP</div>
                    <div class="col-lg-9 col-md-8"><a href="https://whatismyipaddress.com/ip/<?php echo $user['reg_ip']; ?>" target="_blank"><?php echo $user['reg_ip']; ?></a></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Last IP Used</div>
                    <div class="col-lg-9 col-md-8"><a href="https://whatismyipaddress.com/ip/<?php echo $user['log_ip']; ?>" target="_blank"><?php echo $user['log_ip']; ?></a></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Invited By</div>
                    <div class="col-lg-9 col-md-8"><?php echo (empty($user['ref']) ? 'Nobody' : '<a href="'.GenerateURL('edituser&x='.$user['ref'], false, true).'" target="_blank">#'.$user['ref'].'</a>'); ?></div>
                  </div>
				  <?php if(!empty($user['ref_source'])) { ?>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Referred from</div>
                    <div class="col-lg-9 col-md-8"><a href="<?php echo $user['ref_source']; ?>" target="_blank"><?php echo truncate($user['ref_source'], 100); ?></a></div>
                  </div>
				  <?php } ?>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Referrals</div>
                    <div class="col-lg-9 col-md-8"><?php echo number_format($referrals['total']); ?> referrals</div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Complete Shortlinks</div>
                    <div class="col-lg-9 col-md-8"><?php echo number_format($user['sl_total']); ?> shortlinks (<?php echo number_format($user['sl_today']); ?> today)</div>
                  </div>
                </div>
                <div class="tab-pane fade profile-proxy" id="profile-proxy">
                  <table class="table table-striped table-hover table-sm table-responsive">
					<thead>
					  <tr class="table-dark text-center">
						<th scope="col">IP Address</th>
						<th scope="col">Country</th>
					  </tr>
					</thead>
					<tbody>
						<?php
							$proxies = $db->QueryFetchArrayAll("SELECT * FROM `users_proxy` WHERE `user_id`='".$user['id']."'");

							if(empty($proxies))
							{
								echo '<tr><td colspan="2" class="text-center">There is nothing here yet!</td></tr>';
							}

							foreach($proxies as $proxy)
							{
								echo '<tr class="text-center">
									<td><a href="https://whatismyipaddress.com/ip/'.$proxy['ip_address'].'" target="_blank">'.$proxy['ip_address'].'</a></td>
									<td>'.$proxy['country_code'].'</td>
								  </tr>';
							}
						?>
					  
					</tbody>
				  </table>
                </div>
                <div class="tab-pane fade profile-edit pt-3<?php echo (isset($_POST['update_profile']) ? ' show active' : ''); ?>" id="profile-edit">
				  <?php echo $errMessage; ?>
                  <form method="POST">
                    <div class="row mb-3">
                      <label for="username" class="col-md-4 col-lg-3 col-form-label">Username</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="username" type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="email" class="col-md-4 col-lg-3 col-form-label">Email Address</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="text" class="form-control" id="email" value="<?php echo $user['email']; ?>">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="gender" class="col-md-4 col-lg-3 col-form-label">Gender</label>
                      <div class="col-md-8 col-lg-9">
                        <select name="gender" class="form-control" id="gender">
							<option value="0">-- Select Gender --</option>
							<option value="1"<?php echo ($user['gender'] == 1 ? ' selected' : ''); ?>>Male</option>
							<option value="2"<?php echo ($user['gender'] == 2 ? ' selected' : ''); ?>>Female</option>
						</select>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="country" class="col-md-4 col-lg-3 col-form-label">Country</label>
                      <div class="col-md-8 col-lg-9">
                        <select id="country" class="form-control selectpicker" name="country" data-size="10" data-live-search="true" data-style="btn-white">
							<?php
								echo '<option value="">-- Select Country --</option>';
								
								$countries = $db->QueryFetchArrayAll("SELECT id,country,code FROM `list_countries` ORDER BY country ASC");
								foreach($countries as $country){ 
									echo '<option value="'.$country['id'].'"'.($country['id'] == $user['country_id'] ? ' selected' : '').'>'.$country['country'].'</option>';
								}
							?>
						</select>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="account_balance" class="col-md-4 col-lg-3 col-form-label">Account Balance</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="account_balance" type="text" class="form-control" id="account_balance" value="<?php echo $user['account_balance']; ?>">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="admin" class="col-md-4 col-lg-3 col-form-label">Account Type</label>
                      <div class="col-md-8 col-lg-9">
                        <select name="admin" class="form-control" id="admin">
							<option value="0">Member</option>
							<option value="1"<?php echo ($user['admin'] == 1 ? ' selected' : ''); ?>>Administrator</option>
						</select>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="disabled" class="col-md-4 col-lg-3 col-form-label">Account Status</label>
                      <div class="col-md-8 col-lg-9">
                        <select name="disabled" class="form-control" id="disabled">
							<option value="0">Active</option>
							<option value="1"<?php echo ($user['disabled'] == 1 ? ' selected' : ''); ?>>Disabled</option>
						</select>
                      </div>
                    </div>
                    <div class="text-center">
                      <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form>
                </div>
                <div class="tab-pane fade pt-3<?php echo (isset($_POST['update_password']) ? ' show active' : ''); ?>" id="profile-change-password">
                  <?php echo $errMessage; ?>
				  <form method="POST">
                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                      </div>
                    </div>
                    <div class="text-center">
                      <button type="submit" name="update_password" class="btn btn-primary">Change Password</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>