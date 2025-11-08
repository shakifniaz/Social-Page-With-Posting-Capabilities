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
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        placeholder="Choose a password (min. 6 characters)"
                    >
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
    const passwordError = document.getElementById('passwordError');
    if (this.value.length >= 6) {
        passwordError.classList.add('hidden');
    }
});
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';