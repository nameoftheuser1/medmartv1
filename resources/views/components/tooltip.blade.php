<!-- An unexamined life is not worth living. - Socrates -->
<div class="relative group">
    <div class="inline-block w-full">
        {{ $slot }}
    </div>
    <div
        class="absolute transform -translate-y-full -translate-x-full mb-2 hidden group-hover:block bg-gray-800 text-white text-sm rounded py-2 px-3 shadow-lg z-10 max-w-48">
        {{ $message }}
    </div>
</div>
