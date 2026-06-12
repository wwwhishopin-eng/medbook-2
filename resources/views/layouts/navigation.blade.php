<nav x-data="{ open: false }" class="bg-white border-b border-gray-100" dir="rtl">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('patients.index') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
                        <div style="width:36px;height:36px;background:linear-gradient(135deg,#2E5BFF,#1A3FDB);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                                <path d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <span style="font-size:16px;font-weight:800;color:#111A6B;font-family:'Vazirmatn',sans-serif;">MedBoard</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:mr-10 sm:flex" dir="rtl" style="gap:4px;">
                    <x-nav-link :href="route('appointments.quick')" :active="request()->routeIs('appointments.quick')" style="font-family:'Vazirmatn',sans-serif;">
                        ثبت نوبت
                    </x-nav-link>
                    <x-nav-link :href="route('appointments.calendar')" :active="request()->routeIs('appointments.calendar')" style="font-family:'Vazirmatn',sans-serif;">
                        تقویم
                    </x-nav-link>
                    <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')" style="font-family:'Vazirmatn',sans-serif;">
                        بیماران
                    </x-nav-link>
                    <x-nav-link :href="route('queue.index')" :active="request()->routeIs('queue.*')" style="font-family:'Vazirmatn',sans-serif;">
                        صف
                    </x-nav-link>
                    <x-nav-link :href="route('financial.debtors')" :active="request()->routeIs('financial.*')" style="font-family:'Vazirmatn',sans-serif;">
                        بدهکاران
                    </x-nav-link>
                    <x-nav-link :href="route('waiting-list.index')" :active="request()->routeIs('waiting-list.*')" style="font-family:'Vazirmatn',sans-serif;">
                        لیست انتظار
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" style="font-family:'Vazirmatn',sans-serif;">
                        داشبورد
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:mr-6">
                <x-dropdown align="left" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150" style="font-family:'Vazirmatn',sans-serif;">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="mr-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('subscription.show')" style="font-family:'Vazirmatn',sans-serif;">
                            مدیریت اشتراک
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.edit')" style="font-family:'Vazirmatn',sans-serif;">
                            پروفایل
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    style="font-family:'Vazirmatn',sans-serif;">
                                خروج
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('appointments.quick')" :active="request()->routeIs('appointments.quick')" style="font-family:'Vazirmatn',sans-serif;">
                ثبت نوبت
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('appointments.calendar')" :active="request()->routeIs('appointments.calendar')" style="font-family:'Vazirmatn',sans-serif;">
                تقویم
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')" style="font-family:'Vazirmatn',sans-serif;">
                بیماران
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('queue.index')" :active="request()->routeIs('queue.*')" style="font-family:'Vazirmatn',sans-serif;">
                صف
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('financial.debtors')" :active="request()->routeIs('financial.*')" style="font-family:'Vazirmatn',sans-serif;">
                بدهکاران
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('waiting-list.index')" :active="request()->routeIs('waiting-list.*')" style="font-family:'Vazirmatn',sans-serif;">
                لیست انتظار
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" style="font-family:'Vazirmatn',sans-serif;">
                داشبورد
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" style="font-family:'Vazirmatn',sans-serif;">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('subscription.show')" style="font-family:'Vazirmatn',sans-serif;">
                    مدیریت اشتراک
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('profile.edit')" style="font-family:'Vazirmatn',sans-serif;">
                    پروفایل
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            style="font-family:'Vazirmatn',sans-serif;">
                        خروج
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
