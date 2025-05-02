@props([
    'class' => '', // Properti untuk menerima kelas tambahan
])

<thead class="bg-gray-100 {{ $class }}">
    {{ $slot }}
</thead>