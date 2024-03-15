@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-purple-400 flex justify-center items-center">
        <div
            class="absolute w-60 h-60 rounded-xl bg-purple-300 -top-5 -left-16 z-0 transform rotate-45 hidden md:block"></div>
        <div
            class="absolute w-48 h-48 rounded-xl bg-purple-300 -bottom-6 -right-10 transform rotate-12 hidden md:block"></div>
        <form class="py-12 px-12 bg-white rounded-2xl shadow-xl z-20" action="{{ route('authenticate') }}"
              method="POST">
            @csrf
            <div>
                <h1 class="text-3xl font-bold text-center mb-4 cursor-pointer">Tizimga kirish oynasi</h1>
            </div>
            <div class="space-y-4">
                <label>
                    <input type="text" name="login" placeholder="Login"
                           class="block text-sm py-3 px-4 rounded-lg w-full border outline-purple-500"/>
                </label><br>
                <label>
                    <input type="password" name="password" placeholder="Password"
                           class="block text-sm py-3 px-4 rounded-lg w-full border outline-purple-500"/>
                </label>
            </div>
            <div class="text-center mt-6">
                <button
                    class="w-full py-2 text-xl text-white bg-purple-400 rounded-lg hover:bg-purple-500 transition-all">
                    Kirish
                </button>
            </div>
        </form>
        <div class="w-40 h-40 absolute bg-purple-300 rounded-full top-0 right-12 hidden md:block"></div>
        <div
            class="w-20 h-40 absolute bg-purple-300 rounded-full bottom-20 left-10 transform rotate-45 hidden md:block"></div>
    </div>
@endsection
