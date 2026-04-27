<?php
if (!defined('BASEPATH')) {
    exit('Unable to view file.');
}

// Load Sidebar
require(BASE_PATH.'/template/admin/common/sidebar.php');

//  Handle Add/Edit
if(isset($_POST['save'])){
    $rank_position = intval($_POST['rank_position']);
    $prize = $db->EscapeString($_POST['prize']);
    $id = intval($_POST['id']);

    // Check if prize already exists for this rank (except current ID if edit)
    $check = $db->QueryFetchArray("SELECT id FROM rank_prizes WHERE rank_position='$rank_position' AND id != '$id' LIMIT 1");
    if($check){
        echo "<script>alert('A prize already exists for this rank!');window.location.href='admin/page/admin_rank_prizes.html';</script>";
        exit;
    } else {
        if($id > 0) {
            // Update
            $db->Query("UPDATE rank_prizes SET rank_position='$rank_position', prize='$prize' WHERE id='$id'");
        } else {
            // Insert
            $db->Query("INSERT INTO rank_prizes (rank_position, prize, status) VALUES ('$rank_position', '$prize', 1)");
        }
        echo "<script>window.location.href='admin/page/admin_rank_prizes.html';</script>";
        exit;
    }
}

//  Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $db->Query("DELETE FROM rank_prizes WHERE id='$id'");
    echo "<script>window.location.href='admin/page/admin_rank_prizes.html';</script>";
    exit;
}

//  Toggle (activate/deactivate)
if(isset($_GET['toggle'])){
    $id = intval($_GET['toggle']);
    $db->Query("UPDATE rank_prizes SET status = IF(status=1,0,1) WHERE id='$id'");
    echo "<script>window.location.href='admin/page/admin_rank_prizes.html';</script>";
    exit;
}

// ✅ Fetch prizes
$prizes = $db->QueryFetchArrayAll("SELECT * FROM rank_prizes ORDER BY rank_position ASC");

?>
<div class="bg-b-csm" style="background-image: url('/assets/img/background-1.webp')"></div>
<div class="content-wrapper" style="margin-top: 120px;">
    <h2 class="text-center mt-3">Rank Prizes Management</h2>

    <!-- Add/Edit Form -->
    <form method="POST" class="text-center mb-4" style="max-width:500px;margin:auto;">
        <input type="hidden" name="id" value="<?= isset($_GET['edit']) ? intval($_GET['edit']) : 0 ?>">
        <div class="form-group">
            <label>Rank Position</label>
            <input type="number" name="rank_position" class="form-control" 
                   value="<?php if(isset($_GET['edit'])){ 
                       $editId = intval($_GET['edit']); 
                       $row = $db->QueryFetchArray("SELECT * FROM rank_prizes WHERE id='$editId'");
                       if($row){ echo $row['rank_position']; } 
                   } ?>" required>
        </div>
        <div class="form-group">
            <label>Prize</label>
            <input type="text" name="prize" class="form-control" 
                   value="<?php if(!empty($row)){ echo htmlspecialchars($row['prize'], ENT_QUOTES); } ?>" required>
        </div>
        <button type="submit" name="save" class="btn btn-success mt-2">Save</button>
    </form>

    <!-- Prizes Table -->
    <table class="table table-bordered table-striped text-center" style="width:80%;margin:20px auto;">
        <thead class="thead-dark">
            <tr>
                <th>Rank</th>
                <th>Prize</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($prizes)): ?>
                <?php foreach($prizes as $p): ?>
                    <tr>
                        <td><?= $p['rank_position'] ?></td>
                        <td><?= htmlspecialchars($p['prize']) ?></td>
                        <td>
                            <?php if($p['status'] == 1): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="admin/page/admin_rank_prizes.html?edit=<?= $p['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="admin/page/admin_rank_prizes.html?toggle=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Toggle</a>
                            <a href="admin/page/admin_rank_prizes.html?delete=<?= $p['id'] ?>" onclick="return confirm('Are you sure?');" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No prizes added</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
