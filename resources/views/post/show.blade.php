<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Show Post') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <x-nav-link :href="route('post.index')">
            {{ __('back') }}
          </x-nav-link>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p class="font-extrabold">  {{ $post->title }}</p>
                    <p> {{ $post->body }} </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
