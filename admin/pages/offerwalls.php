<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
	
	
	// Load offerwall settings
	$ow_config = array();
	$ow_configs = $db->QueryFetchArrayAll("SELECT config_name,config_value FROM `offerwall_config`");
	foreach ($ow_configs as $con)
	{
		$ow_config[$con['config_name']] = $con['config_value'];
	}
	unset($ow_configs);
	
	if(empty($ow_config['adgate_hash']))
	{
		$ow_config['adgate_hash'] = GenerateKey(12);
		$db->Query("UPDATE `offerwall_config` SET `config_value`='".$ow_config['adgate_hash']."' WHERE `config_name`='adgate_hash'");
	}
	
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
	
	if(isset($_POST['edit_offerwall'])){
		$posts = $db->EscapeString($_POST['set2']);
		foreach ($posts as $key => $value){
			if($ow_config[$key] != $value){
				$db->Query("UPDATE `offerwall_config` SET `config_value`='".$value."' WHERE `config_name`='".$key."'");
				$ow_config[$key] = $value;
			}
		}
		
		$errMessage = '<div class="alert alert-success mt-2"><strong>SUCCESS:</strong> Settings were successfully changed!</div>';
	}
?>
<main id="main" class="main">
<div class="pagetitle margin-top">
  <h1>Offerwalls Settings</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item">Offerwalls Settings</li>
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
		  <h5 class="card-title"><a href="https://offerwall.info/?tracking=60c9ac9710619" target="_blank">OfferWall Script</a> Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="offerwall_key" class="form-label">API Key</label>
			  <input type="text" class="form-control" name="set2[offerwall_key]" value="<?=$ow_config['offerwall_key']?>" id="offerwall_key">
			</div>
			<div class="col-12">
			  <label for="offerwall_secret" class="form-label">Secret Key</label>
			  <input type="text" class="form-control" name="set2[offerwall_secret]" value="<?=$ow_config['offerwall_secret']?>" id="offerwall_secret">
			</div>
			<div class="col-12">
			  <label for="offerwall_url" class="form-label">Website URL</label>
			  <input type="text" class="form-control" name="set2[offerwall_url]" value="<?=$ow_config['offerwall_url']?>" id="offerwall_url">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Wannads Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="wannads_key" class="form-label">API Key</label>
			  <input type="text" class="form-control" name="set2[wannads_key]" value="<?=$ow_config['wannads_key']?>" id="wannads_key">
			</div>
			<div class="col-12">
			  <label for="wannads_secret" class="form-label">Secret</label>
			  <input type="text" class="form-control" name="set2[wannads_secret]" value="<?=$ow_config['wannads_secret']?>" id="wannads_secret">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Monlix Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="monlix_api" class="form-label">API Key</label>
			  <input type="text" class="form-control" name="set2[monlix_api]" value="<?=$ow_config['monlix_api']?>" id="monlix_api">
			</div>
			<div class="col-12">
			  <label for="monlix_secret" class="form-label">Secret Key</label>
			  <input type="text" class="form-control" name="set2[monlix_secret]" value="<?=$ow_config['monlix_secret']?>" id="monlix_secret">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">AdGateMedia Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="adgate_id" class="form-label">Code</label>
			  <input type="text" class="form-control" name="set2[adgate_id]" value="<?=$ow_config['adgate_id']?>" id="adgate_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Lootably Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="lootably_id" class="form-label">Placement ID</label>
			  <input type="text" class="form-control" name="set2[lootably_id]" value="<?=$ow_config['lootably_id']?>" id="lootably_id">
			</div>
			<div class="col-12">
			  <label for="lootably_secret" class="form-label">Secret Key</label>
			  <input type="text" class="form-control" name="set2[lootably_secret]" value="<?=$ow_config['lootably_secret']?>" id="lootably_secret">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Admantium Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="admantium_uuid" class="form-label">Uuid</label>
			  <input type="text" class="form-control" name="set2[admantium_uuid]" value="<?=$ow_config['admantium_uuid']?>" id="admantium_uuid">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">OfferToro Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="offertoro_pub" class="form-label">Pub ID</label>
			  <input type="text" class="form-control" name="set2[offertoro_pub]" value="<?=$ow_config['offertoro_pub']?>" id="offertoro_pub">
			</div>
			<div class="col-12">
			  <label for="offertoro_app" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[offertoro_app]" value="<?=$ow_config['offertoro_app']?>" id="offertoro_app">
			</div>
			<div class="col-12">
			  <label for="offertoro_secret" class="form-label">Secret Key</label>
			  <input type="text" class="form-control" name="set2[offertoro_secret]" value="<?=$ow_config['offertoro_secret']?>" id="offertoro_secret">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">CPX Research Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="cpx_id" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[cpx_id]" value="<?=$ow_config['cpx_id']?>" id="cpx_id">
			</div>
			<div class="col-12">
			  <label for="cpx_hash" class="form-label">Security Hash</label>
			  <input type="text" class="form-control" name="set2[cpx_hash]" value="<?=$ow_config['cpx_hash']?>" id="cpx_hash">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Notik Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="notik_api" class="form-label">API Key</label>
			  <input type="text" class="form-control" name="set2[notik_api]" value="<?=$ow_config['notik_api']?>" id="notik_api">
			</div>
			<div class="col-12">
			  <label for="notik_app" class="form-label">APP ID</label>
			  <input type="text" class="form-control" name="set2[notik_app]" value="<?=$ow_config['notik_app']?>" id="notik_app">
			</div>
			<div class="col-12">
			  <label for="notik_secret" class="form-label">APP Secret</label>
			  <input type="text" class="form-control" name="set2[notik_secret]" value="<?=$ow_config['notik_secret']?>" id="notik_secret">
			</div>
			<div class="col-12">
			  <label for="notik_id" class="form-label">Publisher ID</label>
			  <input type="text" class="form-control" name="set2[notik_id]" value="<?=$ow_config['notik_id']?>" id="notik_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Timewall Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="timewall_id" class="form-label">Site ID</label>
			  <input type="text" class="form-control" name="set2[timewall_id]" value="<?=$ow_config['timewall_id']?>" id="timewall_id">
			</div>
			<div class="col-12">
			  <label for="timewall_secret" class="form-label">Secret Key</label>
			  <input type="text" class="form-control" name="set2[timewall_secret]" value="<?=$ow_config['timewall_secret']?>" id="timewall_secret">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Upwall Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="upwall_id" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[upwall_id]" value="<?=$ow_config['upwall_id']?>" id="upwall_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Taskwall Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="taskwall_id" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[taskwall_id]" value="<?=$ow_config['taskwall_id']?>" id="taskwall_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Clickwall Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="clickwall_id" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[clickwall_id]" value="<?=$ow_config['clickwall_id']?>" id="clickwall_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Adtowall Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="adtowall_id" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[adtowall_id]" value="<?=$ow_config['adtowall_id']?>" id="adtowall_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Pubscale Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="pubscale_id" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[pubscale_id]" value="<?=$ow_config['pubscale_id']?>" id="pubscale_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Offery Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="offery_id" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[offery_id]" value="<?=$ow_config['offery_id']?>" id="offery_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Flexwall Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="flexwall_id" class="form-label">App ID</label>
			  <input type="text" class="form-control" name="set2[flexwall_id]" value="<?=$ow_config['flexwall_id']?>" id="offery_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Sushiads Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="sushiads_api" class="form-label">API KEY</label>
			  <input type="text" class="form-control" name="set2[sushiads_api]" value="<?=$ow_config['sushiads_api']?>" id="offery_id">
			</div>
			<div class="text-center">
			  <button type="submit" name="edit_offerwall" class="btn btn-primary">Save</button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
	<div class="col-lg-6">
	  <div class="card" style="
    margin-bottom: 15px;
">
		<div class="card-body">
		  <h5 class="card-title">Settings</h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="hold_days" class="form-label">Hold Days <small>- How many days transaction stays pending</small></label>
			  <input type="text" class="form-control" name="set[hold_days]" value="<?=$config['hold_days']?>" id="hold_days" required>
			</div>
			<div class="col-12">
			  <label for="ref_com" class="form-label">Referral Commission <small>- Percentage earned by referrer</small></label>
			  <input type="text" class="form-control" name="set[ref_com]" value="<?=$config['ref_com']?>" id="ref_com" required>
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
		  <h5 class="card-title">Instructions</h5>
		  <div class="accordion accordion-flush" id="info-group">
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-1" type="button" data-bs-toggle="collapse">
				  OfferWall Script Instructions
				</button>
			  </h2>
			  <div id="info-1" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <div class="alert alert-info">If you purchased CryptoWall script from <a href="https://offerwall.info/?tracking=60c9ac9710619" target="_blank">OfferWall.info</a> you can integrate your offerwall into this system.</div>
				  <p><b>1)</b> Go to your OfferWall website and login.</p>
				  <p><b>2)</b> Go to <i>Add Website</i> and configure your new offer wall as you wish.</p>
				  <p><b>3)</b> When you create your new offer wall, at step 2, complete the Postback URL with:<br />
					<input type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/offerwall.php" onclick="select()" class="form-control" readonly />
				  </p>
				  <p><b>4)</b> At step 3 you will find <i>API Key</i> and <i>Secret Key</i>, required to configure your new offer wall.</p>
				  <p><b>5)</b> Make sure you complete <i>Website URL</i> field with your OfferWall website URL, without trailing slash (Eg. https://yourofferwallsite.com)</p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-2" type="button" data-bs-toggle="collapse">
				  Wannads Instructions
				</button>
			  </h2>
			  <div id="info-2" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p><b>1)</b> Go to <a href="https://publishers.wannads.com/?referral_code=GYW0JE" target="_blank">Wannads</a> and create an account (or login if you're already registered).</p>
				  <p><b>2)</b> Go to <i>Apps</i> -> <i>Create App</i> and make sure you place URL from bellow at <i>Postback URL</i>
					<input type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/wannads.php" onclick="select()" class="form-control" readonly />
				  </p>
				  <p><b>3)</b> When you finish you will find API Key and Secret Key.</p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-3" type="button" data-bs-toggle="collapse">
				  Monlix Instructions
				</button>
			  </h2>
			  <div id="info-3" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p><b>1)</b> Go to <a href="https://publisher.monlix.com/" target="_blank">Monlix.com</a> and create a publisher account (or login if you're already registered).</p>
				  <p><b>2)</b> Go to <i>Sites</i>, click on <i>Add New Site</i> then configure it as you want.</i></p>
				  <p><b>3)</b> Complete <i>Postback</i> using following URL:
					<input type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/monlix.php?userid={{userId}}&transactionid={{transactionId}}&reward={{rewardValue}}&payout={{payout}}&status={{status}}&userip={{userIp}}&country={{countryCode}}&secret={{secretKey}}" onclick="select()" class="form-control" readonly />
				  </p>
				  <p><b>4)</b> After your website gets approved, copy <i>API Key</i> and <i>Secret Key</i> and complete required fields here.</i></p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-4" type="button" data-bs-toggle="collapse">
				  AdGateMedia Instructions
				</button>
			  </h2>
			  <div id="info-4" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p><b>1)</b> Go to <a href="https://adgatemedia.com/" target="_blank">AdGateMedia</a> and create a publisher account (or login if you're already registered).</p>
				  <p><b>2)</b> Go to <i>Monetization Tools</i> -> <i>AdGate Rewards</i> and click on <i>Create AdGate Rewards Wall</i> then configure it as you want.</p>
				  <p><b>3)</b> Complete <i>Postback</i> using following URL:
					<input type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/adgatemedia.php?conversion_id={conversion_id}&user_id={s1}&amount={points}&payout={payout}&offer_title={vc_title}&offer_id={offer_id}&ip={session_ip}&status={status}&hash=<?php echo $ow_config['adgate_hash']; ?>" onclick="select()" class="form-control" readonly />
				  </p>
				  <p><b>4)</b> After you submit your website, you will find <i>Code</i> required to complete fields here.</p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-5" type="button" data-bs-toggle="collapse">
				  Lootably Instructions
				</button>
			  </h2>
			  <div id="info-5" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p><b>1)</b> Go to <a href="https://dashboard.lootably.com/" target="_blank">Lootably</a> and create an account (or login if you're already registered).</p>
				  <p><b>2)</b> Go to <i>Apps / Placements</i> and click on <i>New App / Placement</i></p>
				  <p><b>3)</b> Edit your newly created placement, go to <i>Postback</i> and complete with following postback URL:
					<input type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/lootably.php?user_id={userID}&transaction={transactionID}&ip_address={ip}&payout={revenue}&reward={currencyReward}&name={offerName}&status={status}&hash={hash}" onclick="select()" class="form-control" readonly />
				  </p>
				  <p><b>4)</b> You will find your Placement ID on Apps / Placements main page and Secret Key on <i>Postback</i> section, at the bottom of the page (extract string from Javascript or PHP example).</p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-6" type="button" data-bs-toggle="collapse">
				  Admantium Instructions
				</button>
			  </h2>
			  <div id="info-6" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p><b>1)</b> Go to <a href="https://affiliates.admantium.net/settings" target="_blank">Admantium</a> and create an account (or login if you're already registered)</p>
				  <p><b>2)</b> Go to <i>Monetization tools</i> -> <i>OfferWalls</i> and click on <i>Add New</i></p>
				  <p><b>3)</b> Configure your new offerwall as desired and complete <i>Postback URL</i> with following postback URL:
					<input type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/admantium.php?transaction_id={transaction_id}&payout={payout}&status={status}&session_ip={session_ip}&user_id={user_id}&reward={reward}&offer_name={offer_name}&offer_id={offer_id}" onclick="select()" class="form-control" readonly />
				  </p>
				  <p><b>4)</b> After you finish, you will find your <i>Uuid</i> at <i>Monetization toold</i> -> <i>OfferWalls</i> page.</p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-7" type="button" data-bs-toggle="collapse">
				  OfferToro Instructions
				</button>
			  </h2>
			  <div id="info-7" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p><b>1)</b> Go to <a href="https://www.offertoro.com" target="_blank">OfferToro</a> and create an account (or login if you're already registered).</p>
				  <p><b>2)</b> Go to <i>App Placements</i>, click on <i>Add App</i> and configure your new offer wall as you wish.</p>
				  <p><b>3)</b> When you create your new offer wall, complete <i>Postback URL</i> with:<br />
					<input type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/offertoro.php?id={id}&oid={oid}&amount={amount}&user_id={user_id}&ip_address={ip_address}&payout={payout}" onclick="select()" class="form-control" readonly />
				  </p>
				  <p><b>4)</b> After you create your app, click on <i>Edit</i> to find required informations.</p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-8" type="button" data-bs-toggle="collapse">
				  CPX Research Instructions
				</button>
			  </h2>
			  <div id="info-8" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p>1) <a href="https://publisher.cpx-research.com/?advertiser_id=5627" target="_blank"><b>Click here</b></a> and create an account (or login if you're already registered).</p>
				  <p>2) Click on <i>Apps</i> -> <i>My Apps</i> -> <i>Add new App</i> and create your new app.</p>
				  <p>3) After you create your app, click on <i>Edit</i> -> <i>Postback Settings</i> and complete with following URL:</p>
				  <p>
					<input type="text" class="form-control" value="<?php echo $config['secure_url']; ?>/system/gateways/cpx.php?status={status}&trans_id={trans_id}&user_id={user_id}&sub_id={subid}&sub_id_2={subid_2}&amount_local={amount_local}&amount_usd={amount_usd}&offer_id={offer_ID}&hash={secure_hash}&ip_click={ip_click}" onclick="this.select()" readonly />
				  </p>
				  <p> 4) Complete fields with your APP ID and Security Hash.</p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-9" type="button" data-bs-toggle="collapse">
				  Notik Instructions
				</button>
			  </h2>
			  <div id="info-9" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p>1) <a href="https://notik.me/?ref_code=03njzs" target="_blank">Click here</a> and create an account (or login if you're already registered).</p>
				  <p>2) Go to <i>App Settings</i> and make sure you place URL from bellow at <i>Postback URL</i>:</p>
				  <p>
					<input class="form-control" type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/notik.php" onclick="this.select()" readonly />
				  </p>
				  <p>3) When you finish you will find API Key, Secret Key and Publisher ID on <i>Offer API</i> page.</p>
				</div>
			  </div>
			</div>
			<div class="accordion-item">
			  <h2 class="accordion-header">
				<button class="accordion-button collapsed" data-bs-target="#info-10" type="button" data-bs-toggle="collapse">
				  Timewall Instructions
				</button>
			  </h2>
			  <div id="info-10" class="accordion-collapse collapse" data-bs-parent="#info-group">
				<div class="accordion-body">
				  <p>1) <a href="https://siteowner.timewall.io/" target="_blank">Click here</a> and create an account (or login if you're already registered).</p>
				  <p>2) Go to <i>Placements</i> and click on <i>Add Placement</i>.</p>
				  <p>3) Configure your placement as desired and make sure you copy <b>secret key</b> from the bottom of the page. Also, make sure you place URL from bellow into <i>Postback URL</i> field:</p>
				  <p>
					<input class="form-control" type="text" value="<?php echo $config['secure_url']; ?>/system/gateways/timewall.php?user_id={userID}&transaction={transactionID}&revenue={revenue}&reward={currencyAmount}&hash={hash}&ip={ip}" onclick="this.select()" readonly />
				  </p>
				  <p>4) When you finish you will find your Site ID on <i>Placements</i> page.</p>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</section>
</main>