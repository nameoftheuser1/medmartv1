<x-layout>
    <div class="w-full p-5 px-4 bg-white rounded-lg shadow-lg sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-between mb-5 sm:flex-row">
            <h1 class="mb-2 text-2xl font-bold sm:mb-0">Product List</h1>
            <p class="mb-2 text-sm sm:mb-0">@lang('message.total') Products: {{ $products->total() }}</p>
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col w-full sm:flex-row sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full px-4 py-2 mb-2 border border-gray-300 rounded-lg sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:mb-0">

                <select name="category"
                    class="w-full px-4 py-2 mb-2 ml-2 border border-gray-300 rounded-lg sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:mb-0"
                    onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <option value="Medications" {{ request('category') == 'Medications' ? 'selected' : '' }}>Medications
                    </option>
                    <option value="Supplements" {{ request('category') == 'Supplements' ? 'selected' : '' }}>Supplements
                    </option>
                    <option value="Personal Care" {{ request('category') == 'Personal Care' ? 'selected' : '' }}>
                        Personal Care</option>
                    <option value="First Aid" {{ request('category') == 'First Aid' ? 'selected' : '' }}>First Aid
                    </option>
                    <option value="Medical Equipment"
                        {{ request('category') == 'Medical Equipment' ? 'selected' : '' }}>Medical Equipment</option>
                    <option value="Baby Products" {{ request('category') == 'Baby Products' ? 'selected' : '' }}>Baby
                        Products</option>
                    <option value="Health Devices" {{ request('category') == 'Health Devices' ? 'selected' : '' }}>
                        Health Devices</option>
                    <option value="Homeopathic Remedies"
                        {{ request('category') == 'Homeopathic Remedies' ? 'selected' : '' }}>Homeopathic Remedies
                    </option>
                    <option value="Herbal Products" {{ request('category') == 'Herbal Products' ? 'selected' : '' }}>
                        Herbal Products</option>
                    <option value="Skin Care" {{ request('category') == 'Skin Care' ? 'selected' : '' }}>Skin Care
                    </option>
                    <option value="Hair Care" {{ request('category') == 'Hair Care' ? 'selected' : '' }}>Hair Care
                    </option>
                    <option value="Oral Care" {{ request('category') == 'Oral Care' ? 'selected' : '' }}>Oral Care
                    </option>
                    <option value="Sexual Health" {{ request('category') == 'Sexual Health' ? 'selected' : '' }}>Sexual
                        Health</option>
                    <option value="Eye Care" {{ request('category') == 'Eye Care' ? 'selected' : '' }}>Eye Care
                    </option>
                    <option value="Ear Care" {{ request('category') == 'Ear Care' ? 'selected' : '' }}>Ear Care
                    </option>
                    <option value="Nutrition" {{ request('category') == 'Nutrition' ? 'selected' : '' }}>Nutrition
                    </option>
                    <option value="Wellness" {{ request('category') == 'Wellness' ? 'selected' : '' }}>Wellness
                    </option>
                </select>

            </form>
        </div>
        <div class="mb-2">
            This page provides a comprehensive overview of the products available in our system. It is designed to help
            you easily navigate and manage the product inventory.
        </div>

        <div class="flex flex-col sm:flex-row sm:justify-between items-center mb-4 space-y-2 sm:space-y-0 sm:space-x-3">
            <a
                href="{{ route('products.create') }}"
                class="w-full sm:w-auto text-sm text-center px-4 py-2 btn btn-primary">
                @lang('message.add', ['item' => 'Product'])
            </a>
            <a
                href="{{ route('products.export') }}"
                class="w-full sm:w-auto text-sm text-center px-4 py-2 btn bg-green-500 text-white hover:bg-green-600">
                Export to Excel
            </a>
        </div>


        <!-- Print Button -->

        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

        <!-- Include the _table partial -->
        <div id="product-table">
            @include('products.partials._table', ['products' => $products])
        </div>

        <div class="mt-4">
            {{ $products->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>


    </div>

    <script>
        $(document).ready(function() {
            $('input[name="search"], select[name="category"]').on('input change', function() {
                let search = $('input[name="search"]').val();
                let category = $('select[name="category"]').val();

                $.ajax({
                    url: '{{ route('products.index') }}',
                    type: 'GET',
                    data: {
                        search: search,
                        category: category
                    },
                    success: function(response) {
                        $('#product-table').html(response);
                    },
                    error: function(xhr) {
                        console.error('AJAX error:', xhr);
                        alert('An error occurred while searching products');
                    }
                });
            });
        });


    </script>

</x-layout>
