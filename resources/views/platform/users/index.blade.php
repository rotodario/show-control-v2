<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ __('ui.platform') }}</p>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('ui.users') }}</h2>
            </div>
            <p class="max-w-2xl text-sm text-slate-500">
                {{ __('ui.platform_users_description') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('platform.partials.nav')

            @if (session('platform_status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('platform_status') }}
                </div>
            @endif

            @if (session('platform_error'))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700">
                    {{ session('platform_error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.registered_accounts') }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ __('ui.registered_accounts_help') }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <tr>
                                <th class="px-6 py-4">{{ __('ui.user') }}</th>
                                <th class="px-6 py-4">{{ __('ui.status') }}</th>
                                <th class="px-6 py-4">{{ __('ui.role') }}</th>
                                <th class="px-6 py-4">{{ __('ui.usage') }}</th>
                                <th class="px-6 py-4">{{ __('ui.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($users as $user)
                                <tr class="align-top">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $user->email }}</p>
                                        <p class="mt-2 text-xs text-slate-400">{{ __('ui.created_at_label') }}: {{ $user->created_at?->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $user->is_active ? __('ui.active_female') : __('ui.inactive_female') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($user->roles as $role)
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <p>{{ $user->tours_count }} {{ __('ui.tours') }}</p>
                                        <p class="mt-1">{{ $user->shows_count }} {{ __('ui.shows') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('platform.users.update', $user) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')

                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ __('ui.role') }}</label>
                                                <select name="role" class="block w-full rounded-2xl border-slate-300 text-sm shadow-sm focus:border-slate-400 focus:ring-slate-400">
                                                    @foreach ($assignableRoles as $roleValue => $roleLabel)
                                                        <option value="{{ $roleValue }}" @selected($user->hasRole($roleValue))>{{ $roleLabel }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <label class="inline-flex items-center gap-3 text-sm text-slate-700">
                                                <input type="hidden" name="is_active" value="0">
                                                <input
                                                    type="checkbox"
                                                    name="is_active"
                                                    value="1"
                                                    class="rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                                                    @checked($user->is_active)
                                                >
                                                {{ __('ui.active_account') }}
                                            </label>

                                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                                {{ __('ui.save') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                        {{ __('ui.no_registered_users') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
