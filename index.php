<?php
    session_start([
        'cookie_lifetime' => 86400,
        'read_and_close' => true
    ]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PHP image resize application.</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/global.css">
</head>

<body>
    <?php
        require 'vendor/autoload.php';
        
        use app\Resize;
        use app\Upload;
        
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $upload = new Upload($_FILES['image'], new Resize($_POST['width'], $_POST['height']));
            $upload->setAllowedExtensions(['png', 'jpg', 'jpeg', 'gif']);
            $upload->setUploadDirectory('./assets/upload');
            $upload->setMaxFileSize(350000); // ~ 3.5mb
            $upload->persist();
        }
        
    ?>

    <div class="container">
        <div class="mt-4 p-4 card shadow-sm border-0">
            <h2 class="app-header mb-4">Image resize</h2>
            <?php if(isset($_SESSION['upload_error']) && !empty($_SESSION['upload_error'])): ?>
                <div class="alert alert-warning"><?php echo getFromSession('upload_error'); ?></div>
            <?php endif; ?>
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                <div class="custom-file mb-4">
                    <input type="file" name="image[]" class="custom-file-input">
                    <label class="custom-file-label" for="image">Choose file</label>
                </div>
                <div class="form-group row">
                    <label for="width" class="col-3">Width <small>(px)</small></label>
                    <div class="col-9">
                        <input type="number" name="width" id="width" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="width" class="col-3">Heigh <small>(px)</small></label>
                    <div class="col-9">
                        <input type="number" name="height" id="height" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        <?php if(isset($_SESSION['resized']) && !empty($_SESSION['resized'])): ?>
            <div class="mt-4">
                <div class="alert alert-success">Image resized!</div>
                <div class="text-center">
                    <?php foreach(getFromSession('resized') as $image): ?>
                        <img src="<?php echo $image ?>" class="img-fluid">
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>