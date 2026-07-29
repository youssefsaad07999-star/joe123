<x-layout>
    <div class="min-h-[calc(100vh-64px)] flex">

        {{-- Left Panel --}}
        <div class="hidden lg:flex w-1/2 bg-[#1C1C1C] items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-30"
                style="background-image: radial-gradient(circle at 30% 70%, #C85C6E 0%, transparent 50%)">
            </div>
            <div class="relative text-center px-12">
                <span class="font-['Cormorant_Garamond'] text-6xl font-light text-white tracking-widest">JOE</span>
                <p class="text-gray-400 mt-4 font-light text-lg leading-relaxed">
                    Fashion for every you.<br>Your style, your story.
                </p>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-[#F7F3EE]">
            <div class="w-full max-w-md">

                <div class="mb-8">
                    <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">Welcome back</p>
                    <h1 class="font-['Cormorant_Garamond'] text-4xl font-light">Sign In</h1>
                </div>

                <form action="{{ route('login.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <x-form.field name="email" title="Email Address" type="email" placeholder="you@example.com" />
                    <x-form.field name="password" title="Password" type="password" placeholder="••••••••" />

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E]">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-[#C85C6E] hover:underline">Forgot
                            password?</a>

                    </div>

                    <button type="submit"
                        class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium
                                   hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide mt-2">
                        Sign In
                    </button>
                </form>

                <p class="text-center text-gray-500 text-sm mt-6">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-[#C85C6E] font-medium hover:underline ml-1">Create
                        one</a>
                </p>

            </div>
        </div>
    </div>
</x-layout>
