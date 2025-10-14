<x-app-layout>
    <div class="py-1 flex justify-center items-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg max-w-lg w-full">
            <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-gray-200 mb-6">Create New Ticket</h1>

            <form method="POST" action="{{ route('tickets.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Title</label>
                    <input id="title" type="text" name="title" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Description</label>
                    <textarea id="description" name="description" required rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"></textarea>
                </div>

                <div class="mb-6">
                    <p class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Do you want to sync this ticket with ServiceNow now?</p>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="sync_option" value="synced"
                                class="form-radio text-blue-600 dark:text-blue-400">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Yes, Sync Now</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sync_option" value="not_synced" checked
                                class="form-radio text-blue-600 dark:text-blue-400">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">No, Sync Later</span>
                        </label>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit"
                            class="w-full px-6 py-3 text-lg font-bold text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>