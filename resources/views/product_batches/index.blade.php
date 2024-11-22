<x-layout>
    <div class="w-full p-5 px-4 bg-white rounded-lg shadow-lg sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-between mb-6 sm:flex-row">
            <h1 class="mb-3 text-2xl font-bold sm:mb-0">Product Batch List</h1>
            <p class="mb-3 text-sm text-gray-600 sm:mb-0">Total Product Batches: {{ $productBatches->total() }}</p>
            <form method="GET" action="{{ route('product_batches.index') }}"
                class="flex flex-col w-full sm:flex-row sm:w-auto gap-4 sm:gap-6">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full px-4 py-2 mb-3 border border-gray-300 rounded-lg sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:mb-0">

                <select name="sort"
                    class="px-4 py-2 mb-3 border border-gray-300 rounded-lg sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:mb-0"
                    onchange="this.form.submit()">
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Ascending Expiration Date
                    </option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Descending Expiration Date
                    </option>
                </select>
            </form>
        </div>

        <div class="mb-2">
            This page displays a comprehensive list of all product batches in the inventory. You can search for specific
            batches, sort them by their expiration dates, and view essential details such as batch number, expiration
            date, supplier price, and product price. Additionally, you can manage product batches by viewing, editing,
            or deleting entries.
        </div>

        <div class="flex mb-5">
            <a href="{{ route('product_batches.create') }}" class="btn text-lg w-full sm:w-auto text-center">Add Product
                Batch</a>
        </div>

        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

        <div class="relative overflow-x-auto sm:rounded-lg" id="product-table">
            @include('product_batches.partials._table', ['productBatches' => $productBatches])
        </div>

        <div class="mt-6">
            {{ $productBatches->appends(['search' => request('search'), 'sort' => request('sort', 'asc')])->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Function to perform search and sort
            function fetchProductBatches() {
                const search = $('input[name="search"]').val();
                const sort = $('select[name="sort"]').val();

                $.ajax({
                    url: '{{ route('product_batches.index') }}',
                    type: 'GET',
                    data: {
                        search: search,
                        sort: sort
                    },
                    success: function(response) {
                        $('#product-table').html(response);
                    },
                    error: function(xhr) {
                        console.error('AJAX error:', xhr);
                        alert('An error occurred while fetching product batches.');
                    }
                });
            }

            // Trigger fetchProductBatches when search or sort changes
            $('input[name="search"], select[name="sort"]').on('input change', function() {
                fetchProductBatches();
            });
        });
    </script>
</x-layout>
