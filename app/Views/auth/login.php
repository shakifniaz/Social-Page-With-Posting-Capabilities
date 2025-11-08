<?php
$title = 'Login | MetroPost';
ob_start();
?>
<div class="h-screen bg-gray-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-orange-500 mb-2">MetroPost</h1>
            <h2 class="text-2xl font-bold text-gray-900">
                Sign in to your account
            </h2>
        </div>

        <div class="bg-white py-6 px-6 rounded-lg border border-gray-200 shadow-lg">
            <form class="space-y-4" method="POST" action="/login">
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
                        autocomplete="current-password" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                        placeholder="Enter your password"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg font-semibold transition-colors duration-200"
                >
                    Sign in
                </button>

                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="/register" class="text-orange-600 hover:text-orange-500 font-medium transition-colors duration-200 ml-1">
                            Create a new account
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';