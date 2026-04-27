<?php
if (!defined('BASEPATH')) {
  exit('Unable to view file.');
}

$new_activity = $db->QueryFetchArray("SELECT COUNT(*) AS `total` FROM `users_activities` WHERE `user_id`='" . $data['id'] . "' AND `read`='0'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo (empty($page_title) ? $config['site_name'] : $page_title . ' - ' . $config['site_logo']); ?></title>
  <meta content="<?php echo $config['site_description']; ?>" name="description">
  <meta content="<?php echo $config['site_keywords']; ?>" name="keywords">
  <base href="<?php echo $config['secure_url']; ?>">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link rel="preload" as="style" href="/assets/css/fontawesome.css">
  <link rel="preload" as="style" href="/assets/css/flag-icons.css">
  <link rel="stylesheet" href="/assets/css/fontawesome.css" data-navigate-track="reload">
  <link rel="stylesheet" href="/assets/css/flag-icons.css" data-navigate-track="reload">
  <link rel="preload" as="style" href="/assets/css/core-dark.css">
  <link rel="preload" as="style" href="/assets/css/theme-default-dark.css">
  <link rel="preload" as="style" href="/assets/css/demo.css">
  <link rel="stylesheet" href="assets/css/core-dark.css" data-navigate-track="reload">
  <link rel="stylesheet" href="/assets/css/theme-default-dark.css" data-navigate-track="reload">
  <link rel="stylesheet" href="/assets/css/demo.css" data-navigate-track="reload">
  <link rel="preload" as="style" href="/assets/css/perfect-scrollbar.css">
  <link rel="preload" as="style" href="/assets/css/typeahead.css">
  <link rel="stylesheet" href="/assets/css/perfect-scrollbar.css" data-navigate-track="reload">
  <link rel="stylesheet" href="/assets/css/typeahead.css" data-navigate-track="reload">
  <link rel="modulepreload" href="/assets/js/helpers.js">
  <link rel="modulepreload" href="assets/js/config.js">
  <script type="module" src="/assets/js/helpers.js" data-navigate-track="reload"></script>
  <script type="module" src="assets/js/config.js" data-navigate-track="reload"></script>
  <link rel="preload" as="style" href="/assets/css/app-kr.css">
  <link rel="stylesheet" href="/assets/css/app-kr.css" data-navigate-track="reload">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
  <nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="toggle-sidebar-btn layout-menu-toggle layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
        </svg>
      </a>
    </div>
    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

      <?php if($is_online) { ?>
        <ul class="navbar-nav flex-row align-items-center ms-auto">
          <li class="nav-item navbar-dropdown dropdown me-2">
           

          

          <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-between bg-secondary bg-opacity-50 rounded-pill py-1 pe-3 ps-1 text-body"
              href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside">
              <div class="avatar avatar-online text-start">
                <img class="h-auto rounded-circle" src="https://www.gravatar.com/avatar/<?=md5(strtolower(trim($data['email'])))?>?s=40" alt="">
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end text-start f-md">
              <li class="mb-4">
                <div class=" text-center justify-content-center align-items-center m-auto">
                  <div class="avatar avatar-online d-block m-auto mb-2">
                    <img src="https://www.gravatar.com/avatar/<?=md5(strtolower(trim($data['email'])))?>?s=40" alt=""
                      class="h-auto rounded-circle">
                  </div>
                  <div class="d-block">
                    <span class="fw-semibold d-block"><?php echo $data['username']; ?></span>
                  </div>
                </div>
              </li>
              <li>
                <a class="dropdown-item text-body" href="<?=GenerateURL('account')?>">
                  <i class="fa-solid fa-user" style="width: 30px"></i>
                  <span class="align-middle">Profile</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item text-body" href="<?=$config['secure_url']?>/?logout">
                  <i class="fa-solid fa-right-from-bracket" style="width: 30px"></i>
                  <span class="align-middle">Logout</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      <?php } else { ?>
        <ul class="navbar-nav flex-row align-items-center ms-auto">
          <a class="btn bg-primary bg-opacity-50 text-white f-md d-md-block me-2" 
                href ="#">
            <i class="fa-solid fa-arrow-right-to-bracket me-1" style="font-size: 16px"></i>
           Home
</a>
        </ul>
      <?php } ?>
    </div>
  </nav>
