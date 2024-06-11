<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ env('APP_NAME') }}</title>
</head>

<body>
    @auth
        <nav class="bg-white p-4 flex justify-between fixed w-full z-50">
            <div>
                <a href="">
                    <img src="{{ asset('storage/img/logo.png') }}" alt="" class="h-8">
                </a>
            </div>
            <div class="flex items-center">
                <img class="w-8 h-8 rounded-full" src="{{ asset('storage/img/user.png') }}" alt="">
                <p class="mx-5">Hello, {{ auth()->user()->name }}</p>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="block text-slate-700 hover:bg-gray-100  w-full">
                        <span class="flex-1 ms-3 whitespace-nowrap">Log out</span>
                    </button>
                </form>
            </div>
        </nav>
        <div class="flex -z-50">
            <!-- Left Sidebar -->
            <div class="hidden md:block fixed top-14 left-0 bottom-0 w-64 bg-inherit pt-2 px-3">
                <h2 class="text-lg font-semibold mb-4"></h2>
                <ul>
                    <li class="mb-2 flex items-center">
                        <a href="{{ route('dashboard') }}"
                            class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center rounded-md {{ Route::currentRouteName() == 'dashboard' ? 'bg-gray-300 text-slate-700' : '' }}">Dashboard</a>
                    </li>
                    <li class="mb-2 flex items-center">
                        <a href="{{ route('products.index') }}"
                            class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center rounded-md {{ Route::currentRouteName() == 'products.index' || Route::currentRouteName() == 'addproducts' || Route::currentRouteName() == 'products.show' ? 'bg-gray-300 text-slate-700' : '' }}">Products</a>
                    </li>
                    <li class="mb-2 flex items-center">
                        <a href="{{ route('product_batches.index') }}"
                            class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center rounded-md {{ Route::currentRouteName() == 'product_batches.index' ? 'bg-gray-300 text-slate-700' : '' }}">Product
                            Batches</a>
                    </li>
                    <li class="mb-2 flex items-center">
                        <a href="{{ route('inventories.index') }}"
                            class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center rounded-md {{ Route::currentRouteName() == 'inventory.index' ? 'bg-gray-300 text-slate-700' : '' }}">Inventories</a>
                    </li>
                    <li class="mb-2 flex items-center">
                        <a href="{{ route('suppliers.index') }}"
                            class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center rounded-md {{ Route::currentRouteName() == 'suppliers.index' ? 'bg-gray-300 text-slate-700' : '' }}">Suppliers</a>
                    </li>
                    <li class="mb-2 flex items-center">
                        <a href="{{ route('sales.index') }}"
                            class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center rounded-md {{ Route::currentRouteName() == 'sales.index' ? 'bg-gray-300 text-slate-700' : '' }}">Sales</a>
                    </li>
                    <li class="mb-2 flex items-center">
                        <a href="{{ route('sale_details.index') }}"
                            class="hover:text-slate-700 w-full hover:bg-gray-300 h-[40px] px-2 flex items-center rounded-md {{ Route::currentRouteName() == 'sale_details.index' ? 'bg-gray-300 text-slate-700' : '' }}">Sale Details</a>
                    </li>


                    <!-- Add more links as needed -->
                </ul>
            </div>

            <!-- Main Content -->
            <div class="flex-1 mx-auto md:ps-72 md:pe-8 px-4 pt-20">
                <!-- Apply different padding for mobile and larger screens -->
                {{ $slot }}
            </div>

        </div>
    @endauth

    @guest
        {{ $slot }}
    @endguest

</body>

</html>
