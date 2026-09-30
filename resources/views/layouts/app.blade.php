<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ERP System') - Davao Sugar Central Co., Inc.</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- FontAwesome / Lucide Icons Support -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <div class="min-h-screen flex flex-col md:flex-row">
        
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 bg-emerald-950 text-white flex-shrink-0 flex flex-col justify-between shadow-xl">
            <div>
                <!-- Company Header Branding -->
                <div class="p-4 border-b border-emerald-800 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center font-bold text-xl text-white shadow-inner">
                        <i class="fa-solid fa-wheat-awn"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-sm tracking-wide text-white leading-tight">Davao Sugar Central</h1>
                        <p class="text-xs text-emerald-300 font-medium">Co., Inc. | PSHC / FDC</p>
                    </div>
                </div>
                
                <div class="px-3 py-2 text-[10px] uppercase font-bold tracking-wider text-emerald-400 bg-emerald-900/60">
                    <i class="fa-solid fa-location-dot mr-1"></i> Guihing, Hagonoy, Davao del Sur
                </div>

                <!-- Main Menu -->
                <nav class="p-3 space-y-1 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-emerald-700 text-white font-semibold' : 'text-emerald-100 hover:bg-emerald-900 hover:text-white' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center text-emerald-300"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="pt-2 text-xs font-semibold text-emerald-400 uppercase tracking-wider px-3">Agriculture & Supply</div>
                    
                    <a href="{{ url('/farmers') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-users w-5 text-center text-emerald-300"></i>
                        <span>Farmers Management</span>
                    </a>

                    <a href="{{ url('/fertilizer-requests') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-vial-circle-check w-5 text-center text-emerald-300"></i>
                        <span>Fertilizer Automation</span>
                    </a>

                    <a href="{{ url('/inventory') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-warehouse w-5 text-center text-emerald-300"></i>
                        <span>Inventory & Stock</span>
                    </a>

                    <div class="pt-2 text-xs font-semibold text-emerald-400 uppercase tracking-wider px-3">Sales & Trade</div>

                    <a href="{{ url('/sales-orders') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-receipt w-5 text-center text-emerald-300"></i>
                        <span>Sales Orders</span>
                    </a>

                    <a href="{{ url('/import-orders') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-ship w-5 text-center text-emerald-300"></i>
                        <span>Imports & Exports</span>
                    </a>

                    <div class="pt-2 text-xs font-semibold text-emerald-400 uppercase tracking-wider px-3">Human Resources</div>

                    <a href="{{ url('/employees') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-id-card w-5 text-center text-emerald-300"></i>
                        <span>Employee & Payroll</span>
                    </a>

                    <div class="pt-2 text-xs font-semibold text-emerald-400 uppercase tracking-wider px-3">Intelligence & Logs</div>

                    <a href="{{ url('/analytics') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-square-poll-vertical w-5 text-center text-emerald-300"></i>
                        <span>Sugarcane Analytics</span>
                    </a>

                    <a href="{{ url('/reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-file-invoice w-5 text-center text-emerald-300"></i>
                        <span>Reports</span>
                    </a>

                    <a href="{{ url('/audit-logs') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-emerald-100 hover:bg-emerald-900 hover:text-white">
                        <i class="fa-solid fa-shield-halved w-5 text-center text-emerald-300"></i>
                        <span>Audit Logs</span>
                    </a>
                </nav>
            </div>

            <!-- User Footer Info -->
            <div class="p-3 border-t border-emerald-900 bg-emerald-950">
                <div class="flex items-center justify-between text-xs text-emerald-200">
                    <span class="truncate">Logged in as: <strong class="text-white">{{ Auth::user()->name ?? 'User' }}</strong></span>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-gray-50 overflow-hidden">
            
            <!-- Topbar Navigation -->
            <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shadow-sm">
                <!-- Search bar -->
                <div class="flex items-center gap-3 w-1/3">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" placeholder="Search farmers, orders, inventory..." class="w-full pl-9 pr-4 py-1.5 text-sm bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition">
                    </div>
                </div>

                <!-- Actions / Profile -->
                <div class="flex items-center gap-4">
                    <!-- Notifications Link -->
                    <a href="{{ url('/notifications') }}" class="relative p-2 text-gray-500 hover:text-emerald-700 transition">
                        <i class="fa-regular fa-bell text-lg"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-emerald-600 rounded-full"></span>
                    </a>

                    <div class="h-6 w-px bg-gray-300"></div>

                    <!-- User Profile Dropdown / Logout -->
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-800 leading-none">{{ Auth::user()->name ?? 'Administrator' }}</p>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">{{ Auth::user()->role_name ?? 'System Administrator' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Status Flash Alerts -->
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-600 text-emerald-800 p-4 m-6 mb-0 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 m-6 mb-0 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-red-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Main Body View -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

            <!-- Global Footer -->
            <footer class="bg-white border-t border-gray-200 px-6 py-3 text-xs text-gray-500 flex justify-between items-center">
                <div>
                    <strong>Davao Sugar Central Co., Inc.</strong> &copy; {{ date('Y') }} ERP Platform Development Project
                </div>
                <div>
                    Location: Salutillo St., Brgy. Guihing, Hagonoy, Davao del Sur
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
