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
	
	if(isset($_POST['add_method']))
	{
		$name = $db->EscapeString($_POST['name']);
		$info = $db->EscapeString($_POST['info']);
		$minimum = $db->EscapeString($_POST['minimum']);
		
		if(empty($name) || empty($info) || empty($minimum))
		{
			$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> Please complete all fields!</div>';
		}
		else
		{
			$db->Query("INSERT INTO `withdraw_methods` (`method`,`info`,`minimum`) VALUES ('".$name."','".$info."','".$minimum."')");
		
			$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> Withdrawal method was successfully added!</div>';
		}
	}
	
	if(isset($_GET['edit']))
	{
		$id = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `withdraw_methods` WHERE `id` = '".$id."' LIMIT 1");
		
		if(!empty($edit['id']))
		{
			if(isset($_POST['edit_method']))
			{
				$name = $db->EscapeString($_POST['name']);
				$info = $db->EscapeString($_POST['info']);
				$minimum = $db->EscapeString($_POST['minimum']);
				
				if(empty($name) || empty($info) || empty($minimum))
				{
					$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> Please complete all fields!</div>';
				}
				else
				{
					$db->Query("UPDATE `withdraw_methods` SET `method`='".$name."', `info`='".$info."', `minimum`='".$minimum."' WHERE `id`='".$edit['id']."'");
					$edit = $db->QueryFetchArray("SELECT * FROM `withdraw_methods` WHERE `id` = '".$edit['id']."' LIMIT 1");
	
					$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> Withdrawal method was successfully updated!</div>';
				}
			}
		}
	}
	
	if(isset($_GET['delete']) && is_numeric($_GET['delete']))
	{
		$id = $db->EscapeString($_GET['delete']);
		$db->Query("DELETE FROM `withdraw_methods` WHERE `id`='".$id."'");
		$errMessage = '<div class="alert alert-warning"><b>SUCCESS:</b> Withdrawal method was successfully removed!</div>';
	}
?>
<main id="main" class="main">
<div class="pagetitle margin-top">
  <h1>Withdrawal Settings</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item">Withdrawal Settings</li>
	</ol>
  </nav>
</div>
<section class="section">
  <div class="row">
    <?php echo $errMessage; ?>
	<div class="col-lg-6">
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="coins_rate" class="form-label">Coins Value <small>- How many coins for $1</small></label>
			  <input type="text" class="form-control" name="set[coins_rate]" value="<?=$config['coins_rate']?>" id="coins_rate" required>
			</div>
			<div class="text-center">
			  <button type="submit" name="submit" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Payment Methods</h5>
		  <table class="table table-striped table-hover table-sm table-responsive">
			<thead>
			  <tr class="table-dark text-center">
				<th scope="col">#</th>
				<th scope="col">Gateway</th>
				<th scope="col">Min. Withdraw</th>
				<th scope="col"></th>
			  </tr>
			</thead>
            <tbody>
			<?php
				$methods = $db->QueryFetchArrayAll("SELECT * FROM `withdraw_methods` ORDER BY `id` ASC");

				foreach($methods as $method)
				{
					echo '<tr class="text-center">
						<td>'.$method['id'].'</td>
						<td><b>'.$method['method'].'</b></td>
						<td>$'.$method['minimum'].'</td>
						<td>
							<a href="'.GenerateURL('payset&edit='.$method['id'], false, true).'" class="btn btn-sm btn-success m-r-2" title="Edit"><i class="bi bi-pencil"></i></a>
							<a href="'.GenerateURL('payset&delete='.$method['id'], false, true).'" class="btn btn-sm btn-danger m-r-2" title="Delete" onclick="return confirm(\'Are you sure you want to delete this payment method?\');"><i class="bi bi-x-circle-fill"></i></a>
						</td>
						</tr>';
				}
			?>
			</tbody>
          </table>
		</div>
	  </div>
	</div>
	<div class="col-lg-6">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title"><?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'Edit Payment Method #'.$edit['id'] : 'Add Payment Method'); ?></h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="name" class="form-label">Name</label>
			  <input type="text" class="form-control" name="name" value="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['method'] : ''); ?>" id="name" placeholder="Eg. Paypal" required>
			</div>
			<div class="col-12">
			  <label for="info" class="form-label">Payment Info</label>
			  <input type="text" class="form-control" name="info" value="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['info'] : ''); ?>" id="info" placeholder="Eg. Paypal Email Address" required>
			</div>
			<div class="col-12">
			  <label for="minimum" class="form-label">Minimum Withdrawal</label>
			  <input type="text" class="form-control" name="minimum" value="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['minimum'] : ''); ?>" id="minimum" placeholder="2.50" required>
			</div>
			<div class="text-center">
			  <button type="submit" name="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'edit_method' : 'add_method'); ?>" class="btn btn-primary"><?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'Edit' : 'Add'); ?></button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
  </div>
</section>
</main>