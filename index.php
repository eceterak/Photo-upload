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
            $upload->persist();

            $upload->resize();
        }
        
    ?>

    <div class="container">
        <div class="mt-4">
            <h2 class="text-center app-header mb-4">Image resize application</h2>
            <?php 
                if($upload && $upload->errors)
                {
                    echo $upload->errors;
                }
            ?>
            <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                <div class="custom-file mb-4">
                    <input type="file" name="image[]" class="custom-file-input">
                    <label class="custom-file-label" for="image">Choose file</label>
                </div>
                <div class="form-group row">
                    <label for="width" class="col-3">Width</label>
                    <div class="col-9">
                        <input type="number" name="width" id="width" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="width" class="col-3">Height</label>
                    <div class="col-9">
                        <input type="number" name="height" id="height" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>



</body>
</html>