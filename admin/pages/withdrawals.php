<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');

	$page = (isset($_GET['x']) ? $_GET['x'] : 0);
	$limit = 25;
	$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

	$total_pages = $db->QueryGetNumRows("SELECT `id` FROM `withdrawals` WHERE `status`!='0'");
	include(BASE_PATH.'/system/libs/paginator.php');

	$urlPattern = GenerateURL('withdrawals&x=(:num)', false, true);
	$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
	$paginator->setMaxPagesToShow(5);
?>
<main id="main" class="main">
<div class="pagetitle margin-top">
  <h1>Complete Withdrawals (<?php echo number_format($total_pages); ?>)</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item">Withdrawals</li>
	  <li class="breadcrumb-item active">Complete</li>
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
					<th>Coins</th>
					<th>Value</th>
					<th>Method</th>
					<th>Payment Info</th>
					<th>User IP</th>
					<th>Status</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$requests = $db->QueryFetchArrayAll("SELECT a.*, b.username FROM withdrawals a LEFT JOIN users b ON b.id = a.user_id WHERE a.status != '0' ORDER BY a.id DESC LIMIT ".$start.",".$limit);
					
					if(count($requests) == 0)
					{
						echo '<td colspan="8"><center>There is nothing here yet!</center></td>';
					}
					else
					{
						foreach($requests as $request) 
						{
							echo '<tr>
								<td>'.$request['id'].'</td>
								<td><b><a href="'.GenerateURL('edituser&x='.$request['user_id'], false, true).'">'.$request['username'].'</a></b></td>
								<td><span class="badge bg-light text-dark"><img src="/assets/img/coin.png" alt="" width="10" class="me-1"
                  x-show="1">'.number_format($request['coins']).'</span></td>
								<td><span class="badge bg-success">$'.number_format($request['amount'], 2).'</span></td>
								<td><span class="badge bg-light text-dark">'.$request['method_name'].'</span></td>
								<td><span class="badge bg-dark text-light">'.$request['payment_info'].'</span></td>
								<td><span class="badge bg-light text-dark">'.$request['ip_address'].'</span></td>
								<td>'.($request['status'] == 1 ? '<i class="bi bi-patch-check-fill text-success" title="Sent"></i>' : ($request['status'] == 3 ? '<i class="bi bi-arrow-repeat text-warning" data-toggle="tooltip" data-placement="top" title="Refunded"></i>' : '<i class="bi bi-x-octagon-fill text-danger" data-toggle="tooltip" data-placement="top" title="Rejected"></i>')).'</td>
								<td><span class="badge bg-light text-dark">'.date('d M Y - H:i', $request['time']).'</span></td>
							</tr>';
						}
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