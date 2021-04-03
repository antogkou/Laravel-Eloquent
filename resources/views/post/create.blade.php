<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('post.store') }}">
                @csrf

                <!-- Title -->
                    <div>
                        <x-label for="title" :value="__('Title')"/>

                        <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')"
                                 required
                                 autofocus/>
                    </div>

                    <!-- Body -->
                    <div class="mt-4">
                        <x-label for="body" :value="__('Body')"/>

                        <x-input id="body" class="block mt-1 w-full" type="text" name="body"
                                 :value="old('body')" required/>
                    </div>


                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4">
                            {{ __('Submit Post') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
