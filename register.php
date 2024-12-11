<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, rgba(0, 204, 255, 0.8), rgba(0, 153, 255, 0.8));
            background-size: cover;
            font-family: 'Nunito', sans-serif;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group input {
            border-radius: 10px;
        }

        .btn-primary {
            border-radius: 10px;
        }

        /* Modern Image Upload Field */
        .upload-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .upload-container:hover {
            transform: scale(1.05);
        }

        .upload-container .file-input {
            opacity: 0; /* Hide the file input */
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .image-preview {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            transition: opacity 0.3s ease;
        }

        .upload-icon {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border-radius: 50%;
            padding: 8px;
            font-size: 18px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .upload-icon:hover {
            opacity: 1;
        }

        /* Add a border when image is uploaded */
        .image-preview img.uploaded {
            border: 3px solid #4e73df;
        }

        label {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        /* Make the form more responsive */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-control-user {
            font-size: 1rem;
        }

        .btn-user {
            padding: 1rem;
            font-size: 1.2rem;
        }

        @media (max-width: 576px) {
            .upload-container {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" action="register_process.php" method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                    <label for="imgupload"></label>
                                    <div class="upload-container">
                                        <input type="file" class="file-input" name="imgupload" id="imgupload" accept="image/*" required>
                                        <div class="image-preview">
                                            <img id="profileImage" src="default-avatar.png" alt="" class="preview-img">
                                            <div class="upload-icon">
                                                <i class="fas fa-camera"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" name="firstname" placeholder="First Name" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" name="username" placeholder="User Name" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" name="email" placeholder="Email Address" required>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">Register Account</button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="settings.php">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <script>
        // Allow the image preview when file input changes
        document.getElementById('imgupload').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
                var previewImg = document.getElementById('profileImage');
                previewImg.src = e.target.result;
                previewImg.classList.add('uploaded'); // Add border after image is uploaded
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });

        // Allow the camera icon to open file input when clicked
        document.querySelector('.upload-icon').addEventListener('click', function() {
            document.getElementById('imgupload').click();
        });
    </script>

</body>

</html>
