<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ env('APP_NAME') }}</title>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('sidebar-overlay');
            overlay.addEventListener('click', toggleSidebar);
        });
    </script>
</head>

<body>
    @auth
        <nav class="bg-white p-4 flex justify-between fixed w-full z-50">
            <div class="flex items-center">
                <button class="flex mr-2 md:hidden" onclick="toggleSidebar()">
                    <span class="flex justify-center items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                        </svg>
                        Menu
                    </span>
                </button>
                <p class="mx-5 hidden sm:block">Hello, {{ auth()->user()->name }}</p>
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('img/alegre.png') }}" alt="" class="h-8">
                </a>
            </div>
        </nav>
        <div class="flex -z-50 overflow-x-scroll:h">
            <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden md:hidden"></div>
            <div id="sidebar"
                class="transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:flex flex-col justify-between fixed top-14 left-0 bottom-0 w-64 bg-white pt-2 px-3 z-50">
                <div>
                    <ul>
                        @if (Auth::check() && Auth::user()->role === 'admin')
                            <li class="mb-2 flex items-center">
                                <a href="{{ route('dashboard') }}"
                                    class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md {{ Route::currentRouteName() == 'dashboard' ? 'bg-gray-300 text-slate-700' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                        @endif
                        <li class="mb-2 flex items-center">
                            <a href="{{ route('pos.index') }}"
                                class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md {{ Route::currentRouteName() == 'pos.index' ? 'bg-gray-300 text-slate-700' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                                Point of sale</a>
                        </li>
                        <li class="mb-2 flex items-center">
                            <a href="{{ route('products.index') }}"
                                class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md {{ Route::currentRouteName() == 'products.index' || Route::currentRouteName() == 'addproducts' || Route::currentRouteName() == 'products.show' ? 'bg-gray-300 text-slate-700' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                Products</a>
                        </li>
                        <li class="mb-2 flex items-center">
                            <a href="{{ route('product_batches.index') }}"
                                class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md {{ Route::currentRouteName() == 'product_batches.index' ? 'bg-gray-300 text-slate-700' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 8.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v8.25A2.25 2.25 0 0 0 6 16.5h2.25m8.25-8.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-7.5A2.25 2.25 0 0 1 8.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 0 0-2.25 2.25v6" />
                                </svg>
                                Product Batches</a>
                        </li>
                        <li class="mb-2 flex items-center">
                            <a href="{{ route('inventories.index') }}"
                                class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md {{ Route::currentRouteName() == 'inventories.index' ? 'bg-gray-300 text-slate-700' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                                @lang('message.inventory')</a>
                        </li>
                        <li class="mb-2 flex items-center">
                            <a href="{{ route('suppliers.index') }}"
                                class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md {{ Route::currentRouteName() == 'suppliers.index' ? 'bg-gray-300 text-slate-700' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                Suppliers</a>
                        </li>
                        <li class="mb-2 flex items-center">
                            <a href="{{ route('sales.index') }}"
                                class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md {{ Route::currentRouteName() == 'sales.index' ? 'bg-gray-300 text-slate-700' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                                Sales</a>
                        </li>
                        <li class="mb-2 flex items-center">
                            <a href="{{ route('sale_details.index') }}"
                                class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md {{ Route::currentRouteName() == 'sale_details.index' ? 'bg-gray-300 text-slate-700' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                                </svg>
                                Sale Details</a>
                        </li>
                    </ul>
                </div>
                <div class="mt-auto mb-5">
                    <form action="{{ route('logout') }}" method="post" class="w-full">
                        @csrf
                        <button
                            class="w-full text-left hover:text-slate-700 hover:bg-gray-300 h-[40px] px-2 flex items-center gap-2 rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M9 12h12m0 0l-3-3m3 3l-3 3" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                    <div class="flex w-full justify-center gap-2"><a href="locale/en">English</a>|<a
                            href="locale/fil">Filipino</a></div>
                    <p class="text-center text-sm text-gray-400">BSU 2024</p>
                </div>
            </div>
            <div class="flex-1 mx-auto md:ps-72 md:pe-8 px-2 pt-20 overflow-x-hidden">
                {{ $slot }}
            </div>
        </div>
    @endauth

    @guest
        {{ $slot }}
    @endguest

</body>

</html>
