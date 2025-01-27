<?php
require('db.inc.php');
$errors = [];
$success = [];

print '<pre>';
print_r($_FILES);
print '</pre>';

$imgUpload = '';

if (isset($_POST['formSubmit'])) {
    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxSize = 1 * 1024 * 1024;
    $errors = array();
    $file = $_FILES['imgupload'];

    // Validatie
    if (!in_array($file['type'], $allowedFileTypes)) {
        $errors[] = "Only JPG, JPEG, or PNG files are allowed.";
    }

    if ($file['error'] !== 0) {
        $errors[] = "Error uploading image: " . $file['error'];
    }

    if ($file['size'] > $maxSize) {
        $errors[] = "File size must be under 1MB.";
    }

    if (!count($errors)) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        $upload_dir = "uploads/";; // aangepast naar src/uploads
        $upload_file = $upload_dir . $newName;

        // Pad opslaan in de database (relatief pad!!)
        $insertId = insertDbImage($upload_file);

        if ($insertId) {
            // Bestand uploaden naar de server
            if (move_uploaded_file($file['tmp_name'], $upload_file)) {
                $success[] = "Image uploaded and saved to database.";
            } else {
                $errors[] = "Failed to upload image.";
            }
        } else {
            $errors[] = "Error saving image to database.";
        }
    }
}

// Haal afbeeldingen op uit de database
$items = getDbImages();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DB Images</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <style>
        img.thumb {
            height: 50px;
        }
    </style>
</head>

<body>

    <div class="container">
        <section>
            <h2>Upload Image</h2>
            <hr />

            <?php if (count($errors)) : ?>
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?= $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (count($success)) : ?>
                <div class="alert alert-success" role="alert">
                    <ul>
                        <?php foreach ($success as $msg) : ?>
                            <li><?= $msg; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php" enctype="multipart/form-data">

                <div class="form-group mt-3">
                    <label for="imgupload" class="col-sm-2 col-form-label">Image: *</label>
                    <div>
                        <input type="file" name="imgupload" id="imgupload">
                    </div>
                </div>

                <div class="form-group mt-5">
                    <div>
                        <button type="submit" class="btn btn-primary" name="formSubmit" style="width: 100%">Add</button>
                    </div>
                </div>
            </form>
        </section>

        <main>
            <h2>Images</h2>
            <div class="table-responsive small">
                <table class="table table-hover table-striped table-sm">
                    <thead>
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Image</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?= $item['id']; ?></td>
                                <td>
                                    <img src="uploads/<?= $item['path']; ?>" class="thumb" />
                                </td>
                                <td><?= $item['created_date']; ?></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>