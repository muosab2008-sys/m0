<?php
	if(! defined('BASEPATH') ){ exit('Unable to view file.'); }
	
	// Load Sidebar
	require(BASE_PATH.'/template/admin/common/sidebar.php');
	
	$errMessage = '';
	if(isset($_POST['add_faq']))
	{
		$question = $db->EscapeString($_POST['question']);
		$answer = $db->EscapeString($_POST['answer']);

		if(empty($question) || empty($answer))
		{
			$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> Please complete all fields!</div>';
		}
		else
		{
			$db->Query("INSERT INTO `faq` (`question`,`answer`) VALUES ('".$question."','".$answer."')");
		
			$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> F.A.Q was successfully added!</div>';
		}
	}
	
	if(isset($_GET['edit']))
	{
		$id = $db->EscapeString($_GET['edit']);
		$edit = $db->QueryFetchArray("SELECT * FROM `faq` WHERE `id` = '".$id."' LIMIT 1");
		
		if(!empty($edit['id']))
		{
			if(isset($_POST['edit_faq']))
			{
				$question = $db->EscapeString($_POST['question']);
				$answer = $db->EscapeString($_POST['answer']);
				
				if(empty($question) || empty($answer))
				{
					$errMessage = '<div class="alert alert-danger"><b>ERROR:</b> Please complete all fields!</div>';
				}
				else
				{
					$db->Query("UPDATE `faq` SET `question`='".$question."', `answer`='".$answer."' WHERE `id`='".$edit['id']."'");
					$edit = $db->QueryFetchArray("SELECT * FROM `faq` WHERE `id` = '".$edit['id']."' LIMIT 1");
	
					$errMessage = '<div class="alert alert-success"><b>SUCCESS:</b> F.A.Q was successfully updated!</div>';
				}
			}
		}
	}
	
	if(isset($_GET['delete']) && is_numeric($_GET['delete']))
	{
		$id = $db->EscapeString($_GET['delete']);
		$db->Query("DELETE FROM `faq` WHERE `id`='".$id."'");
		$errMessage = '<div class="alert alert-warning"><b>SUCCESS:</b> F.A.Q was successfully removed!</div>';
	}
?>
<main id="main" class="main">
<div class="pagetitle margin-top">
  <h1>Frequently Asked Questions</h1>
  <nav>
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="<?php echo GenerateURL('dashboard', false, true); ?>">Home</a></li>
	  <li class="breadcrumb-item">F.A.Q</li>
	</ol>
  </nav>
</div>
<section class="section">
  <div class="row">
    <?php echo $errMessage; ?>
	<div class="col-lg-6">
	  <div class="card">
		<div class="card-body">
		  <h5 class="card-title">Frequently Asked Questions</h5>
		  <table class="table table-striped table-hover table-sm table-responsive">
			<thead>
			  <tr class="table-dark text-center">
				<th scope="col">#</th>
				<th scope="col">Question</th>
				<th scope="col"></th>
			  </tr>
			</thead>
            <tbody>
			<?php
				$faqs = $db->QueryFetchArrayAll("SELECT * FROM `faq` ORDER BY `id` ASC");

				foreach($faqs as $faq)
				{
					echo '<tr class="text-center">
						<td>'.$faq['id'].'</td>
						<td>'.$faq['question'].'</td>
						<td>
							<a href="'.GenerateURL('faq&edit='.$faq['id'], false, true).'" class="btn btn-sm btn-success m-r-2" title="Edit"><i class="bi bi-pencil"></i></a>
							<a href="'.GenerateURL('faq&delete='.$faq['id'], false, true).'" class="btn btn-sm btn-danger m-r-2" title="Delete" onclick="return confirm(\'Are you sure you want to delete this FAQ?\');"><i class="bi bi-x-circle-fill"></i></a>
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
		  <h5 class="card-title"><?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'Edit FAQ #'.$edit['id'] : 'Add FAQ'); ?></h5>
		  <form method="POST" class="row g-3">
			<div class="col-12">
			  <label for="question" class="form-label">Question</label>
			  <input type="text" class="form-control" name="question" value="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['question'] : ''); ?>" id="question" placeholder="How to do what?" required>
			</div>
			<div class="col-12">
			  <label for="answer" class="form-label">Answer</label>
			  <textarea class="form-control" name="answer" id="answer" placeholder="This is how to do what." required><?php echo (isset($_GET['edit']) && !empty($edit['id']) ? $edit['answer'] : ''); ?></textarea>
			</div>
			<div class="text-center">
			  <button type="submit" name="<?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'edit_faq' : 'add_faq'); ?>" class="btn btn-primary"><?php echo (isset($_GET['edit']) && !empty($edit['id']) ? 'Edit' : 'Add'); ?></button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
  </div>
</section>
</main>