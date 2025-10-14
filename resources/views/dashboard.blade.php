<x-app-layout>
    {{-- Header Section --}}
    @if (session('status') === 'logged-in')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <div class="flex flex-col sm:flex-row justify-center items-center text-center">
                <p>Welcome, {{ Auth::user()->name }}!</p>
                <p class="mt-4 sm:mt-0 sm:ml-2">{{ __("You're logged in!") }}</p>
            </div>
        </h2>
    </x-slot>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Success, Error, and Sync Results Messages --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('sync_results'))
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
                            <p class="font-bold">Sync Results:</p>
                            <ul>
                                @foreach(session('sync_results') as $result)
                                    <li>{{ $result }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Ticket Table and Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold mb-4 sm:mb-0">Ticket Lists</h3>
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                            <a href="{{ route('tickets.create') }}" class="px-4 py-2 text-center text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition duration-150 ease-in-out">
                                Create New Ticket
                            </a>
                            <form action="{{ route('tickets.sync-all') }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600 transition duration-150 ease-in-out w-full">
                                    Sync All Unsynced Tickets
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Ticket Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ServiceNow ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Third Party Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $ticket->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ Str::limit($ticket->description, 50) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="{{ $ticket->syncing_status === 'synced' ? 'bg-green-500' : 'bg-red-500' }} text-white text-xs font-semibold px-2 py-1 rounded-full capitalize">
                                                {{ str_replace('_', ' ', $ticket->syncing_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if ($ticket->syncing_status === 'not_synced')
                                                <form action="{{ route('tickets.sync-single', $ticket) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600 transition duration-150 ease-in-out shadow-md">
                                                        <i class="fas fa-sync-alt"></i>
                                                        Sync
                                                    </button>
                                                </form>
                                            @else
                                                <button disabled class="px-4 py-2 text-white bg-gray-400 rounded-lg cursor-not-allowed opacity-50 shadow-md">
                                                    Synced
                                                </button>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $ticket->service_now_id ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="{{ $ticket->status === 'New' ? 'bg-blue-500' : ($ticket->status === 'Resolved' ? 'bg-green-500' : 'bg-red-500') }} text-white text-xs font-semibold px-2 py-1 rounded-full">
                                                {{ $ticket->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            No tickets found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>