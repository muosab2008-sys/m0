<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
	
	$errMessage = '';
	if(isset($_GET['delete']) && is_numeric($_GET['delete']))
	{
		$delete = $db->EscapeString($_GET['delete']);
		$db->Query("DELETE FROM `shortlinks_config` WHERE `id`='".$delete."'");
	}
	elseif(isset($_GET['edit']))
	{
		$edit = $db->EscapeString($_GET['edit']);
		$shortlink = $db->QueryFetchArray("SELECT * FROM `shortlinks_config` WHERE `id`='".$edit."'");

		if(isset($_POST['submit']))
		{
			$name = $db->EscapeString($_POST['name']);
			$password = $db->EscapeString($_POST['password']);
			$reward = $db->EscapeString($_POST['reward']);
			$daily_limit = $db->EscapeString($_POST['daily_limit']);
			$status = $db->EscapeString($_POST['status']);
			
			$db->Query("UPDATE `shortlinks_config` SET `name`='".$name."', `password`='".$password."', `reward`='".$reward."', `daily_limit`='".$daily_limit."', `status`='".$status."' WHERE `id`='".$edit."'");
			$shortlink = $db->QueryFetchArray("SELECT * FROM `shortlinks_config` WHERE `id`='".$shortlink['id']."'");
			
			$errMessage = '<div class="alert alert-success"><b>Success!</b> Reward was successfully edited!</div>';
		}
	}

	if(isset($_POST['add_link']))
	{
		$name = $db->EscapeString($_POST['name']);
		$shortlink = $db->EscapeString($_POST['shortlink']);
		$password = $db->EscapeString($_POST['password']);
		$reward = $db->EscapeString($_POST['reward']);
		$daily_limit = $db->EscapeString($_POST['daily_limit']);
	
		if(empty($name) || empty($shortlink) || empty($password) || !is_numeric($reward) || !is_numeric($daily_limit))
		{
			$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> You have to complete all fields!</div>';
		}
		elseif($db->QueryGetNumRows("SELECT `id` FROM `shortlinks_config` WHERE `shortlink`='".$shortlink."' LIMIT 1") > 0)
		{
			$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> This shortlink already exists!</div>';
		}
		else
		{
			$valid = true;
			$getLink = get_data('http://'.$shortlink.'/api?api='.$password.'&url='.urlencode($config['secure_url']).'&alias=CB'.GenerateKey(5));
			if(empty($getLink))
			{
				$valid = false;
			}
			else
			{
				$getLink = json_decode($getLink, true);
				if($getLink['status'] === 'error' || empty($getLink['shortenedUrl']))
				{
					$valid = false;
				}
			}
			
			if(!$valid) 
			{
				$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> This link is not valid or API Token is not correct. Please try again!</div>';
			}
			else
			{
				$db->Query("INSERT INTO `shortlinks_config` (`name`,`shortlink`,`password`,`reward`,`daily_limit`,`status`) VALUES('".$name."', '".$shortlink."', '".$password."', '".$reward."', '".$daily_limit."', '1')");
				$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> Shortlink was successfuly added!</div>';
			}
		}
	}
	
	if(isset($_POST['settings']))
	{
		$shortlink_reset = $db->EscapeString($_POST['shortlink_reset']);
		
		$db->Query("UPDATE `site_config` SET `config_value`='".$shortlink_reset."' WHERE `config_name`='shortlink_reset'");
		$config['shortlink_reset'] = $shortlink_reset;

		$errMessage = '<div class="alert alert-success"><b>Success!</b> Faucet settings were successfully changed!</div>';
	}
	
	if(isset($_GET['edit']) && !empty($shortlink['id']))
	{
?>
<main id="main" class="main">
<div class="pagetitle">
  <h1>Edit Shortlink</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('shortlinks', false, true); ?>">Shortlinks</a></li>
	  <li class="breadcrumb-item">Edit Shortlink</li>
	</ol>
  </nav>
</div>
<section class="section">
  <div class="row">
    <?php echo $errMessage; ?>
	<div class="col-lg-12">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Edit Shortlink</h5>
		  <form method="post" class="row g-3">
			<div class="col-12">
			  <label for="name" class="form-label">Name</label>
			  <input type="text" class="form-control" name="name" value="<?=$shortlink['name']?>" placeholder="ShortLink" id="name" required>
			</div>
			<div class="col-12">
			  <label for="shortlink" class="form-label">Shortlink</label>
			  <input type="text" class="form-control" value="<?=$shortlink['shortlink']?>" id="shortlink" disabled>
			</div>
			<div class="col-12">
			  <label for="password" class="form-label">API Token</label>
			  <input type="text" class="form-control" name="password" value="<?=$shortlink['password']?>" placeholder="bc2f6ffad155c12d6ae8206e29ec3a0e" id="password" required>
			</div>
			<div class="col-12">
			  <label for="reward" class="form-label">Reward</label>
			  <input type="text" class="form-control" name="reward" value="<?=$shortlink['reward']?>" placeholder="25" id="reward" required>
			</div>
			<div class="col-12">
			  <label for="daily_limit" class="form-label">Daily Limit</label>
			  <input type="text" class="form-control" name="daily_limit" value="<?=$shortlink['daily_limit']?>" placeholder="5" id="daily_limit" required>
			</div>
			<div class="col-12">
			  <label for="status" class="form-label">Status</label>
			  <select name="status" id="status" class="form-select">
				<option value="1">Enabled</option>
				<option value="0"<?=($shortlink['status'] == 0 ? ' selected' : '')?>>Disabled</option>
			  </select>
			</div>
			<div class="text-center">
			  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
			</div>
          </form>
		</div>
	  </div>
	</div>
  </div>
</section>
</main>
<?php } else { ?>
<main id="main" class="main">
<div class="pagetitle">
  <h1>Shortlinks</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item">Shortlinks</li>
	</ol>
  </nav>
</div>
<section class="section">
  <div class="row">
    <?php echo $errMessage; ?>
	<div class="col-lg-6">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Add Shortlink</h5>
		  <form method="post" class="row g-3">
			<div class="col-12">
			  <label for="name" class="form-label">Name</label>
			  <input type="text" class="form-control" name="name" placeholder="ShortLink" id="name" required>
			</div>
			<div class="col-12">
			  <label for="shortlink" class="form-label">Shortlink</label>
			  <input type="text" class="form-control" name="shortlink" placeholder="shortlink.com" id="shortlink" required>
			</div>
			<div class="col-12">
			  <label for="password" class="form-label">API Token</label>
			  <input type="text" class="form-control" name="password" placeholder="bc2f6ffad155c12d6ae8206e29ec3a0e" id="password" required>
			</div>
			<div class="col-12">
			  <label for="reward" class="form-label">Reward</label>
			  <input type="text" class="form-control" name="reward" placeholder="25" id="reward" required>
			</div>
			<div class="col-12">
			  <label for="daily_limit" class="form-label">Daily Limit</label>
			  <input type="text" class="form-control" name="daily_limit" placeholder="5" id="daily_limit" required>
			</div>
			<div class="text-center">
			  <button type="submit" name="add_link" class="btn btn-primary">Submit</button>
			</div>
          </form>
		</div>
	  </div>
	</div>
	<div class="col-lg-6">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Settings</h5>
		  <form method="post" class="row g-3 mb-3">
			<div class="col-12">
			  <label for="name" class="form-label">Views Reset</label>
			  <select name="shortlink_reset" class="form-select">
				<option value="0">Midnight</option>
				<option value="1"<?=($config['shortlink_reset'] == 1 ? ' selected' : '')?>>Every 24 hours</option>
			  </select>
			</div>
			<div class="text-center">
			  <button type="submit" name="settings" class="btn btn-primary">Submit</button>
			</div>
          </form>
		  <p><small><b>NOTE 1:</b> Please be aware that shortlinks server time my be different. If shortlinks views are reset every midnight, some views may not be counted by those shortlinks.<br />Eg.: if on this server views are reset at mignight, on shortlink server views may be reset 2 hours later, which means in those 2 hours you may not be rewarded for visits done by your users.</small></p>
		  <p><small><b>NOTE 2:</b> If views reset every 24 hours you may notice a slight decrease of shortlinks activity.</small></p>
		</div>
	  </div>
	</div>
	<div class="col-lg-12">
	  <div class="table-responsive card">
		<table class="table table-striped table-hover table-sm table-responsive-sm text-center m-0">
			<thead>
			  <tr>
				<th scope="col">#</th>
				<th scope="col">Name</th>
				<th scope="col">Shortlink</th>
				<th scope="col">API Token</th>
				<th scope="col">Daily Limit</th>
				<th scope="col">Reward</th>
				<th scope="col">Today Views</th>
				<th scope="col">Total Views</th>
				<th scope="col">Rating</th>
				<th scope="col">Actions</th>
			  </tr>
			</thead>
			<tbody>
				<?php
				  $shortlinks = $db->QueryFetchArrayAll("SELECT * FROM `shortlinks_config` ORDER BY `rating` DESC");
				  
				  foreach($shortlinks as $shortlink)
				  {
				?>	
				<tr>
					<td><?=$shortlink['id']?></td>
					<td><?=($shortlink['status'] == 1 ? '<font color="green">'.$shortlink['name'].'</font>' : '<font color="red">'.$shortlink['name'].'</font>')?></td>
					<td><a href="http://<?=$shortlink['shortlink']?>" target="_blank"><?=$shortlink['shortlink']?></a></td>
					<td><small><?=$shortlink['password']?></small></td>
					<td><?=$shortlink['daily_limit']?> visits</td>
					<td><?=number_format($shortlink['reward'])?> Tokens</td>
					<td><?=number_format($shortlink['today_views'])?> views</td>
					<td><?=number_format($shortlink['total_views'])?> views</td>
					<td><?=number_format($shortlink['rating'])?></td>
					<td class="center">
						<a href="<?php echo GenerateURL('shortlinks&edit='.$shortlink['id'], false, true); ?>" class="btn btn-sm btn-primary m-r-2"><i class="bi bi-pencil-square"></i></a>
						<a href="<?php echo GenerateURL('shortlinks&delete='.$shortlink['id'], false, true); ?>" onclick="return confirm('You sure you want to delete this shortlink?');" class="btn btn-sm btn-danger m-r-2"><i class="bi bi-trash"></i></a>
					</td>
				 </tr>
				<?php }?>
			</tbody>
		</table>
	  </div>
	</div>
  </div>
</section>
</main>
<?php } ?>