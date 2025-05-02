@props([
    'class' => '', // Properti untuk menerima kelas tambahan
])

<tbody class="divide-y divide-gray-200 bg-white {{ $class }}">
    {{ $slot }}
</tbody>