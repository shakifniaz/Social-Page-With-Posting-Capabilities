<?php
$title = 'Register | MetroPost';
ob_start();
?>
<div class="h-screen bg-gray-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-orange-500 mb-2">MetroPost</h1>
            <h2 class="text-2xl font-bold text-gray-900">
                Create your account
            </h2>
        </div>

        <div class="bg-white py-6 px-6 rounded-lg border border-gray-200 shadow-lg">
            <form class="space-y-4" method="POST" action="/register" id="registerForm">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        autocomplete="name" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        placeholder="Enter your full name"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        placeholder="Enter your email"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="new-password" 
                        required 
                        class="w-full password px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        placeholder="Choose a password (min. 6 characters)"
                    >
                    <div class="mt-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-500">Password strength</span>
                            <span id="passwordStrengthText" class="text-xs font-medium">Weak</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="passwordStrengthBar" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                    <div id="passwordRequirements" class="mt-2 space-y-1">
                        <div class="flex items-center">
                            <div id="lengthCheck" class="w-3 h-3 rounded-full border border-gray-300 mr-2"></div>
                            <span class="text-xs text-gray-600">At least 6 characters</span>
                        </div>
                        <div class="flex items-center">
                            <div id="lowercaseCheck" class="w-3 h-3 rounded-full border border-gray-300 mr-2"></div>
                            <span class="text-xs text-gray-600">Lowercase letter</span>
                        </div>
                        <div class="flex items-center">
                            <div id="uppercaseCheck" class="w-3 h-3 rounded-full border border-gray-300 mr-2"></div>
                            <span class="text-xs text-gray-600">Uppercase letter</span>
                        </div>
                        <div class="flex items-center">
                            <div id="numberCheck" class="w-3 h-3 rounded-full border border-gray-300 mr-2"></div>
                            <span class="text-xs text-gray-600">Number</span>
                        </div>
                        <div class="flex items-center">
                            <div id="specialCheck" class="w-3 h-3 rounded-full border border-gray-300 mr-2"></div>
                            <span class="text-xs text-gray-600">Special character</span>
                        </div>
                    </div>
                    <p id="passwordError" class="mt-1 text-xs text-red-600 hidden">
                        Must be at least 6 characters long
                    </p>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg font-semibold transition-colors duration-200"
                >
                    Create Account
                </button>

                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="/login" class="text-orange-600 hover:text-orange-500 font-medium transition-colors duration-200 ml-1">
                            Sign in to existing account
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function checkPasswordStrength(password) {
    let strength = 0;
    const requirements = {
        length: password.length >= 6,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
    };

    Object.keys(requirements).forEach(key => {
        const element = document.getElementById(key + 'Check');
        if (requirements[key]) {
            element.classList.add('bg-green-500', 'border-green-500');
            element.classList.remove('bg-gray-300', 'border-gray-300');
            strength++;
        } else {
            element.classList.add('bg-gray-300', 'border-gray-300');
            element.classList.remove('bg-green-500', 'border-green-500');
        }
    });

    const totalRequirements = Object.keys(requirements).length;
    const strengthPercentage = (strength / totalRequirements) * 100;
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');

    strengthBar.style.width = strengthPercentage + '%';

    if (strengthPercentage <= 20) {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
        strengthText.textContent = 'Very Weak';
        strengthText.className = 'text-xs font-medium text-red-600';
    } else if (strengthPercentage <= 40) {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-orange-500';
        strengthText.textContent = 'Weak';
        strengthText.className = 'text-xs font-medium text-orange-600';
    } else if (strengthPercentage <= 60) {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
        strengthText.textContent = 'Fair';
        strengthText.className = 'text-xs font-medium text-yellow-600';
    } else if (strengthPercentage <= 80) {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-blue-500';
        strengthText.textContent = 'Good';
        strengthText.className = 'text-xs font-medium text-blue-600';
    } else {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
        strengthText.textContent = 'Strong';
        strengthText.className = 'text-xs font-medium text-green-600';
    }

    return strengthPercentage;
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordError = document.getElementById('passwordError');
    
    if (password.length < 6) {
        e.preventDefault();
        passwordError.classList.remove('hidden');
        document.getElementById('password').focus();
    } else {
        passwordError.classList.add('hidden');
    }
});

document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const passwordError = document.getElementById('passwordError');
    
    if (password.length >= 6) {
        passwordError.classList.add('hidden');
    } else {
        passwordError.classList.remove('hidden');
    }
    
    checkPasswordStrength(password);
});

document.addEventListener('DOMContentLoaded', function() {
    const requirements = ['length', 'lowercase', 'uppercase', 'number', 'special'];
    requirements.forEach(req => {
        const element = document.getElementById(req + 'Check');
        element.classList.add('bg-gray-300', 'border-gray-300');
    });
});
</script>

<style>
#passwordStrengthBar {
    transition: all 0.3s ease;
}

#lengthCheck,
#lowercaseCheck,
#uppercaseCheck,
#numberCheck,
#specialCheck {
    transition: all 0.3s ease;
}
</style>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';