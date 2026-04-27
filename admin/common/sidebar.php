<?php
if (!defined('BASEPATH')) {
  exit('Unable to view file.');
}

$pendingJobs = $db->QueryFetchArray("SELECT `id` FROM `jobs_done` WHERE `status`='0' LIMIT 1");
$pendingPay = $db->QueryFetchArray("SELECT `id` FROM `withdrawals` WHERE `status`='0' LIMIT 1");

$job_alert = '';
if (!empty($pendingJobs['id'])) {
  $job_alert = ' <i class="bi bi-exclamation-triangle-fill text-warning"></i>';
}

$pay_alert = '';
if (!empty($pendingPay['id'])) {
  $pay_alert = ' <i class="bi bi-exclamation-triangle-fill text-warning"></i>';
}
?>
<aside id="sidebar" class=" admin-sidebar" style="overflow: auto; scrollbar-width: none;">
  <div class="app-brand demo">
    <a href="#" class="app-brand-link" style="
">

      <span class="app-brand-text demo menu-text fw-bold">
        <img src="assets/img/logo.png" alt="Logo" style="width: 186px;height: 67px;">
      </span>
    </a>

    <a href="javascript:void(0);" class="toggle-sidebar-btn layout-menu-toggle menu-link text-large ms-auto">
      <i class="fa-regular fa-circle  d-none align-middle"></i>
      <svg width="24" height="24" class="d-block d-xl-none align-middle" xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
      </svg> </a>


  </div>
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link<?php echo (!isset($_GET['page']) ? '' : ' collapsed'); ?>"
        href="<?php echo GenerateURL('dashboard', false, true); ?>">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-heading">Users</li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && in_array($_GET['page'], array('users', 'affiliates')) ? '' : ' collapsed'); ?>"
        data-bs-target="#leaderboard-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-x-diamond-fill"></i><span>Users</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="leaderboard-nav"
        class="nav-content collapse<?php echo (isset($_GET['page']) && in_array($_GET['page'], array('users', 'affiliates')) ? ' show' : ''); ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo GenerateURL('users', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'users' && !isset($_GET['y']) ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>All users</span>
          </a>
        </li>
        <li>
          <a href="<?php echo GenerateURL('users&y=today', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'users' && isset($_GET['y']) ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>Registered Today</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && in_array($_GET['page'], array('completed', 'pending', 'rejected', 'canceled')) ? '' : ' collapsed'); ?>"
        data-bs-target="#trans-nav" data-bs-toggle="collapse" href="#">
        <i class="bx bx-list-ul"></i><span>Transactions</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="trans-nav"
        class="nav-content collapse<?php echo (isset($_GET['page']) && in_array($_GET['page'], array('completed', 'pending', 'rejected', 'canceled')) ? ' show' : ''); ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo GenerateURL('completed', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'completed' ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>Complete</span>
          </a>
        </li>
        <li>
          <a href="<?php echo GenerateURL('pending', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'pending' ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>Pending</span>
          </a>
        </li>
        <li>
          <a href="<?php echo GenerateURL('rejected', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'rejected' ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>Rejected</span>
          </a>
        </li>
        <li>
          <a href="<?php echo GenerateURL('canceled', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'canceled' ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>Canceled</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && in_array($_GET['page'], array('withdrawals', 'pendingwithdrawals')) ? '' : ' collapsed'); ?>"
        data-bs-target="#rewards-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-credit-card-fill"></i><span>Withdrawals<?php echo $pay_alert; ?></span><i
          class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="rewards-nav"
        class="nav-content collapse<?php echo (isset($_GET['page']) && in_array($_GET['page'], array('withdrawals', 'pendingwithdrawals')) ? ' show' : ''); ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo GenerateURL('withdrawals', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'withdrawals' ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>Complete</span>
          </a>
        </li>
        <li>
          <a href="<?php echo GenerateURL('pendingwithdrawals', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'pendingwithdrawals' ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>Pending</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-heading">Website</li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && in_array($_GET['page'], array('admin_rank', 'admin_rank_prizes')) ? '' : ' collapsed'); ?>"
        data-bs-target="#earn-nav" data-bs-toggle="collapse" href="#">
        <i class="menu-icon fas fa-ranking-star"></i><span>Leaderboard</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="earn-nav"
        class="nav-content collapse<?php echo (isset($_GET['page']) && in_array($_GET['page'], array('admin_rank', 'admin_rank_prizes')) ? ' show' : ''); ?>"
        data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo GenerateURL('admin_rank', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_rank' && !isset($_GET['y']) ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span> Manage Leaderboard</span>
          </a>
        </li>
        <li>
          <a href="<?php echo GenerateURL('admin_rank_prizes', false, true); ?>" <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin_rank_prizes' && isset($_GET['y']) ? ' class="active"' : ''); ?>>
            <i class="bi bi-circle"></i><span>Manage Leaderboard prizes</span>
          </a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && $_GET['page'] == 'faq' ? '' : ' collapsed'); ?>"
        href="<?= GenerateURL('faq', false, true) ?>">
        <i class="bi bi-patch-question-fill"></i>
        <span>F.A.Q.</span>
      </a>
    </li>
    <li class="nav-heading">Settings</li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && $_GET['page'] == 'settings' ? '' : ' collapsed'); ?>"
        href="<?= GenerateURL('settings', false, true) ?>">
        <i class="bi bi-gear-fill"></i>
        <span>Website Settings</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && $_GET['page'] == 'proxy' ? '' : ' collapsed'); ?>"
        href="<?= GenerateURL('proxy', false, true) ?>">
        <i class="bi bi-gear-fill"></i>
        <span>Proxycheck Settings</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && $_GET['page'] == 'captcha' ? '' : ' collapsed'); ?>"
        href="<?= GenerateURL('captcha', false, true) ?>">
        <i class="bi bi-gear-fill"></i>
        <span>Captcha Settings</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && $_GET['page'] == 'offerwalls' ? '' : ' collapsed'); ?>"
        href="<?= GenerateURL('offers', false, true) ?>">
        <i class="bi bi-gear-fill"></i>
        <span>featured offers Settings</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && $_GET['page'] == 'offerwalls' ? '' : ' collapsed'); ?>"
        href="<?= GenerateURL('offerwalls', false, true) ?>">
        <i class="bi bi-gear-fill"></i>
        <span>Offerwalls Settings</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link<?php echo (isset($_GET['page']) && $_GET['page'] == 'payset' ? '' : ' collapsed'); ?>"
        href="<?= GenerateURL('payset', false, true) ?>">
        <i class="bi bi-gear-fill"></i>
        <span>Withdrawal Settings</span>
      </a>
    </li>
  </ul>
</aside>