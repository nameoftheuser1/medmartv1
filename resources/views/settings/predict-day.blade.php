<x-layout>
    <nav class="bg-white shadow-md rounded mb-6 p-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 md:space-x-4">
            <li>
                <div class="flex items-center">
                    <a href="{{ route('settings') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Settings
                    </a>
                    <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 15.293a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414L10 12.586 5.707 8.293a1 1 0 00-1.414 1.414l5 5z" clip-rule="evenodd" /></svg>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <a href="{{ route('settings.edit.prediction') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        Prediction Day
                    </a>
                </div>
            </li>
        </ol>
    </nav>
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-semibold mb-6 text-gray-800">Edit Prediction Days</h1>

        <form action="{{ route('settings.update.prediction') }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            {{-- <div class="mb-6">
                <label for="predictedSalesDay" class="block text-gray-700 font-medium">Predicted Sales Day:</label>
                <input type="number" name="predictedSalesDay" id="predictedSalesDay"
                    value="{{ $settings['predictedSalesDay'] }}"
                    class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200"
                    required min="1">
            </div> --}}

            <div class="mb-6">
                <label for="historicalDataDays" class="block text-gray-700 font-medium">Historical Data Days:</label>
                <input type="number" name="historicalDataDays" id="historicalDataDays"
                    value="{{ $settings['historicalDataDays'] }}"
                    class="mt-2 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-200"
                    required min="1">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md transition duration-200">Update Settings</button>
        </form>
    </div>
</x-layout>
