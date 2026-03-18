@if ($errors->any())
    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-800">
        <p class="font-semibold">No se ha podido guardar el formulario.</p>
        <ul class="mt-2 list-disc space-y-1 ps-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
