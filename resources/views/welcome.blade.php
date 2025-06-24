<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=space-grotesk:400,500,600,700" rel="stylesheet" />
<!-- Styles / Scripts -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white min-h-screen relative overflow-x-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-4000"></div>
    </div>

    <!-- Grid Pattern Overlay -->
    <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>

    <header class="relative z-10 w-full px-6 lg:px-8 py-6">
        @if (Route::has('login'))
        <nav class="flex items-center justify-between max-w-7xl mx-auto">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-gradient-to-r from-cyan-400 to-purple-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">CryptoP2P</span>
            </div>

            <!-- Navigation Links -->
            <div class="flex items-center space-x-4">
                @auth
                <a href="{{ url('/dashboard') }}"
                class="group relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white font-medium rounded-lg shadow-lg shadow-purple-500/25 transition-all duration-300 hover:shadow-purple-500/40 hover:scale-105">
                    <span class="relative z-10">Dashboard</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-purple-500 rounded-lg blur opacity-30 group-hover:opacity-50 transition-opacity"></div>
                </a>
                @else
                <a href="{{ route('login') }}"
                class="px-6 py-2.5 text-gray-300 hover:text-white font-medium transition-colors duration-300 hover:bg-white/10 rounded-lg backdrop-blur-sm border border-white/10 hover:border-white/20">
                    Log in
                </a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                class="group relative inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white font-medium rounded-lg shadow-lg shadow-purple-500/25 transition-all duration-300 hover:shadow-purple-500/40 hover:scale-105">
                    <span class="relative z-10">Get Started</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-purple-500 rounded-lg blur opacity-30 group-hover:opacity-50 transition-opacity"></div>
                </a>
                @endif
                @endauth
            </div>
        </nav>
        @endif
    </header>

    <div class="relative z-10 flex items-center justify-center min-h-[80vh] px-6 lg:px-8 transition-opacity opacity-100 duration-750 starting:opacity-0">
        <main class="flex max-w-7xl w-full flex-col lg:flex-row items-center gap-12 lg:gap-16">
            <!-- Hero Content -->
            <div class="flex-1 text-center lg:text-left">
                <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 border border-cyan-500/30 rounded-full text-sm font-medium text-cyan-300 mb-6 backdrop-blur-sm">
                    🚀 Next Generation P2P Trading
                </div>
                
                <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                    Trade Crypto
                    <span class="block bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                        Peer-to-Peer
                    </span>
                    <span class="block text-gray-300">Securely</span>
                </h1>
                
                <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    Connect directly with traders worldwide. No intermediaries, lower fees, complete control over your cryptocurrency transactions.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    @if (!Auth::check())
                    <a href="{{ route('register') }}" class="group relative inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-xl shadow-purple-500/25 transition-all duration-300 hover:shadow-purple-500/40 hover:scale-105">
                        <span class="relative z-10 flex items-center">
                            Start Trading Now
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-purple-500 rounded-xl blur opacity-30 group-hover:opacity-50 transition-opacity"></div>
                    </a>
                    
                    <a href="#learn-more" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 hover:border-white/30 text-white font-semibold rounded-xl transition-all duration-300">
                        Learn More
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </a>
                    @else
                    <a href="{{ url('/dashboard') }}" class="group relative inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-xl shadow-purple-500/25 transition-all duration-300 hover:shadow-purple-500/40 hover:scale-105">
                        <span class="relative z-10 flex items-center">
                            Go to Dashboard
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-purple-500 rounded-xl blur opacity-30 group-hover:opacity-50 transition-opacity"></div>
                    </a>
                    @endif
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 mt-12 pt-8 border-t border-white/10">
                    <div class="text-center lg:text-left">
                        <div class="text-2xl lg:text-3xl font-bold text-cyan-400">24/7</div>
                        <div class="text-gray-400 text-sm">Active Trading</div>
                    </div>
                    <div class="text-center lg:text-left">
                        <div class="text-2xl lg:text-3xl font-bold text-purple-400">0.1%</div>
                        <div class="text-gray-400 text-sm">Trading Fees</div>
                    </div>
                    <div class="text-center lg:text-left col-span-2 lg:col-span-1">
                        <div class="text-2xl lg:text-3xl font-bold text-pink-400">50+</div>
                        <div class="text-gray-400 text-sm">Cryptocurrencies</div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Visual -->
            <div class="flex-1 relative max-w-lg">
                <div class="relative">
                    <!-- Main Card -->
                    <div class="bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-xl border border-white/20 rounded-2xl p-8 shadow-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold">Trading Dashboard</h3>
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        </div>
                        
                        <!-- Mock Chart -->
                        <div class="h-32 bg-gradient-to-r from-cyan-500/20 to-purple-500/20 rounded-lg mb-6 flex items-end justify-between px-4 py-4">
                            <div class="w-2 bg-cyan-400 rounded-t" style="height: 60%"></div>
                            <div class="w-2 bg-purple-400 rounded-t" style="height: 80%"></div>
                            <div class="w-2 bg-pink-400 rounded-t" style="height: 45%"></div>
                            <div class="w-2 bg-cyan-400 rounded-t" style="height: 90%"></div>
                            <div class="w-2 bg-purple-400 rounded-t" style="height: 70%"></div>
                            <div class="w-2 bg-pink-400 rounded-t" style="height: 85%"></div>
                        </div>
                        
                        <!-- Mock Crypto List -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">₿</div>
                                    <div>
                                        <div class="font-medium">Bitcoin</div>
                                        <div class="text-xs text-gray-400">BTC</div>
                                    </div>
                                </div>
                                <div class="text-green-400 font-medium">+2.4%</div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">Ξ</div>
                                    <div>
                                        <div class="font-medium">Ethereum</div>
                                        <div class="text-xs text-gray-400">ETH</div>
                                    </div>
                                </div>
                                <div class="text-red-400 font-medium">-1.2%</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-r from-cyan-400 to-purple-500 rounded-xl rotate-12 opacity-80 animate-pulse"></div>
                    <div class="absolute -bottom-4 -left-4 w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-500 rounded-lg -rotate-12 opacity-60 animate-pulse animation-delay-2000"></div>
                </div>
            </div>
        </main>
    </div>

    @if (Route::has('login'))
    <div class="h-14.5 hidden lg:block"></div>
    @endif

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        .bg-grid-pattern {
            background-image: 
                linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
        }
    </style>
</body>
</html>