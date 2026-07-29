<x-layout>
    <div class="min-h-[calc(100vh-64px)] flex">

        {{-- Left Panel --}}
        <div class="hidden lg:flex w-1/2 bg-[#1C1C1C] items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-30"
                style="background-image: radial-gradient(circle at 70% 30%, #C85C6E 0%, transparent 50%)">
            </div>
            <div class="relative text-center px-12">
                <span class="font-['Cormorant_Garamond'] text-6xl font-light text-white tracking-widest">JOE</span>
                <p class="text-gray-400 mt-4 font-light text-lg leading-relaxed">
                    Join thousands of fashion lovers.<br>Create your free account today.
                </p>
                <div class="flex justify-center gap-8 mt-10">
                    @foreach (['Free Returns', 'Style Tips', 'Early Access'] as $perk)
                        <div class="text-center">
                            <div
                                class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center mx-auto mb-2">
                                <span class="text-[#C85C6E] text-sm">✦</span>
                            </div>
                            <p class="text-gray-400 text-xs font-light">{{ $perk }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-[#F7F3EE]">
            <div class="w-full max-w-md">

                <div class="mb-8">
                    <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-2">
                        Get Started
                    </p>
                    <h1 class="font-['Cormorant_Garamond'] text-4xl font-light">Create Account</h1>
                </div>

                <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Name fields structured cleanly with a responsive grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.field name="first_name" title="First Name" placeholder="John" />
                        <x-form.field name="last_name" title="Last Name" placeholder="Doe" />
                    </div>

                    {{-- Rest of your form inputs go here... --}}


                    {{-- date_of_birth → users.date_of_birth (date column) --}}
                    <x-form.field name="date_of_birth" title="Date of Birth" type="date" placeholder="" />

                    {{-- phone_number → users.phone_number --}}
                    <x-form.field name="phone_number" title="Phone Number" type="tel"
                        placeholder="+20 10 1234 5678" />

                    {{-- email → users.email --}}
                    <x-form.field name="email" title="Email Address" type="email" placeholder="you@example.com" />

                    {{-- password + strength meter --}}
                    <div>
                        <x-form.field name="password" title="Password" type="password"
                            placeholder="Min. 8 characters" />

                        {{-- Strength bar — driven by Alpine watching the input --}}
                        <div class="mt-2" x-data="{
                            strength: 0,
                            check(val) {
                                let s = 0;
                                if (val.length >= 8) s++;
                                if (/[A-Z]/.test(val)) s++;
                                if (/[0-9]/.test(val)) s++;
                                if (/[^A-Za-z0-9]/.test(val)) s++;
                                this.strength = s;
                            }
                        }">
                            <div class="flex gap-1.5"
                                x-on:input.window="check($event.target.name === 'password' ? $event.target.value : '')">
                                @for ($i = 1; $i <= 4; $i++)
                                    <div class="h-1 flex-1 rounded-full transition-colors duration-300"
                                        :class="{
                                            'bg-red-400': strength >= {{ $i }} && strength === 1,
                                            'bg-amber-400': strength >= {{ $i }} && strength === 2,
                                            'bg-yellow-400': strength >= {{ $i }} && strength === 3,
                                            'bg-emerald-400': strength >= {{ $i }} && strength === 4,
                                            'bg-gray-200': strength < {{ $i }}
                                        }">
                                    </div>
                                @endfor
                            </div>
                            <p class="text-xs mt-1 transition-colors"
                                :class="{
                                    'text-red-400': strength === 1,
                                    'text-amber-400': strength === 2,
                                    'text-yellow-500': strength === 3,
                                    'text-emerald-500': strength === 4,
                                    'text-transparent': strength === 0
                                }"
                                x-text="['','Weak','Fair','Good','Strong'][strength]">
                            </p>
                        </div>
                    </div>

                    {{-- password_confirmation — not stored, used for validation only --}}
                    <x-form.field name="password_confirmation" title="Confirm Password" type="password"
                        placeholder="Repeat your password" />

                    <label class="flex items-start gap-3 cursor-pointer pt-1">
                        <input type="checkbox" name="terms"
                            class="rounded border-gray-300 text-[#C85C6E] focus:ring-[#C85C6E] mt-0.5">
                        <span class="text-sm text-gray-600">
                            I agree to the
                            <a href="#" class="text-[#C85C6E] underline">Terms of Service</a> and
                            <a href="#" class="text-[#C85C6E] underline">Privacy Policy</a>
                        </span>
                    </label>
                    <x-form.error name="terms" />

                    <button type="submit"
                        class="w-full bg-[#1C1C1C] text-white py-4 rounded-full font-medium
                               hover:bg-[#C85C6E] transition-colors duration-300 text-sm tracking-wide mt-2">
                        Create Account
                    </button>
                </form>

                <p class="text-center text-gray-500 text-sm mt-6">
                    Already have an account?
                    <a href="/login" class="text-[#C85C6E] font-medium hover:underline ml-1">Sign in</a>
                </p>

            </div>
        </div>
    </div>
</x-layout>
