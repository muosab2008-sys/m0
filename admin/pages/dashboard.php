<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }

	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
	
	// Page Data
	$leads = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `completed_offers`");
	$todayLeads = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `completed_offers` WHERE `timestamp`>='".strtotime(date('d M Y'))."'");
	$users = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users`");
	$todayUsers = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users` WHERE `reg_time`>='".strtotime(date('d M Y'))."'");
	$withdrawals = $db->QueryFetchArray("SELECT SUM(`amount`) AS `total` FROM `withdrawals` WHERE `status`='1'");
	$pendingWithdrawals = $db->QueryFetchArray("SELECT SUM(`amount`) AS `total` FROM `withdrawals` WHERE `status`='0'");
	$stats_leads = $db->QueryFetchArrayAll("SELECT COUNT(*) AS `total`, DATE(FROM_UNIXTIME(`timestamp`)) AS `day` FROM `completed_offers` GROUP BY `day` ORDER BY `day` DESC LIMIT 7");
	$offersRevenue = $db->QueryFetchArray("SELECT SUM(`revenue`) AS `total` FROM `completed_offers` WHERE `status`='0' OR `status`='1'");
	$offersCRevenue = $db->QueryFetchArray("SELECT SUM(`revenue`) AS `total` FROM `completed_offers` WHERE `status`>'1'");
	$shortlinks = $db->QueryFetchArray("SELECT SUM(`sl_total`) AS `total`, SUM(`sl_today`) AS `today` FROM `users`");

	$dates = array();
	$graphDates = array();
	for ($i = 0; $i < 7; $i++) {
		$dates[] = date('Y-m-d', time() - 86400 * $i);
		$graphDates[] = '"'.date('Y-m-d', time() - 86400 * $i).'"';
	}
	$dates = array_reverse($dates);
	$graphDates = array_reverse($graphDates);
	$graphDates = implode(',', $graphDates);
	
	$dailyLeads = array();
	foreach($dates as $date) {
		$result = 0;
		foreach($stats_leads as $stat) {
			if($date == $stat['day']) {
				$result = $stat['total'];
			}
		}

		$dailyLeads[] = $result;
	}
	
	$dailyLeads = implode(',', $dailyLeads);
?>
  <main id="main" class="main">
    <div class="pagetitle margin-top">
      <h1>Dashboard</h1>
    </div>
    <section class="section dashboard">
      <div class="row">
		<div class="col-md-6" style="
    margin-bottom: 17px;
"> 
		  <div class="card info-card sales-card">
			<div class="card-body" >
			  <h5 class="card-title">Total Users</h5>
			  <div class="d-flex align-items-center">
				<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
				  <i class="bi bi-people"></i>
				</div>
				<div class="ps-3">
				  <h6><?php echo number_format($users['total']); ?></h6>
				  <span class="text-success small pt-2 ps-1"><b>+<?php echo number_format($todayUsers['total']); ?> today</b></span>
				</div>
			  </div>
			</div>
		  </div>
		</div>
		<div class="col-md-6" style="
    margin-bottom: 17px;
">
		  <div class="card info-card customers-card">
			<div class="card-body">
			  <h5 class="card-title">Total Withdrawn</h5>
			  <div class="d-flex align-items-center">
				<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
				  <i class="bi bi-credit-card-fill"></i>
				</div>
				<div class="ps-3">
				  <h6>$<?php echo number_format($withdrawals['total'], 2); ?></h6>
				  <span class="text-warning small pt-2 ps-1"><b>$<?php echo number_format($pendingWithdrawals['total'], 2); ?> pending</b></span>
				</div>
			  </div>
			</div>
		  </div>
		</div>
		<div class="col-xxl-4 col-md-6" style="
    margin-bottom: 17px;
">
		  <div class="card info-card revenue-card">
			<div class="card-body">
			  <h5 class="card-title">Completed Offers</h5>
			  <div class="d-flex align-items-center">
				<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
				  <i class="bx bx-list-ul"></i>
				</div>
				<div class="ps-3">
				  <h6><?php echo number_format($leads['total']); ?></h6>
				  <span class="text-success small pt-2 ps-1"><b>+<?php echo number_format($todayLeads['total']); ?> today</b></span>
				</div>
			  </div>
			</div>
		  </div>
		</div>
		<div class="col-xxl-4 col-md-6" style="
    margin-bottom: 17px;
">
		  <div class="card info-card revenue-card" >
			<div class="card-body">
			  <h5 class="card-title">Completed Shortlinks</h5>
			  <div class="d-flex align-items-center">
				<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
				  <i class="bi bi-box-arrow-up-right"></i>
				</div>
				<div class="ps-3">
				  <h6><?php echo number_format($shortlinks['total']); ?></h6>
				  <span class="text-success small pt-2 ps-1"><b>+<?php echo number_format($shortlinks['today']); ?> today</b></span>
				</div>
			  </div>
			</div>
		  </div>
		</div>
		<div class="col-xxl-4 col-md-12" style="
    margin-bottom: 17px;
">
		  <div class="card info-card sales-card" >
			<div class="card-body">
			  <h5 class="card-title">Offers Revenue</span></h5>
			  <div class="d-flex align-items-center">
				<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
				  <i class="bx bx-list-ul"></i>
				</div>
				<div class="ps-3">
				  <h6>$<?php echo number_format($offersRevenue['total'], 2); ?></h6>
				  <span class="text-danger small pt-2 ps-1"><b>$<?php echo number_format($offersCRevenue['total'], 2); ?> canceled</b></span>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	  <div class="row" >
        <div class="col-lg-8">
          <div class="row">
            <div class="col-12" style="
    margin-bottom: 17px;
">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Completed Offers <span>| Reports</span></h5>
                  <div id="reportsChart"></div>
                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Completed Offers',
                          data: [<?php echo $dailyLeads; ?>],
                        }],
                        chart: {
                          height: 364,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                        },
                        markers: {
                          size: 4
                        },
                        colors: ['#4154f1'],
                        fill: {
                          type: "gradient",
                          gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100]
                          }
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2
                        },
                        xaxis: {
                          type: 'datetime',
                          categories: [<?php echo $graphDates; ?>]
                        },
                        tooltip: {
                          x: {
                            format: 'dd/MM/yy'
                          },
                        }
                      }).render();
                    });
                  </script>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4" style="
    margin-bottom: 17px;
">
		  <?php
			$leads = $db->QueryFetchArrayAll("SELECT COUNT(*) AS `total`, `status` FROM `completed_offers` GROUP BY `status`");
			
			$graph = array();
			foreach($leads as $lead)
			{
				$graph[$lead['status']] = $lead['total'];
			}
			
			$pendingLeads = (isset($graph[0]) ? $graph[0] : 0);
			$approvedLeads = (isset($graph[1]) ? $graph[1] : 0);
			$chargebackLeads = (isset($graph[3]) ? $graph[3] : 0);
			$rejectedLeads = (isset($graph[2]) ? $graph[2] : 0);
			$rejectedLeads = $rejectedLeads + (isset($graph[4]) ? $graph[4] : 0);
		  ?>
          <div class="card">
            <div class="card-body pb-0">
              <h5 class="card-title">Total Transactions <span>| Reports</span></h5>
              <div id="trafficChart" style="min-height: 400px;" class="echart"></div>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  echarts.init(document.querySelector("#trafficChart")).setOption({
                    tooltip: {
                      trigger: 'item'
                    },
                    legend: {
                      top: '5%',
                      left: 'center'
                    },
                    series: [{
                      name: 'Transactions',
                      type: 'pie',
                      radius: ['40%', '70%'],
                      avoidLabelOverlap: false,
                      label: {
                        show: false,
                        position: 'center'
                      },
                      emphasis: {
                        label: {
                          show: true,
                          fontSize: '18',
                          fontWeight: 'bold'
                        }
                      },
                      labelLine: {
                        show: false
                      },
                      data: [{
                          value: <?php echo $pendingLeads; ?>,
                          name: 'Pending'
                        },{
                          value: <?php echo $approvedLeads; ?>,
                          name: 'Approved'
                        },
                        {
                          value: <?php echo $rejectedLeads; ?>,
                          name: 'Rejected'
                        },
                        {
                          value: <?php echo $chargebackLeads; ?>,
                          name: 'Chargeback'
                        }
                      ]
                    }]
                  });
                });
              </script>
            </div>
          </div>
        </div>
		<div class="col-12">
		  <div class="card recent-sales overflow-auto">
			<div class="card-body">
			  <h5 class="card-title">Past 10 Transactions</h5>

			  <table class="table table-borderless table-sm table-responsive text-center">
				<thead>
				  <tr>
					<th scope="col">#</th>
                    <th scope="col">User</th>
					<th scope="col">Provider</th>
					<th scope="col">Offer</th>
                    <th scope="col">Revenue</th>
					<th scope="col">Reward</th>
					<th scope="col">Status</th>
				  </tr>
				</thead>
				<tbody>
					<?php
						$offers = $db->QueryFetchArrayAll("SELECT a.*, b.username FROM completed_offers a LEFT JOIN users b ON b.id = a.user_id ORDER BY a.id DESC LIMIT 10");
						$status = array(0 => '<span class="badge bg-info">Pending</span>', 1 => '<span class="badge bg-success">Approved</span>', 2 => '<span class="badge bg-danger">Rejected</span>', 3 => '<span class="badge bg-warning">Chargeback</span>', 4 => '<span class="badge bg-danger">Proxy / VPN</span>');
						
						foreach($offers as $offer)
						{
							echo '<tr>
								<td><b>'.$offer['id'].'</b></td>
								<td><b><a href="'.GenerateURL('edituser&x='.$offer['user_id'], false, true).'">'.$offer['username'].'</a></b></td>
								<td>'.strtoupper($offer['method']).'</td>
								<td><b class="text-primary">'.(empty($offer['campaign_name']) ? 'Unknown' : $offer['campaign_name']).'</b></td>
								<td><span class="badge bg-success">$'.$offer['revenue'].'</span></td>
								<td>'.$offer['reward'].' coins</td>
								<td>'.$status[$offer['status']].'</td>
							  </tr>';
						}
					?>
				</tbody>
			  </table>
			</div>
		  </div>
		</div>
      </div>
    </section>
  </main>