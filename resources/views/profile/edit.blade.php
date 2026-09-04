<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-sm sm:rounded-xl transition-colors duration-200">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-sm sm:rounded-xl transition-colors duration-200">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-sm sm:rounded-xl transition-colors duration-200">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
