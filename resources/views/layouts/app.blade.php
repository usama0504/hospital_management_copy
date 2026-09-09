<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Health Care') }}</title>

    <!-- Fonts -->
   <body class="antialiased text-gray-800" style="font-family: 'Poppins', sans-serif;" x-data="{ sidebarOpen: false }">
    <!-- Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50/50 font-sans antialiased text-gray-800" x-data="{ sidebarOpen: false }">

    @if(request()->routeIs('login') || request()->routeIs('register'))
        <!-- Header & Footer Layout for Login & Register Pages -->
        <div class="min-h-screen flex flex-col justify-between bg-gray-50/50">
            
            <!-- Simple Top Header -->
            <header class="w-full bg-white border-b border-gray-100 py-4 px-6 sm:px-12 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold shadow-sm shadow-orange-500/30 text-base">
                        🏥
                    </div>
                    <span class="text-base font-black tracking-tight text-gray-900">Health Care</span>
                </div>
                {{-- <div>
                    <a href="{{ request()->routeIs('login') ? route('register') : route('login') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 transition">
                        {{ request()->routeIs('login') ? 'Create Account' : 'Sign In' }}
                    </a>
                </div> --}}
            </header>

            <!-- Main Form Content -->
            <main class="flex-1 flex items-center justify-center py-8 px-4">
                @yield('content')
            </main>

            <!-- Simple Footer -->
            <footer class="w-full bg-white border-t border-gray-100 py-4 text-center text-xs text-gray-400 font-medium">
                &copy; {{ date('Y') }} E-Health Tips. All rights reserved.
            </footer>

        </div>
    @else
        <!-- Dashboard Layout with Sidebar & Header -->
        <div class="flex h-screen overflow-hidden">

            <!-- Left Sidebar -->
            <aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-100 z-20">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3 px-6 h-20 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-bold shadow-sm shadow-orange-500/30 text-lg">
                        🏥
                    </div>
                    <span class="text-lg font-black tracking-tight text-gray-900">Health Care</span>
                </div>

                <!-- Sidebar Links -->
                <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto text-xs font-semibold">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('appointments.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('appointments.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Appointment
                    </a>
                    <a href="{{ route('bills.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('bills.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Billing & Invoices
                    </a>
                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('users.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Manage Users
                    </a>
                    <a href="{{ route('doctors.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('doctors.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Doctors
                    </a>
                    <a href="{{ route('patients.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('patients.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Patients
                    </a>
                    <a href="{{ route('reports.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h2v8H3v-8zm4-6h2v14H7V7zm4-4h2v18h-2V3zm4 8h2v10h-2V11zm4-5h2v15h-2V6z" />
                        </svg>
                        Reports
                    </a>
                </nav>

                <!-- Logout Action at bottom of sidebar -->
                <div class="p-4 border-t border-gray-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition font-semibold text-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Wrapper -->
            <div class="flex-1 flex flex-col overflow-hidden">

                <!-- Top Header Bar -->
                <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-8 z-10">
                    <div class="flex items-center gap-4 w-1/3">
                        <h1 class="text-xl font-black text-gray-900 tracking-tight">Dashboard Overview</h1>
                    </div>

                    <!-- Search Input Bar -->
                    {{-- <div class="w-1/3">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" placeholder="Search here..."
                                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200/80 rounded-full text-xs focus:outline-none focus:border-orange-500 transition">
                        </div>
                    </div> --}}

                    <!-- Admin Profile Section -->
                    <div class="flex items-center gap-4">
                        {{-- <div class="relative">
                            <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                            <button class="p-2.5 rounded-full bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </button>
                        </div> --}}
                        @auth
                            <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
                                <img src="https://images.unsplash.com/photo-1618001789159-ffffe6f96ef2?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8bWVucyUyMHBpY3xlbnwwfHwwfHx8MA%3D%3D"
                                    alt="Admin" class="w-10 h-10 rounded-full object-cover shadow-sm">
                                <div>
                                    <p class="text-xs font-bold text-gray-900 leading-none">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-gray-400 mt-1 capitalize font-semibold">
                                        {{ method_exists(auth()->user(), 'getRoleNames') ? auth()->user()->getRoleNames()->first() ?? 'Admin' : 'Admin' }}
                                    </p>
                                </div>
                            </div>
                        @endauth
                    </div>
                </header>

                <!-- Scrollable Page Content Area -->
                <main class="flex-1 overflow-y-auto p-8">
                    @yield('content')
                </main>

            </div>

        </div>
    @endif

</body>

</html>