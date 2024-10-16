<x-layout>
    <div class="container mx-auto p-4">
        <!-- Breadcrumbs -->
        <nav class="bg-white shadow-md rounded mb-4 p-3" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li>
                    <div class="flex items-center justify-center">
                        <a href="{{ route('settings') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            Settings
                        </a>
                    </div>
                </li>

            </ol>
        </nav>

        <h1 class="text-3xl font-semibold mb-6">Settings</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Account Settings</h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('settings.edit.password') }}" class="text-blue-600 hover:underline">Edit Password</a>
                </li>
                <li>
                    <a href="{{ route('settings.edit.prediction') }}" class="text-blue-600 hover:underline">Days
                        Prediction</a>
                </li>
            </ul>
        </div>
    </div>
</x-layout>
