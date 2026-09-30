<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Davao Sugar Central Co., Inc. ERP</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-emerald-900 via-emerald-950 to-gray-950 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-emerald-800/30">
        <!-- Brand Header Banner -->
        <div class="bg-emerald-900 text-white p-6 text-center relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 text-emerald-800/30 text-9xl font-bold select-none pointer-events-none">
                <i class="fa-solid fa-wheat-awn"></i>
            </div>
            
            <div class="inline-flex items-center justify-center w-14 h-14 bg-emerald-700 rounded-xl mb-3 shadow-lg text-2xl text-emerald-200">
                <i class="fa-solid fa-industry"></i>
            </div>

            <h2 class="text-xl font-bold tracking-tight text-white">Davao Sugar Central Co., Inc.</h2>
            <p class="text-xs text-emerald-300 font-medium mt-0.5">Pacific Sugar Holdings Corp. / Filinvest Dev Corp</p>
            <p class="text-[11px] text-emerald-200 mt-2 bg-emerald-950/60 py-1 px-3 rounded-full inline-block">
                <i class="fa-solid fa-location-dot text-emerald-400 mr-1"></i> Salutillo St., Brgy. Guihing, Hagonoy, Davao del Sur
            </p>
        </div>

        <!-- Login Form Container -->
        <div class="p-8">
            <div class="mb-6 text-center">
                <h3 class="text-lg font-bold text-gray-900">ERP System Login</h3>
                <p class="text-xs text-gray-500 mt-1">Sugarcane Farmers Analytics & Fertilizer Automation</p>
            </div>

            <!-- Error Notification -->
            @if ($errors->any())
                <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-3 rounded-r text-xs text-red-700">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-5 bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r text-xs text-blue-700">
                    {{ session('info') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Email Address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 focus:bg-white transition"
                            placeholder="admin@davaosugar.com">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 focus:bg-white transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <span class="ml-2">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Sign In to ERP</span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-100 p-4 text-center text-xs text-gray-500">
            Davao Sugar Central Co., Inc. &copy; {{ date('Y') }} ERP Platform
        </div>
    </div>

</body>
</html>
