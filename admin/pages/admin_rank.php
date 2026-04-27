<?php
if (!defined('BASEPATH')) {
    exit('Unable to view file.');
}

// Load Sidebar
require(BASE_PATH.'/template/admin/common/sidebar.php');

//  Handle Add/Edit
if(isset($_POST['save'])){
    $id = intval($_POST['id']);
    $title = $db->EscapeString($_POST['title']);
    $start_time = strtotime($_POST['start_time']);
    $end_time = strtotime($_POST['end_time']);

    if($id > 0){
        // Update
        $db->Query("UPDATE rank_sessions SET title='$title', start_time='$start_time', end_time='$end_time' WHERE id='$id'");
    } else {
        // Insert
        $db->Query("INSERT INTO rank_sessions (title, start_time, end_time, status) VALUES ('$title', '$start_time', '$end_time', 1)");
    }
    echo "<script>window.location.href='admin/page/admin_rank.html';</script>";
    exit;
}

//  Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $db->Query("DELETE FROM rank_sessions WHERE id='$id'");
    echo "<script>window.location.href='admin/page/admin_rank.html';</script>";
    exit;
}

//  Toggle
if(isset($_GET['toggle'])){
    $id = intval($_GET['toggle']);
    $db->Query("UPDATE rank_sessions SET status = IF(status=1,0,1) WHERE id='$id'");
    echo "<script>window.location.href='admin/page/admin_rank.html';</script>";
    exit;
}

//  Fetch all sessions
$sessions = $db->QueryFetchArrayAll("SELECT * FROM rank_sessions ORDER BY id DESC");

// For Edit
$editRow = null;
if(isset($_GET['edit'])){
    $editId = intval($_GET['edit']);
    $editRow = $db->QueryFetchArray("SELECT * FROM rank_sessions WHERE id='$editId'");
}
?>
<div class="bg-b-csm" style="background-image: url('/assets/img/background-1.webp')"></div>
<div class="content-wrapper" style="margin-top: 120px;">
    <h2 class="text-center mt-3">Sessions Management</h2>

    <!-- Add/Edit Form -->
    <form method="POST" class="text-center mb-4" style="max-width:500px;margin:auto;">
        <input type="hidden" name="id" value="<?= $editRow ? $editRow['id'] : 0 ?>">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" 
                   value="<?= $editRow ? htmlspecialchars($editRow['title'], ENT_QUOTES) : '' ?>" required>
        </div>
        <div class="form-group">
            <label>Start Time</label>
            <input type="datetime-local" name="start_time" class="form-control" 
                   value="<?= $editRow ? date('Y-m-d\TH:i', $editRow['start_time']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label>End Time</label>
            <input type="datetime-local" name="end_time" class="form-control" 
                   value="<?= $editRow && $editRow['end_time'] > 0 ? date('Y-m-d\TH:i', $editRow['end_time']) : '' ?>" required>
        </div>
        <button type="submit" name="save" class="btn btn-success mt-2">Save</button>
    </form>

    <!-- Sessions Table -->
    <table class="table table-bordered table-striped text-center" style="width:90%;margin:20px auto;">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($sessions)): ?>
                <?php foreach($sessions as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['title']) ?></td>
                        <td><?= date("Y-m-d h:i a", $s['start_time']) ?></td>
                        <td><?= $s['end_time'] > 0 ? date("Y-m-d h:i a", $s['end_time']) : '-' ?></td>
                        <td>
                            <?php if($s['status'] == 1): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="admin/page/admin_rank.html?edit=<?= $s['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="admin/page/admin_rank.html?toggle=<?= $s['id'] ?>" class="btn btn-warning btn-sm">Toggle</a>
                            <a href="admin/page/admin_rank.html?delete=<?= $s['id'] ?>" onclick="return confirm('Are you sure?');" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No sessions found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
