<x-layout>
    <div class="flex flex-col md:flex-row items-center justify-between lg:mt-44">
        <div class="w-full">
            <img class="my-10  md:mt-0 md:ms-0" src="{{ asset('img/alegre.png') }}" alt="">
        </div>

        <div class="w-full">
            <div class="card m-7">
                <h1 class="text-center mb-4">@lang('message.welcome_back')</h1>
                <div class="mx-10">
                    <form action="{{ route('login') }}" method="post">
                        @csrf

                        @error('failed')
                            <p class="error"> {{ $message }}</p>
                        @enderror

                        <div class="mb-4">
                            <label for="email">Email:</label>
                            <input class="input" type="text" name="email" id="email"
                                placeholder="example@gmail.com" value="{{ old('email') }}">
                            @error('email')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password">Password:</label>

                            <input class="input" type="password" name="password" id="password"
                                placeholder="***********" value="{{ old('password') }}">

                            @error('password')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn mx-auto w-full mb-5">@lang('message.login')</button>
                    </form>
                    <div class="grid grid-cols-1 divide-y-[2px] divide-gray-300">

                        {{-- <div class="flex justify-center gap-1">
                            <a href="locale/en">English</a>| <a href="locale/fil">Filipino</a>
                        </div> --}}
                    </div>
                    <p class="text-gray-400 text-[12px]">BSU &#169; 2024</p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
