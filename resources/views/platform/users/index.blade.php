<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Plataforma</p>
                <h2 class="text-2xl font-semibold text-slate-900">Usuarios</h2>
            </div>
            <p class="max-w-2xl text-sm text-slate-500">
                Gestion global de cuentas registradas, roles de plataforma y estado de acceso.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
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
                    <h3 class="text-lg font-semibold text-slate-900">Cuentas registradas</h3>
                    <p class="mt-1 text-sm text-slate-500">Puedes cambiar rol global y activar o desactivar acceso.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Usuario</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4">Rol</th>
                                <th class="px-6 py-4">Uso</th>
                                <th class="px-6 py-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($users as $user)
                                <tr class="align-top">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $user->email }}</p>
                                        <p class="mt-2 text-xs text-slate-400">Alta: {{ $user->created_at?->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $user->is_active ? 'Activa' : 'Desactivada' }}
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
                                        <p>{{ $user->tours_count }} giras</p>
                                        <p class="mt-1">{{ $user->shows_count }} bolos</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('platform.users.update', $user) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')

                                            <div>
                                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Rol</label>
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
                                                Cuenta activa
                                            </label>

                                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                                Guardar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No hay usuarios registrados.
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
