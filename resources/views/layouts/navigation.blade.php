<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo di Kiri -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/LogoAmerta.png') }}" alt="Logo" class="h-10 w-auto">
                </a>
            </div>

            <!-- Navigasi di Tengah -->
            <div class="hidden sm:flex space-x-8">
                @guest
                    <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                        {{ __('Beranda') }}
                    </x-nav-link>
                    
                    <x-nav-link href="{{ route('category') }}" :active="request()->routeIs('category')">
                        {{ __('Kategori') }}
                    </x-nav-link>
                    
                    <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products')">
                        {{ __('Produk') }}
                    </x-nav-link>
                    
                    <x-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                        {{ __('Tentang Kami') }}
                    </x-nav-link>
                @endguest

                @auth
                    <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                        {{ __('Beranda') }}
                    </x-nav-link>

                    <x-nav-link href="{{ route('category') }}" :active="request()->routeIs('category')">
                        {{ __('Kategori') }}
                    </x-nav-link>
                    
                    <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products')">
                        {{ __('Produk') }}
                    </x-nav-link>
                    
                    <x-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                        {{ __('Tentang Kami') }}
                    </x-nav-link>
                @endauth
            </div>

            <!-- Ikon Pencarian dan Pengguna di Kanan -->
            <div class="flex items-center space-x-4 hidden sm:flex">
                <!-- Pencarian -->
                <button class="inline-flex items-center pl-3 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <!-- Ikon Keranjang Belanja dengan Badge -->
                <button class="inline-flex items-center pl-3 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <a href="{{ route('cart.index') }}" class="relative">
                        <i class="fa-solid fa-shopping-bag text-base"></i>

                        <!-- Badge jumlah item -->
                        @if(isset($totalItems) && $totalItems > 0)
                        <span class="absolute start-2.5 bottom-3 inline-flex items-center justify-center w-3 h-3 text-xs font-semibold text-white bg-red-500 rounded-full">
                            {{ $totalItems }}
                        </span>
                        @endif
                    </a>
                </button>


                <!-- Dropdown untuk Pengguna -->
                @guest
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <i class="fa-regular fa-user text-base"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('login')">
                                {{ __('Login') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('register')">
                                {{ __('Register') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                @endguest

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            <!-- Hamburger untuk Mobile -->
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
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Beranda') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('category')" :active="request()->routeIs('category')">
                {{ __('Kategori') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products')">
                {{ __('Produk') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                {{ __('Tentang Kami') }}
            </x-responsive-nav-link>
            <!-- Cart -->
            <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart')">
                {{ __('Keranjang') }}
            </x-responsive-nav-link> 
            
            <hr>

            <!-- Dropdown untuk Pengguna -->
            @guest
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                    {{ __('Login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            @endguest

            @auth
            
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    {{ Auth::user()->name }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            @endauth

            {{-- <!-- Pencarian -->
            <x-responsive-nav-link href="#" class="text-gray-700">
                <i class="fa-solid fa-magnifying-glass"></i> {{ __('Pencarian') }}
            </x-responsive-nav-link> --}}
        </div>
    </div>
</nav>