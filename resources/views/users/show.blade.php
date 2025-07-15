<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}
        </h2>
    </x-slot>

    <div class="mx-10 mt-4">
        <div>Email : {{ $user->email }}</div>
        <div>Password : {{ $user->password }}</div>
        <div>Position as {{ $user->Position }}</div>
        <div>Registered at {{ $user->created_at->diffForHumans() }}</div>

        <!-- Trigger Button -->
        <button onclick="document.getElementById('deleteModal').classList.remove('hidden');document.getElementById('deleteModal').classList.add('flex')" class="mt-6 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
            Delete
        </button>

        <!-- Modal -->
        <div id="deleteModal" class="fixed inset-0 items-center justify-center bg-gray-900 bg-opacity-50 z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-2">Are you sure you want to delete your account?</h3>
                <p class="mb-4 text-gray-600 text-sm">
                    Once your account is deleted, all of its resources and data will be permanently deleted.<br>
                    Please enter your password to confirm you would like to permanently delete your account.
                </p>
                <form action="/users/{{ $user->id }}" method="post">
                    @method('DELETE')
                    @csrf
                    <input type="password" name="password" placeholder="Password" required class="border rounded px-3 py-2 w-full mb-4 focus:outline-none focus:ring-2 focus:ring-green-400">
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden');document.getElementById('deleteModal').classList.remove('flex')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
