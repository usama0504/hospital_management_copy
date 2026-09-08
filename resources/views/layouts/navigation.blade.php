<nav class="bg-white border-b border-gray-100 shadow-sm" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">

            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-orange-500 text-white flex items-center justify-center font-bold shadow-sm shadow-orange-500/30 text-lg">
                    🏥
                </div>
                <a href="{{ url('/') }}" class="text-lg font-black tracking-tight text-gray-900">
                    HospitalMS
                </a>
            </div>

            <!-- Navigation Links (Desktop) -->
            <div class="hidden md:flex space-x-2 text-xs font-semibold">
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    Dashboard
                </a>
                <a href="{{ route('patients.index') }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('patients.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    Patients
                </a>
                <a href="{{ route('doctors.index') }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('doctors.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    Doctors
                </a>
                <a href="{{ route('appointments.index') }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('appointments.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    Appointments
                </a>
                <a href="{{ route('bills.index') }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('bills.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    Billing
                </a>
                <a href="{{ Route::has('reports.index') ? route('reports.index') : '#' }}" class="px-4 py-2.5 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    Reports
                </a>
            </div>

            <!-- Right Profile / Actions Area -->
            <div class="hidden md:flex items-center gap-4">
                @auth
                <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
                    <div>
                        <p class="text-xs font-bold text-gray-900 leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-gray-400 mt-1 capitalize font-semibold">Admin</p>
                    </div>
                </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2.5 rounded-full bg-gray-50 text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" x-show="!mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" x-show="mobileMenuOpen" style="display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu (AlpineJS toggled) -->
    <div x-show="mobileMenuOpen" style="display: none;" class="md:hidden px-4 pt-2 pb-4 space-y-1.5 border-t border-gray-100 bg-white text-xs font-semibold">
        <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            Dashboard
        </a>
        <a href="{{ route('patients.index') }}" class="block px-4 py-3 rounded-xl {{ request()->routeIs('patients.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            Patients
        </a>
        <a href="{{ route('doctors.index') }}" class="block px-4 py-3 rounded-xl {{ request()->routeIs('doctors.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            Doctors
        </a>
        <a href="{{ route('appointments.index') }}" class="block px-4 py-3 rounded-xl {{ request()->routeIs('appointments.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            Appointments
        </a>
        <a href="{{ route('bills.index') }}" class="block px-4 py-3 rounded-xl {{ request()->routeIs('bills.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            Billing
        </a>
        <a href="{{ Route::has('reports.index') ? route('reports.index') : '#' }}" class="block px-4 py-3 rounded-xl {{ request()->routeIs('reports.*') ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            Reports
        </a>
    </div>
</nav>