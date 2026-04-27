<?php
if(!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Load Sidebar
require(BASE_PATH.'/template/admin/common/sidebar.php');

// Save photo path
$uploadDir = BASE_PATH . "/assets/img/offers-icon/";
$maxFileSize = 2 * 1024 * 1024; 
$allowedExts = ['jpg','jpeg','png','webp'];

$errorMsg = ""; 

// Photo upload
function uploadLogo($fileInput, $uploadDir, $allowedExts, $maxFileSize) {
    if(empty($_FILES[$fileInput]['name'])) {
        return "";
    }

    $fileName = $_FILES[$fileInput]['name'];
    $fileSize = $_FILES[$fileInput]['size'];
    $fileTmp  = $_FILES[$fileInput]['tmp_name'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if(!in_array($ext, $allowedExts)) {
        return "ERROR_EXT";
    }

    if($fileSize > $maxFileSize) {
        return "ERROR_SIZE";
    }

    $safeName = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", pathinfo($fileName, PATHINFO_FILENAME));
    $newName = $safeName . "_" . time() . "." . $ext;

    $targetPath = $uploadDir . $newName;

    if(move_uploaded_file($fileTmp, $targetPath)) {
        return "assets/img/offers-icon/" . $newName;
    }

    return "ERROR_UPLOAD";
}

// Delete
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $offer = $db->QueryFetchArray("SELECT logo FROM offers WHERE id='".$id."'");

    if($offer && !empty($offer['logo'])) {
        $logoPath = BASE_PATH . '/' . $offer['logo'];
        if(file_exists($logoPath)) {
            unlink($logoPath);
        }
    }

    $db->Query("DELETE FROM offers WHERE id = '".$id."'");
    header("Location: ?page=offers");
    exit;
}

// ADD
if (isset($_POST['add_offer'])) {
    $name = $db->EscapeString($_POST['name']);
    $price = (int)$_POST['price'];
    $url = $db->EscapeString($_POST['url']);
    $desc = $db->EscapeString($_POST['description']);

    $logoPath = uploadLogo('logo', $uploadDir, $allowedExts, $maxFileSize);
    if($logoPath === "ERROR_EXT") {
        $errorMsg = "Allowed file types: JPG, JPEG, PNG, WEBP only.";
    } elseif($logoPath === "ERROR_SIZE") {
        $errorMsg = "File too large. Maximum size 2MB.";
    } elseif($logoPath === "ERROR_UPLOAD") {
        $errorMsg = "Failed to upload file.";
    } else {
        $db->Query("INSERT INTO offers (name, price, url, logo, description) 
                    VALUES ('$name','$price','$url','$logoPath','$desc')");
        header("Location: ?page=offers");
        exit;
    }
}

// EDIT
if(isset($_POST['edit_offer'])) {
    $id = intval($_POST['id']);
    $name = $db->EscapeString($_POST['name']);
    $price = intval($_POST['price']);
    $url = $db->EscapeString($_POST['url']);
    $description = $db->EscapeString($_POST['description']);

    $offer = $db->QueryFetchArray("SELECT logo FROM offers WHERE id='".$id."'");
    $logoPath = $offer['logo'];

    if(!empty($_FILES['logo']['name'])) {
        $newLogo = uploadLogo('logo', $uploadDir, $allowedExts, $maxFileSize);

        if($newLogo === "ERROR_EXT") {
            $errorMsg = "Allowed file types: JPG, JPEG, PNG, WEBP only.";
        } elseif($newLogo === "ERROR_SIZE") {
            $errorMsg = "File too large. Maximum size 2MB.";
        } elseif($newLogo === "ERROR_UPLOAD") {
            $errorMsg = "Failed to upload file.";
        } else {
            if(!empty($logoPath) && file_exists(BASE_PATH.'/'.$logoPath)) {
                unlink(BASE_PATH.'/'.$logoPath);
            }
            $logoPath = $newLogo;
        }
    }

    if(empty($errorMsg)) {
        $db->Query("UPDATE offers SET 
            name='".$name."', 
            price='".$price."', 
            url='".$url."', 
            logo='".$logoPath."', 
            description='".$description."' 
            WHERE id='".$id."'");
        header("Location: ?page=offers");
        exit;
    }
}

// Fetching all offers
$offers = $db->QueryFetchArrayAll("SELECT * FROM offers ORDER BY id DESC");
?>

<div class="container-fluid margin-top" >
    <div class="row">
        <!--  Main-->
        <div class="col-md-9 col-lg-12 px-md-4">

            <h3 class="mb-4">Manage Offers</h3>

            <?php if(!empty($errorMsg)): ?>
                <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
            <?php endif; ?>

           
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                + Add New Offer
            </button>

            
            <div class="card">
                <div class="card-header">All Offers</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>URL</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($offers)): ?>
                                    <tr><td colspan="7" class="text-center">No offers found.</td></tr>
                                <?php else: ?>
                                    <?php foreach($offers as $offer): ?>
                                        <tr>
                                            <td><?php echo $offer['id']; ?></td>
                                            <td>
                                                <?php if(!empty($offer['logo'])): ?>
                                                    <img src="<?php echo $offer['logo']; ?>" alt="logo" style="max-height:30px;">
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($offer['name']); ?></td>
                                            <td><?php echo $offer['price']; ?> coins</td>
                                            <td><a href="<?php echo $offer['url']; ?>" target="_blank">Visit</a></td>
                                            <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                <?php echo htmlspecialchars($offer['description']); ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $offer['id']; ?>">Edit</button>
                                                <a href="admin/page/offers.html?page=offers&delete=<?php echo $offer['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="editModal<?php echo $offer['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <form method="post" enctype="multipart/form-data">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Offer</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?php echo $offer['id']; ?>">
                                                            <div class="row mb-2">
                                                                <div class="col">
                                                                    <input type="text" name="name" value="<?php echo htmlspecialchars($offer['name']); ?>" class="form-control" required>
                                                                </div>
                                                                <div class="col">
                                                                    <input type="number" name="price" value="<?php echo $offer['price']; ?>" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <input type="text" name="url" value="<?php echo htmlspecialchars($offer['url']); ?>" class="form-control" required>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label>Current Logo:</label><br>
                                                                <?php if(!empty($offer['logo'])): ?>
                                                                    <img src="<?php echo $offer['logo']; ?>" alt="logo" style="max-height:40px;">
                                                                <?php endif; ?>
                                                                <input type="file" name="logo" class="form-control mt-2">
                                                            </div>
                                                            <div class="mb-2">
                                                                <textarea name="description" class="form-control"><?php echo htmlspecialchars($offer['description']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" name="edit_offer" class="btn btn-primary">Save changes</button>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="addModal" tabindex="-1" style="padding: 15px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Offer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col">
                            <input type="text" name="name" class="form-control" placeholder="Offer Name" required>
                        </div>
                        <div class="col">
                            <input type="number" name="price" class="form-control" placeholder="Price (coins)" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="url" class="form-control" placeholder="Offer URL" required>
                    </div>
                    <div class="mb-2">
                        <input type="file" name="logo" class="form-control">
                    </div>
                    <div class="mb-2">
                        <textarea name="description" class="form-control" placeholder="Offer Description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_offer" class="btn btn-success">Add Offer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
