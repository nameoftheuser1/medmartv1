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

                        <div class="mb-4">
                            <label for="captcha">Captcha:</label>
                            <div class="captcha flex gap-4 justify-center">
                                <span style="width: 250px; height: 100px;">{!! captcha_img() !!}</span>
                                <button type="button" class="btn btn-success" id="refresh-captcha">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </button>
                            </div>
                            <input class="input" type="text" id="captcha" name="captcha">
                            @error('captcha')
                                <p class="error">{{ $message }}</p>
                            @enderror
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

<script>
    document.getElementById('refresh-captcha').addEventListener('click', function() {
        fetch('/refresh-captcha')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.captcha span').innerHTML = data.captcha;
            });
    });
</script>
