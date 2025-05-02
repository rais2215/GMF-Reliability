@props([
    'class' => '', // Properti untuk menerima kelas tambahan
])

<td class="whitespace-nowrap px-3 py-2 text-sm text-black border border-solid text-center {{ $class }}">
    {{ $slot }}
</td>