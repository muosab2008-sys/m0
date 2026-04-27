<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
	
	$errMessage = '';
	if(isset($_POST['add_task']))
	{
		$title = $db->EscapeString($_POST['title']);
		$description = htmlentities($_POST['description'], ENT_QUOTES);
		$requirement = $db->EscapeString($_POST['requirement']);
		$url_required = $db->EscapeString($_POST['url_required']);
		$reward = $db->EscapeString($_POST['reward']);

		if(empty($title) || empty($description) || empty($requirement) || empty($reward))
		{
			$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> Please complete all fields!</div>';
		}
		else
		{
			$db->Query("INSERT INTO `jobs` (`title`,`description`,`requirement`,`url_required`,`reward`,`time`) VALUES ('".$title."','".$description."','".$requirement."','".$url_required."','".$reward."','".time()."')");
		
			$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> Task was successfully added!</div>';
		}
	}
	
	if(isset($_GET['edit']))
	{
		$id = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `jobs` WHERE `id` = '".$id."' LIMIT 1");
		
		if(!empty($edit['id']))
		{
			if(isset($_POST['edit_task']))
			{
				$title = $db->EscapeString($_POST['title']);
				$description = htmlentities($_POST['description'], ENT_QUOTES);
				$requirement = $db->EscapeString($_POST['requirement']);
				$url_required = $db->EscapeString($_POST['url_required']);
				$reward = $db->EscapeString($_POST['reward']);
				
				if(empty($title) || empty($description) || empty($requirement) || empty($reward))
				{
					$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> Please complete all fields!</div>';
				}
				else
				{
					$db->Query("UPDATE `jobs` SET `title`='".$title."', `description`='".$description."', `requirement`='".$requirement."', `url_required`='".$url_required."', `reward`='".$reward."' WHERE `id`='".$edit['id']."'");
					$edit = $db->QueryFetchArray("SELECT * FROM `jobs` WHERE `id`='".$edit['id']."' LIMIT 1");
	
					$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> Task was successfully updated!</div>';
				}
			}
		}
	}
	
	if(isset($_GET['delete']) && is_numeric($_GET['delete']))
	{
		$id = $db->EscapeString($_GET['delete']);
		$db->Query("DELETE FROM `jobs` WHERE `id`='".$id."'");
		$db->Query("DELETE FROM `jobs_done` WHERE `job_id`='".$id."'");
		$errMessage = '<div class="alert alert-warning"><b>SUCCESS:</b> Task was successfully removed!</div>';
	}
?>
<main id="main" class="main">
<div class="pagetitle">
  <h1>Manage Tasks</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item">Manage Tasks</li>
	</ol>
  </nav>
</div>
<section class="section">
  <div class="row">
    <?php echo $errMessage; ?>
	<div class="col-lg-6">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Tasks</h5>
		  <table class="table table-striped table-hover table-sm table-responsive">
			<thead>
			  <tr class="table-dark text-center">
				<th scope="col">#</th>
				<th scope="col">Task</th>
				<th scope="col">Reward</th>
				<th scope="col"></th>
			  </tr>
			</thead>
            <tbody>
			<?php
				$jobs = $db->QueryFetchArrayAll("SELECT * FROM `jobs` ORDER BY `id` ASC");

				foreach($jobs as $job)
				{
					echo '<tr class="text-center">
						<td>'.$job['id'].'</td>
						<td>'.$job['title'].'</td>
						<td>'.$job['reward'].' coins</td>
						<td>
							<a href="'.GenerateURL('tasks&edit='.$job['id'], false, true).'" class="btn btn-sm btn-success m-r-2" title="Edit"><i class="bi bi-pencil"></i></a>
							<a href="'.GenerateURL('tasks&delete='.$job['id'], false, true).'" class="btn btn-sm btn-danger m-r-2" title="Delete" onclick="return confirm(\'Are you sure you want to delete this Task?\');"><i class="bi bi-x-circle-fill"></i></a>
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
		  <h5 class="card-title"><?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'Edit Task #'.$edit['id'] : 'Add Task'); ?></h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="title" class="form-label">Title</label>
			  <input type="text" class="form-control" name="title" value="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['title'] : ''); ?>" id="title" placeholder="Tasks title goes here" required>
			</div>
			<div class="col-12">
			  <label for="description" class="form-label">Description &amp; Requirements</label>
			  <textarea class="tinymce-editor" name="description" id="description"><?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['description'] : ''); ?></textarea>
			</div>
			<div class="col-12">
			  <label for="reward" class="form-label">Reward</label>
			  <input type="text" class="form-control" name="reward" value="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['reward'] : ''); ?>" id="reward" placeholder="250.00" required>
			</div>
			<div class="col-12">
			  <label for="requirement" class="form-label">Requirement</label>
			  <input type="text" class="form-control" name="requirement" value="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['requirement'] : ''); ?>" id="requirement" placeholder="URL of photo proof" required>
			</div>
			<div class="col-12">
			  <label for="url_required" class="form-label">URL Required</label>
			  <select class="form-control" name="url_required" id="url_required"><option value="0">No</option><option value="1"<?php echo (isset($_GET['edit']) && !empty($edit['id']) && $edit['url_required'] == 1 ? ' selected' : ''); ?>>Yes</option></select>
			</div>
			<div class="text-center">
			  <button type="submit" name="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'edit_task' : 'add_task'); ?>" class="btn btn-primary"><?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'Edit' : 'Add'); ?></button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
  </div>
</section>
</main>