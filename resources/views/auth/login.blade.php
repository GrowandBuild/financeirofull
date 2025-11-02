@extends('layouts.app')

@section('content')
<div class="login-container">
    <!-- Animated Background -->
    <div class="login-background">
        <div class="bg-gradient-1"></div>
        <div class="bg-gradient-2"></div>
        <div class="bg-gradient-3"></div>
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>
    </div>

    <div class="login-content">
        <!-- Left Side - Branding -->
        <div class="branding-section">
            <div class="branding-content">
                <div class="logo-container">
                    <div class="logo-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>
                <h1 class="branding-title">
                    Bem-vindo ao<br>
                    <span class="branding-highlight">Gerenciador de Produtos</span>
                </h1>
                <p class="branding-description">
                    Gerencie seus produtos com facilidade. Acompanhe compras, analise gastos e otimize sua experiência de compras.
                </p>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Análises Inteligentes</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Gestão Fácil</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle"></i>
                        <span>Seguro e Privado</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="form-section">
            <div class="form-container">
                <!-- Mobile Header -->
                <div class="mobile-header">
                    <div class="mobile-logo">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h1 class="mobile-title">Bem-vindo de Volta</h1>
                    <p class="mobile-subtitle">Entre na sua conta</p>
                </div>

                <!-- Login Card -->
                <div class="login-card">
                    <!-- Desktop Header -->
                    <div class="desktop-header">
                        <h2 class="form-title">Entrar</h2>
                        <p class="form-subtitle">Digite suas credenciais para acessar sua conta</p>
                    </div>

                    @if ($errors->any())
                        <div class="error-alert">
                            <div class="error-header">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span>Erro de validação</span>
                            </div>
                            <ul class="error-list">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="login-form">
                        @csrf

                        <!-- Email Field -->
                        <div class="form-group">
                            <label for="email" class="form-label">E-mail</label>
                            <div class="input-container">
                                <div class="input-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}"
                                       class="form-input @error('email') error @enderror"
                                       placeholder="Digite seu e-mail"
                                       required 
                                       autofocus>
                            </div>
                            @error('email')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <label for="password" class="form-label">Senha</label>
                            <div class="input-container">
                                <div class="input-icon">
                                    <i class="bi bi-lock"></i>
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-input @error('password') error @enderror"
                                       placeholder="Digite sua senha"
                                       required>
                            </div>
                            @error('password')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="form-options">
                            <label class="checkbox-container">
                                <input type="checkbox" name="remember" id="remember">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-label">Lembrar de mim</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-link">
                                    Esqueceu a senha?
                                </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="login-button">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Entrar
                        </button>

                        <!-- Register Link -->
                        @if (Route::has('register'))
                            <div class="register-section">
                                <span class="register-text">Não tem uma conta?</span>
                                <a href="{{ route('register') }}" class="register-link">
                                    Cadastre-se
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Demo Credentials -->
                <div class="demo-section">
                    <div class="demo-header">
                        <div class="demo-icon">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <h4 class="demo-title">Credenciais de Demonstração</h4>
                    </div>
                    <div class="demo-credentials">
                        <div class="credential-item">
                            <span class="credential-label">E-mail:</span>
                            <span class="credential-value">admin@test.com</span>
                        </div>
                        <div class="credential-item">
                            <span class="credential-label">Senha:</span>
                            <span class="credential-value">password</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
/* ===========================================
   ULTRA MODERN LOGIN DESIGN - 2024
   =========================================== */

/* Reset and Base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.login-container {
    position: relative;
    min-height: 100vh;
    width: 100%;
    overflow: hidden;
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #533483 100%);
    margin: -1rem;
    padding: 0;
}

/* ===========================================
   ANIMATED BACKGROUND
   =========================================== */

.login-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
}

.bg-gradient-1 {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 20% 20%, rgba(120, 119, 198, 0.3) 0%, transparent 50%);
    animation: rotate 20s linear infinite;
}

.bg-gradient-2 {
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 80% 80%, rgba(255, 119, 198, 0.2) 0%, transparent 50%);
    animation: rotate 25s linear infinite reverse;
}

.bg-gradient-3 {
    position: absolute;
    bottom: -50%;
    left: 50%;
    transform: translateX(-50%);
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 50% 50%, rgba(119, 198, 255, 0.15) 0%, transparent 50%);
    animation: rotate 30s linear infinite;
}

.floating-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    backdrop-filter: blur(10px);
    animation: float 6s ease-in-out infinite;
}

.shape-1 {
    width: 80px;
    height: 80px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 120px;
    height: 120px;
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.shape-3 {
    width: 60px;
    height: 60px;
    top: 80%;
    left: 20%;
    animation-delay: 4s;
}

.shape-4 {
    width: 100px;
    height: 100px;
    top: 40%;
    right: 30%;
    animation-delay: 1s;
}

/* ===========================================
   LAYOUT STRUCTURE
   =========================================== */

.login-content {
    position: relative;
    z-index: 2;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.branding-section {
    display: none;
    flex: 1;
    max-width: 50%;
    padding: 4rem;
    align-items: center;
    justify-content: center;
}

.form-section {
    flex: 1;
    max-width: 500px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-container {
    width: 100%;
    max-width: 400px;
}

/* ===========================================
   BRANDING SECTION
   =========================================== */

.branding-content {
    text-align: center;
    animation: slideInLeft 1s ease-out;
}

.logo-container {
    margin-bottom: 3rem;
}

.logo-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 30px;
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.4);
    margin-bottom: 2rem;
    animation: pulse 2s ease-in-out infinite;
}

.logo-icon i {
    font-size: 3rem;
    color: white;
}

.branding-title {
    font-size: 4rem;
    font-weight: 800;
    color: white;
    line-height: 1.1;
    margin-bottom: 2rem;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.branding-highlight {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.branding-description {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin-bottom: 3rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.features-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    max-width: 400px;
    margin: 0 auto;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    font-weight: 500;
    animation: fadeInUp 0.8s ease-out;
    animation-fill-mode: both;
}

.feature-item:nth-child(1) { animation-delay: 0.1s; }
.feature-item:nth-child(2) { animation-delay: 0.2s; }
.feature-item:nth-child(3) { animation-delay: 0.3s; }

.feature-item i {
    color: #667eea;
    font-size: 1.5rem;
}

/* ===========================================
   MOBILE HEADER
   =========================================== */

.mobile-header {
    text-align: center;
    margin-bottom: 3rem;
    animation: fadeInUp 0.8s ease-out;
}

.mobile-logo {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 25px;
    box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
    margin-bottom: 2rem;
}

.mobile-logo i {
    font-size: 2rem;
    color: white;
}

.mobile-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.mobile-subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.7);
}

/* ===========================================
   LOGIN CARD
   =========================================== */

.login-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 25px;
    padding: 3rem;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
    animation: slideInUp 1s ease-out;
}

.desktop-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.form-title {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.form-subtitle {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.7);
}

/* ===========================================
   ERROR ALERT
   =========================================== */

.error-alert {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
    animation: shake 0.5s ease-in-out;
}

.error-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #fca5a5;
    font-weight: 600;
    margin-bottom: 1rem;
}

.error-header i {
    font-size: 1.25rem;
}

.error-list {
    list-style: none;
    color: #fca5a5;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-left: 1.5rem;
}

/* ===========================================
   FORM ELEMENTS
   =========================================== */

.login-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.form-label {
    font-size: 0.95rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0.5rem;
}

.input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: rgba(255, 255, 255, 0.5);
    font-size: 1.1rem;
    z-index: 2;
    pointer-events: none;
}

.form-input {
    width: 100%;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 1rem 1rem 1rem 3rem;
    color: white;
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
}

.form-input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.15);
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
    transform: translateY(-2px);
}

.form-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
    font-weight: 400;
}

.form-input.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2);
}

.error-message {
    color: #fca5a5;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ===========================================
   FORM OPTIONS
   =========================================== */

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 1rem 0;
}

.checkbox-container {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    user-select: none;
}

.checkbox-container input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
}

.checkbox-container input[type="checkbox"]:checked + .checkbox-custom {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.checkbox-container input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '✓';
    color: white;
    font-size: 0.8rem;
    font-weight: bold;
}

.checkbox-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
    font-weight: 500;
}

.forgot-link {
    color: #667eea;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.forgot-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

/* ===========================================
   LOGIN BUTTON
   =========================================== */

.login-button {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 15px;
    padding: 1.25rem 2rem;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    margin-top: 1rem;
}

.login-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
}

.login-button:active {
    transform: translateY(-1px);
}

.login-button i {
    font-size: 1.2rem;
}

/* ===========================================
   REGISTER SECTION
   =========================================== */

.register-section {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 2rem;
}

.register-text {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.95rem;
    margin-right: 0.5rem;
}

.register-link {
    color: #667eea;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.register-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

/* ===========================================
   DEMO SECTION
   =========================================== */

.demo-section {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 2rem;
    margin-top: 2rem;
    backdrop-filter: blur(10px);
    animation: fadeInUp 1s ease-out 0.5s both;
}

.demo-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.demo-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.demo-icon i {
    color: white;
    font-size: 1.2rem;
}

.demo-title {
    color: #667eea;
    font-size: 1.1rem;
    font-weight: 600;
}

.demo-credentials {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.credential-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.credential-label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
    font-weight: 500;
}

.credential-value {
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    font-family: 'Courier New', monospace;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.25rem 0.75rem;
    border-radius: 8px;
}

/* ===========================================
   ANIMATIONS
   =========================================== */

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* ===========================================
   RESPONSIVE DESIGN
   =========================================== */

@media (min-width: 1024px) {
    .branding-section {
        display: flex;
    }
    
    .mobile-header {
        display: none;
    }
    
    .login-content {
        padding: 4rem;
    }
}

@media (max-width: 1023px) {
    .branding-section {
        display: none;
    }
    
    .mobile-header {
        display: block;
    }
    
    .login-content {
        padding: 2rem 1rem;
    }
    
    .login-card {
        padding: 2rem;
    }
}

@media (max-width: 640px) {
    .login-card {
        padding: 1.5rem;
        border-radius: 20px;
    }
    
    .form-title {
        font-size: 1.75rem;
    }
    
    .mobile-title {
        font-size: 2rem;
    }
    
    .branding-title {
        font-size: 3rem;
    }
}

/* ===========================================
   ACCESSIBILITY & FOCUS
   =========================================== */

.login-button:focus-visible,
.form-input:focus-visible,
.forgot-link:focus-visible,
.register-link:focus-visible {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2, #667eea);
}

/* Text selection */
::selection {
    background: rgba(102, 126, 234, 0.3);
    color: white;
}

/* ===========================================
   PERFORMANCE OPTIMIZATIONS
   =========================================== */

.login-container {
    will-change: transform;
}

.shape {
    will-change: transform;
}

.login-card {
    will-change: transform;
}

/* Reduce motion for users who prefer it */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endsection