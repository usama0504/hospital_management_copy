<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">
                    HospitalMS
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md font-medium @if(request()->routeIs('dashboard')) bg-indigo-100 @endif">
                    Dashboard
                </a>
                <a href="{{ route('patients.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md font-medium @if(request()->routeIs('patients.*')) bg-indigo-100 @endif">
                    Patients
                </a>
                <a href="{{ route('doctors.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md font-medium @if(request()->routeIs('doctors.*')) bg-indigo-100 @endif">
                    Doctors
                </a>
                <a href="{{ route('appointments.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md font-medium @if(request()->routeIs('appointments.*')) bg-indigo-100 @endif">
                    Appointments
                </a>
                <a href="{{ route('bills.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md font-medium @if(request()->routeIs('bills.*')) bg-indigo-100 @endif">
                    Billing
                </a>
                <a href="{{ route('reports.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md font-medium @if(request()->routeIs('reports.*')) bg-indigo-100 @endif">
                    Reports
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden px-2 pt-2 pb-3 space-y-1">
        <a href="{{ route('dashboard') }}" class="block text-gray-700 hover:bg-indigo-100 px-3 py-2 rounded-md font-medium">
            Dashboard
        </a>
        <a href="{{ route('patients.index') }}" class="block text-gray-700 hover:bg-indigo-100 px-3 py-2 rounded-md font-medium">
            Patients
        </a>
        <a href="{{ route('doctors.index') }}" class="block text-gray-700 hover:bg-indigo-100 px-3 py-2 rounded-md font-medium">
            Doctors
        </a>
        <a href="{{ route('appointments.index') }}" class="block text-gray-700 hover:bg-indigo-100 px-3 py-2 rounded-md font-medium">
            Appointments
        </a>
        <a href="{{ route('bills.index') }}" class="block text-gray-700 hover:bg-indigo-100 px-3 py-2 rounded-md font-medium">
            Billing
        </a>
        <a href="{{ route('reports.index') }}" class="block text-gray-700 hover:bg-indigo-100 px-3 py-2 rounded-md font-medium">
            Reports
        </a>
    </div>

    <script>
        // Simple mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            var menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</nav>
