<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
    <script src="js/sweetalert/sweetalert.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white text-center fs-4">
            <span id="form-title">Login</span>
        </div>

        <!-- Login Form -->
        <form id="login-form">
            <div class="card-body">
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="" required>
                </div>
                <div class="form-group mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <!-- Toggle show password -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show-password" />
                        <label class="form-check-label" for="show-password">Show Password</label>
                    </div>
                </div>

                <!-- Link to Signup form -->
                <div class="form-group text-center mb-3">
                    <p>Don't have an account? <a href="javascript:void(0);" id="show-signup-form">Sign up</a></p>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button class="btn btn-primary">Login</button>
            </div>
        </form>

        <!-- Signup Form -->
        <form id="signup-form" style="display: none;">
            <div class="card-body">
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="username" id="signup-username" placeholder="Username" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <input type="password" class="form-control" name="password" id="signup-password" placeholder="Password" required>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show-signup-password" />
                                <label class="form-check-label" for="show-signup-password">Show Password</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <input type="password" class="form-control" name="password_confirmation" id="signup-password-confirmation" placeholder="Confirm Password" required>
                        </div>
                    </div>
                </div>

                <!-- Link to Login form -->
                <div class="form-group text-center mb-3">
                    <p>Already have an account? <a href="javascript:void(0);" id="show-login-form">Login</a></p>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Signup</button>
            </div>
        </form>
    </div>
</div>
<script src= "js/jquery/jquery-3.6.0.min.js"></script>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="js/actions/authenticate.js"></script>
</body>
</html>