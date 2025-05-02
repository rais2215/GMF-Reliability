<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Setting') }}
        </h2>
    </x-slot>
    
    <div class="mx-8 py-6 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <x-section-title>
                <x-slot name="title">
                    Admin Power
                </x-slot>
                <x-slot name="description">
                    Only Admin can see this User Setting page.
                </x-slot>
            </x-section-title>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <x-third-button as="a" href="/users/create">
                    Add user
                </x-third-button>
            </div>
        </div>

        <div class="mt-8 flow-root">
        
            <x-table.index>
                <x-table.thead>
                    <tr>
                        <x-table.th>#</x-table.th>
                        <x-table.th>Name</x-table.th>
                        <x-table.th>Position</x-table.th>
                        <x-table.th>Email</x-table.th>
                        <x-table.th>Action</x-table.th>
                    </tr>
                </x-table.thead>
                <x-table.tbody>
                    @foreach ($users as $user)
                        <tr>
                            <x-table.td class="py-4">{{ $loop->iteration }}</x-table.td>
                            <x-table.td class="py-4">{{ $user->name }}</x-table.td>
                            <x-table.td class="py-4">{{ $user->Position }}</x-table.td>
                            <x-table.td class="py-4">{{ $user->email }}</x-table.td>
                            <x-table.td class="py-4">
                                <a href="/users/{{ $user->id }}" class="hover:text-black">View</a>
                                |
                                <a href="/users/{{ $user->id }}/edit" class="hover:text-blue-500">Edit</a>
                            </x-table.td>
                        </tr>
                    @endforeach
                </x-table.tbody>
            </x-table.index>

        </div>
    </div>
    
    

</x-app-layout>
