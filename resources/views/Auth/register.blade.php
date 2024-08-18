<x-layout>
    <div class="flex flex-col md:flex-row items-center justify-between lg:mt-44">

        <div class="w-full">
            <img class="my-10 ms-5 md:mt-0 " src="{{ asset('img/alegre.png') }}" alt="">
        </div>

        <div class="w-full">
            <div class="card m-7">
                <h1 class="text-center mb-4">@lang('message.register_one')</h1>
                <div class="mx-10">
                    <form action="{{ route('register') }}" method="post">
                        @csrf

                        <div class="mb-4">
                            <label for="name">Full name:</label>
                            @php
                                $translatedFullname = __('message.fullname');
                                $translatedEmail = __('message.email');
                                $translatedPassword = __('message.password');
                                $translatedConfirm = __('message.confirm');
                            @endphp
                            <x-tooltip message="{{ $translatedFullname }}">
                                <input class="input" type="text" name="name" id="name"
                                    placeholder="San Diego" value="{{ old('name') }}">
                            </x-tooltip>
                            @error('name')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email">Email:</label>
                            <x-tooltip message="{{ $translatedEmail }}">
                                <input class="input" type="text" name="email" id="email"
                                    placeholder="example@gmail.com" value="{{ old('email') }}">
                            </x-tooltip>
                            @error('email')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password">Password:</label>
                            <x-tooltip message="{{ $translatedPassword }}">
                                <input class="input" type="password" name="password" id="password"
                                    placeholder="***********" value="{{ old('password') }}">
                            </x-tooltip>
                            @error('password')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation">Confirm Password:</label>
                            <x-tooltip message="{{ $translatedConfirm }}">
                                <input class="input" type="password" name="password_confirmation"
                                    id="password_confirmation" placeholder="***********">
                            </x-tooltip>
                        </div>
                        <button type="submit" class="btn mx-auto w-full">@lang('message.register')</button>
                    </form>
                    <div class="grid grid-cols-1 divide-y-[2px] divide-gray-300">

                        <div class="text-center mb-7 text-blue-500"><a
                                href="{{ route('login') }}">@lang('message.account')</a></div>

                        <div class="text-gray-600 text-sm mb-2">@lang('message.authnote_one')

                            <div class="flex justify-center gap-1 mt-4">
                                <a href="locale/en">English</a>| <a href="locale/fil">Filipino</a>
                            </div>
                            <p class="text-gray-400">BSU &#169; 2024</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layout>
