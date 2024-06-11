<x-layout>
    <div class="flex flex-col md:flex-row items-center justify-between lg:mt-44">
        <div class="w-full">
            <img class="my-10 ms-5  md:mt-0 md:ms-0" src="{{ asset('storage/img/logo.png') }}" alt="">
        </div>

        <div class="w-full">
            <div class="card m-7">
                <h1 class="text-center mb-4">Welcome Back</h1>
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

                        <div class="mb-4">
                            <input type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Remember Me</label>
                        </div>

                        <button type="submit" class="btn mx-auto w-full">Login as Staff</button>
                    </form>
                    <div class="grid grid-cols-1 divide-y-[2px] divide-gray-300">

                        <div class="text-center mb-7 text-blue-500"><a href="">Forgot
                                Password?</a></div>

                        <div class="w-full flex flex-col justify-center p-4">
                            <a class="mx-auto" href="{{ route('register') }}">
                                <button class="btn">Create new account</button>
                            </a>
                            <div class="text-gray-600 text-sm">In order to use the point of sale for the employee they
                                should create an separate account.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
