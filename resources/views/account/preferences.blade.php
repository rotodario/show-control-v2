<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Cuenta</p>
            <h2 class="text-2xl font-semibold text-slate-900">Preferencias</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('account.partials.nav')

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Preferencias de cuenta</h3>
                <p class="mt-2 max-w-3xl text-sm text-slate-500">
                    Aqui agruparemos opciones generales del espacio: comportamientos por defecto, importaciones,
                    calendarios, nomenclatura y futuras preferencias del flujo de trabajo.
                </p>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-dashed border-slate-300 p-5">
                        <p class="text-sm font-semibold text-slate-900">Importacion y calendario</p>
                        <p class="mt-2 text-sm text-slate-500">Zona prevista para defaults de ICS, Google Calendar y agenda.</p>
                    </div>
                    <div class="rounded-2xl border border-dashed border-slate-300 p-5">
                        <p class="text-sm font-semibold text-slate-900">Flujo de trabajo</p>
                        <p class="mt-2 text-sm text-slate-500">Zona prevista para defaults de formularios, estados y documentos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
