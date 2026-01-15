<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Digital Child Health Record System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url("{{ asset('background.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            z-index: 0;
        }
        
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 50px 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .back-icon {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .back-icon:hover {
            background: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .back-icon svg {
            width: 20px;
            height: 20px;
            color: #333;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-image {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .system-title {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }
        
        .system-subtitle {
            font-size: 13px;
            color: #666;
            font-weight: 400;
        }
        
        .reset-form {
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-input {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            background: #fafafa;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #ff6f91;
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 111, 145, 0.1);
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            transition: color 0.2s;
        }
        
        .password-toggle:hover {
            color: #333;
        }
        
        .password-toggle svg {
            width: 20px;
            height: 20px;
        }
        
        .change-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
        }
        
        .change-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(255, 111, 145, 0.4);
        }
        
        .change-btn:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .success-message {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 40px 30px;
            }
            
            .logo-image {
                width: 120px;
                height: 120px;
            }
            
            .system-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="{{ route('login') }}" class="back-icon" title="Back to Login">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        
        <div class="logo-section">
            <img src="{{ asset('logo.jpg') }}" alt="Logo" class="logo-image">
            <h1 class="system-title">Reset Password</h1>
        </div>
        
        <form class="reset-form" action="{{ route('password.reset.post') }}" method="POST">
            @csrf
            
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <div class="form-group">
                <label for="new_password" class="form-label">New Password</label>
                <div class="password-wrapper">
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        class="form-input" 
                        placeholder="Enter new password"
                        required
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('new_password', 'eyeIcon1')">
                        <svg id="eyeIcon1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0L3 3m3.59 3.59L3 3m3.59 3.59l3.59-3.59"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-input" 
                        placeholder="Confirm new password"
                        required
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'eyeIcon2')">
                        <svg id="eyeIcon2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0L3 3m3.59 3.59L3 3m3.59 3.59l3.59-3.59"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="change-btn">Change Password</button>
        </form>
    </div>
    
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0L3 3m3.59 3.59L3 3m3.59 3.59l3.59-3.59"/>';
            }
        }
        
        // Validate password match on form submit
        document.querySelector('.reset-form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });
    </script>
</body>
</html>
