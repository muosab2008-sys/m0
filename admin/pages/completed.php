<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
	
	$page = (isset($_GET['x']) ? $_GET['x'] : 0);
	$limit = 25;
	$start = (is_numeric($page) && $page > 0 ? ($page-1)*$limit : 0);

	$total_pages = $db->QueryGetNumRows("SELECT `id` FROM `completed_offers` WHERE `status`='1'");
	include(BASE_PATH.'/system/libs/paginator.php');

	$urlPattern = GenerateURL('completed&x=(:num)', false, true);
	$paginator = new Paginator($total_pages, $limit, $page, $urlPattern);
	$paginator->setMaxPagesToShow(5);
?> 
<main id="main" class="main">
<div class="pagetitle margin-top">
  <h1>Complete Transactions (<?php echo number_format($total_pages); ?>)</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item active">Complete Transactions</li>
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
				<th scope="col">#</th>
				<th scope="col">User</th>
				<th scope="col">Transaction ID</th>
				<th scope="col">Provider</th>
				<th scope="col">Country</th>
				<th scope="col">Revenue</th>
				<th scope="col">Reward</th>
				<th scope="col">User IP</th>
				<th scope="col">Time</th>
			  </tr>
			</thead>
			<tbody>
				<?php
					$offers = $db->QueryFetchArrayAll("SELECT a.*, b.username, c.country FROM completed_offers a LEFT JOIN users b ON b.id = a.user_id LEFT JOIN list_countries c ON c.code = a.user_country WHERE a.status = '1' ORDER BY a.id DESC LIMIT ".$start.",".$limit);

					if(empty($offers))
					{
						echo '<tr><td colspan="9" class="text-center">There is nothing here yet!</td></tr>';
					}

					foreach($offers as $offer)
					{
						echo '<tr>
							<td>'.$offer['id'].'</td>
							<td><b><a href="'.GenerateURL('edituser&x='.$offer['user_id'], false, true).'">'.$offer['username'].'</a></b></td>
							<td><span class="badge bg-light text-dark">'.$offer['transaction_id'].'</span></td>
							<td><span class="badge bg-light text-dark">'.ucfirst($offer['method']).'</span></td>
							<td>'.(empty($offer['country']) ? 'Unknown' : $offer['country']).'</td>
							<td><span class="badge bg-success">$'.$offer['revenue'].'</span></td>
							<td><span class="badge bg-light text-dark"><img src="/assets/img/coin.png" alt="" width="10" class="me-1"
                  x-show="1">'.$offer['reward'].'</span></td>
							<td><span class="badge bg-light text-dark">'.$offer['user_ip'].'</span></td>
							<td><span class="badge bg-light text-dark">'.date('d M Y H:i', $offer['timestamp']).'</span></td>
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