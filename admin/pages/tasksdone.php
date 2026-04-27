<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
	
	$errMessage = '';
	if(isset($_GET['confirm'])  && is_numeric($_GET['confirm'])){
		$job_id = $db->EscapeString($_GET['confirm']);
		$job = $db->QueryFetchArray("SELECT * FROM `jobs_done` WHERE `id`='".$job_id."' AND `status`= '0' LIMIT 1");
		
		if(!empty($job['uid'])){
			$db->Query("UPDATE `users` SET `account_balance`=`account_balance`+'".$job['reward']."' WHERE `id`='".$job['uid']."'");
			$db->Query("UPDATE `jobs_done` SET `status`='1' WHERE `id`='".$job_id."'");
			
			$notify_value = serialize(array('id' => $job['uid'], 'reward' => $job['reward']));
			add_activity($job['uid'], 6, $notify_value);

			$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> Task was successfully approved!</div>';
		}
	}
	elseif(isset($_GET['reject'])  && is_numeric($_GET['reject']))
	{
		$job_id = $db->EscapeString($_GET['reject']);
		$db->Query("UPDATE `jobs_done` SET `status`='2' WHERE `id`='".$job_id."'");

		$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> Task was successfully rejected!</div>';
	}
	
	$page = (isset($_GET['x']) ? $_GET['x'] : 0);
	$limit = 25;
	$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

	$pagination = '';
	$page_title = 'Completed Tasks';
	$total_query = " WHERE `status`!='0'"; 
	$select_query = " WHERE a.status != '0'";
	if(isset($_GET['y']) && $_GET['y'] == 'pending')
	{
		$pagination = '&y=pending';
		$page_title = 'Pending Tasks';
		$total_query = " WHERE `status`='0'";
		$select_query = " WHERE a.status = '0'";
	}

	$total_pages = $db->QueryGetNumRows("SELECT `id` FROM `jobs_done`".$total_query);
	include(BASE_PATH.'/system/libs/paginator.php');

	$urlPattern = GenerateURL('tasksdone&x=(:num)'.$pagination, false, true);
	$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
	$paginator->setMaxPagesToShow(5);
?> 
<main id="main" class="main">
<div class="pagetitle">
  <h1><?php echo $page_title; ?> (<?php echo number_format($total_pages); ?>)</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item active"><?php echo $page_title; ?></li>
	</ol>
  </nav>
</div>
<section class="section">
  <div class="row">
	<div class="col-lg-12">
	  <div class="table-responsive card">
		<table class="table table-striped table-hover table-sm table-responsive-sm text-center m-0">
			<thead>
			  <tr>
				<th>#</th>
				<th>User</th>
				<th>Job ID</th>
				<th>Job Requirement</th>
				<th>Reward</th>
				<th>Status</th>
				<th>Time</th>
				<th></th>
			  </tr>
			</thead>
			<tbody>
				<?php
					$tasks = $db->QueryFetchArrayAll("SELECT a.*, b.username FROM jobs_done a LEFT JOIN users b ON b.id = a.uid".$select_query." ORDER BY a.time DESC LIMIT ".$start.",".$limit);

					if(empty($tasks))
					{
						echo '<tr><td colspan="8" class="text-center">There is nothing here yet!</td></tr>';
					}

					foreach($tasks as $task)
					{
						echo '<tr>
							<td>'.$task['id'].'</td>
							<td><a href="'.GenerateURL('edituser&x='.$task['uid'], false, true).'">'.$task['username'].'</a></td>
							<td><a href="'.GenerateURL('tasks&edit='.$task['job_id'], false, true).'">'.$task['job_id'].'</a></td>
							<td><span class="badge bg-light text-dark">'.$task['requirement'].'</span></td>
							<td>'.$task['reward'].' coins</td>
							<td>'.($task['status'] == 2 ? '<span class="badge bg-danger">Rejected</span>' : ($task['status'] == 1 ? '<span class="badge bg-success">Complete</span>' : '<span class="badge bg-info">Pending</span>')).'</span></td>
							<td>'.date('d M Y H:i', $task['time']).'</td>
							<td>'.($task['status'] == 0 ? '<a href="'.GenerateURL('tasksdone&confirm='.$task['id'].$pagination, false, true).'" class="btn btn-sm btn-success m-r-2" title="Approve" onclick="return confirm(\'Are you sure you want to approve this Task?\');"><i class="bi bi-check-circle-fill"></i></a> <a href="'.GenerateURL('tasksdone&reject='.$task['id'].$pagination, false, true).'" class="btn btn-sm btn-danger m-r-2" title="Reject" onclick="return confirm(\'Are you sure you want to reject this Task?\');"><i class="bi bi-x-circle-fill"></i></a>' : '').'</td>
						  </tr>';
					}
				?>
			</tbody>
		  </table>
		  <?php if($total_pages > $limit){ ?>
			<nav class="m-3">
				<ul class="pagination justify-content-center mb-0">
				<?php 
					if ($paginator->getPrevUrl()) {
						echo '<li class="page-item"><a class="page-link" href="'.$paginator->getPrevUrl().'">&laquo; Previous</a></li>';
					} else {
						echo '<li class="page-item disabled"><a class="page-link" href="#">&laquo; Previous</a><li>';
					}

					foreach ($paginator->getPages() as $page) {
						if ($page['url']) {
							echo '<li class="page-item'.($page['isCurrent'] ? ' active' : '').'"><a class="page-link" href="'. $page['url'].'">'.$page['num'].'</a></li>';
						} else {
							echo '<li class="page-item disabled"><a class="page-link" href="#">'.$page['num'].'</a></li>';
						}
					}

					if ($paginator->getNextUrl()) {
						echo '<li class="page-item"><a class="page-link" href="'.$paginator->getNextUrl().'">Next &raquo;</a></li>';
					}
				?>
				</ul>
			</nav>
		  <?php } ?>
	  </div>
    </div>
  </div>
</section>
</main>