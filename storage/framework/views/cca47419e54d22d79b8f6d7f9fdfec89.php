<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'EMS')); ?> - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/bootstrap/dist/css/bootstrap.min.css')); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/font-awesome/css/font-awesome.min.css')); ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/Ionicons/css/ionicons.min.css')); ?>">
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #1a1a2e; /* Fallback */
            background-image: url('<?php echo e(asset('/assets/images/background/sai2.jpg')); ?>');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.6) 100%);
            z-index: 1;
        }

        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            padding: 15px;
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 45px 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            color: #fff;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        
        .glass-panel:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        .login-title {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 35px;
            letter-spacing: 2px;
            text-transform: uppercase;
            background: linear-gradient(to right, #ffbb00, #f39c12, #ffbb00);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            height: 54px;
            padding: 10px 15px 10px 50px;
            font-size: 15px;
            box-shadow: none;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #ffbb00;
            box-shadow: 0 0 15px rgba(255, 187, 0, 0.3);
            color: #fff;
            outline: none;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
            font-weight: 300;
        }

        .form-control-feedback {
            position: absolute;
            top: 0;
            left: 0;
            width: 50px;
            height: 54px;
            line-height: 54px;
            text-align: center;
            color: #ffbb00;
            font-size: 18px;
            opacity: 0.8;
        }

        .btn-login {
            background: linear-gradient(135deg, #ffbb00 0%, #f39c12 100%);
            border: none;
            border-radius: 12px;
            color: #1a1a2e;
            font-size: 16px;
            font-weight: 600;
            height: 54px;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            transition: 0.5s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(243, 156, 18, 0.6);
            color: #fff;
        }

        .btn-login:hover::after {
            left: 100%;
            transition: 0.5s;
        }

        .btn-login:active {
            transform: translateY(1px);
            box-shadow: 0 2px 10px rgba(243, 156, 18, 0.4);
        }

        .forgot-password {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-block;
            margin-top: 20px;
            font-weight: 300;
        }

        .forgot-password:hover {
            color: #ffbb00;
            text-decoration: none;
        }

        .invalid-feedback {
            display: block;
            color: #ff4757;
            font-size: 13px;
            margin-top: 8px;
            font-weight: 400;
            text-align: left;
            padding-left: 10px;
        }
        
        .is-invalid {
            border-color: #ff4757 !important;
        }
        
        /* Auto-fill fix for webkit */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px #2a2a3e inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    
    <div class="login-wrapper">
        <div class="glass-panel">
            <h2 class="login-title">HRMS Login</h2>
            
            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <span class="fa fa-user form-control-feedback"></span>
                    <input id="email" type="text" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" placeholder="NIK / Email" required autocomplete="email" autofocus>
                    
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback" role="alert">
                            <strong><?php echo e($message); ?></strong>
                        </span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <span class="fa fa-lock form-control-feedback"></span>
                    <input id="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" placeholder="Password" required autocomplete="current-password">
                    
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="invalid-feedback" role="alert">
                            <strong><?php echo e($message); ?></strong>
                        </span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="btn-login">
                    <?php echo e(__('Login')); ?>

                </button>
                
                <div class="text-center">
                    <?php if(Route::has('password.request')): ?>
                        <a href="<?php echo e(route('password.request')); ?>" class="forgot-password">
                            <?php echo e(__('Reset Password')); ?>

                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery 3 -->
    <script src="<?php echo e(asset('/assets/bower_components/jquery/dist/jquery.min.js')); ?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo e(asset('/assets/bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>

</body>
</html>




<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/auth/login.blade.php ENDPATH**/ ?>