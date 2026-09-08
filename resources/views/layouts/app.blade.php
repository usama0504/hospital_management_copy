<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Hospital Management') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen font-sans">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between space-x-8">
            <div class="text-indigo-600 font-bold text-xl cursor-pointer select-none">
                HospitalMS
            </div>

            <!-- Navigation -->
            <nav class="flex items-center space-x-6 text-gray-600 font-semibold">
                <a href="{{ route('dashboard') }}"
                    class="py-1 px-3 rounded-full hover:bg-indigo-100 {{ request()->routeIs('dashboard') ? 'bg-indigo-200 text-indigo-700' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('patients.index') }}"
                    class="py-1 px-3 rounded-full hover:bg-indigo-100 {{ request()->routeIs('patients.*') ? 'bg-indigo-200 text-indigo-700' : '' }}">
                    Patients
                </a>
                <a href="{{ route('doctors.index') }}"
                    class="py-1 px-3 rounded-full hover:bg-indigo-100 {{ request()->routeIs('doctors.*') ? 'bg-indigo-200 text-indigo-700' : '' }}">
                    Doctors
                </a>
                <a href="{{ route('appointments.index') }}"
                    class="py-1 px-3 rounded-full hover:bg-indigo-100 {{ request()->routeIs('appointments.*') ? 'bg-indigo-200 text-indigo-700' : '' }}">
                    Appointments
                </a>
                <a href="{{ route('bills.index') }}"
                    class="py-1 px-3 rounded-full hover:bg-indigo-100 {{ request()->routeIs('bills.*') ? 'bg-indigo-200 text-indigo-700' : '' }}">
                    Billing
                </a>
                <a href="{{ route('reports.index') }}"
                    class="py-1 px-3 rounded-full hover:bg-indigo-100 {{ request()->routeIs('reports.*') ? 'bg-indigo-200 text-indigo-700' : '' }}">
                    Reports
                </a>
                @role('admin')
                <a href="{{ route('users.index') }}"
                    class="py-1 px-3 rounded-full hover:bg-indigo-100 {{ request()->routeIs('users.*') ? 'bg-indigo-200 text-indigo-700' : '' }}">
                    Manage Users
                </a>
                @endrole

                @auth
                    <span class="text-gray-300">|</span>
                    <span class="text-sm text-gray-500 font-normal">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="py-1 px-3 rounded-full text-red-600 hover:bg-red-50">
                            Logout
                        </button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto py-8 px-6 min-h-[calc(100vh-88px)]">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} Hospital Management System. All rights reserved.
    </footer>

</body>
</html>