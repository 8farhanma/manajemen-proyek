<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Task Manager</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo-circle.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            padding: 20px 0;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: #3949AB;
            color: white;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
            font-size: 1.25rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #3949AB;
            border-color: #3949AB;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #303F9F;
            border-color: #303F9F;
            transform: translateY(-2px);
        }

        .btn-secondary {
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
        }

        .form-control:focus {
            border-color: #3949AB;
            box-shadow: 0 0 0 0.25rem rgba(57, 73, 171, 0.25);
        }

        .input-group-text {
            background-color: #E8EAF6;
            border-color: #ced4da;
        }

        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #6c757d;
        }

        .form-floating {
            position: relative;
        }

        .invalid-feedback {
            font-size: 0.875em;
            color: #dc3545;
        }

        .password-requirements {
            font-size: 0.875em;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow">
                    <div class="card-header text-center p-4">
                        <img src="{{ asset('assets/img/LOGO_TAPPP.png') }}" class="img-fluid" alt="task manager" style="max-height: 50px;">
                    </div>
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Edit Profile</h4>
                        <form method="POST" action="{{ route('profile.update') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ auth()->user()->name }}" required placeholder="Your Name">
                                <label for="name">Nama</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ auth()->user()->email }}" required placeholder="name@example.com">
                                <label for="email">Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Nomor WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="tel" name="phone_number" id="phone_number" 
                                        class="form-control @error('phone_number') is-invalid @enderror" 
                                        placeholder="8xxxxxxxxxx" pattern="[0-9]{10,13}" 
                                        value="{{ auth()->user()->phone_number }}" required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Contoh: 81234567890 (tanpa angka 0 di depan)</small>
                            </div>

                            <div class="form-floating mb-3 position-relative">
                                <input type="password" name="password" id="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    placeholder="Leave blank if unchanged">
                                <label for="password">Password Baru</label>
                                <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="password-requirements mt-1">
                                    Password harus memiliki minimal 8 karakter
                                </div>
                            </div>

                            <div class="form-floating mb-4 position-relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="form-control" placeholder="Confirm new password">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <i class="bi bi-eye-slash password-toggle" id="toggleConfirmPassword"></i>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Profile
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const password = document.getElementById('password_confirmation');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Phone number formatting
        const phoneInput = document.getElementById('phone_number');
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,4})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : !x[3] ? x[1] + '-' + x[2] : x[1] + '-' + x[2] + '-' + x[3];
        });
    </script>
</body>

</html>
