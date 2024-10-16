<x-layout>
    <nav class="bg-white shadow-md rounded mb-4 p-3" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <div class="flex items-center justify-center">
                    <a href="{{ route('settings') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Settings
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center justify-center">
                    <a href="{{ route('settings') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Change Password
                    </a>
                </div>
            </li>
        </ol>
    </nav>

    <div class="max-w-md mx-auto mt-10 p-6 bg-white shadow-md rounded">

        <h2 class="text-2xl font-semibold mb-4">Change Password</h2>

        @if ($errors->any())
            <div class="mb-4">
                <ul class="list-disc list-inside text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('settings.update.password') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                    class="p-2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>

            <div class="mb-4">
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" id="new_password" name="new_password" required
                    class="p-2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New
                    Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="p-2 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</x-layout>
