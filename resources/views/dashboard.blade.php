<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rice Ordering and Management System') }}
        </h2>
    </x-slot>
<!--the infos are added because it looks to plain if its only the buttons-->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!--menu button-->
                    <a href="{{ route('menus.index') }}" style="text-decoration: none; color: inherit;">
                        <h3 class="text-lg font-bold" style="cursor: pointer;">Menu Management</h3>
                    </a>
                    <p>In here you can:</p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li>Display all available rice menu items (Jasmine, Dinorado, Sinandomeng, Brown Rice).</li>
                        <li>Add new rice products.</li>
                        <li>View and manage existing rice products.</li>
                        <li>Update rice details (name, category, price per kilo, stock).</li>
                        <li>Delete rice products when no longer available.</li>
                        <li>Perform full CRUD operations for menu products.</li>
                    </ul>
                    <hr class="my-4">
                    <!--order button-->
                    <a href="{{ route('orders.index') }}" style="text-decoration: none; color: inherit;">
                        <h3 class="text-lg font-bold" style="cursor: pointer;">Order Management</h3>
                    </a>
                    <p>In here you can:</p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li>Use a POS interface for creating orders.</li>
                        <li>Create a new order linked to customer, rice item, and quantity.</li>
                        <li>Automatically calculate total cost: <strong>Total = Quantity × Price per Kilo</strong>.</li>
                        <li>Display selected items and total.</li>
                        <li>Monitor order status (Pending, Processing, Completed).</li>
                        <li>Display order summary list.</li>
                    </ul>
                    <hr class="my-4">
                    <!--payment button-->
                    <a href="{{ route('payments.index') }}" style="text-decoration: none; color: inherit;">
                        <h3 class="text-lg font-bold" style="cursor: pointer;">Payment Management</h3>
                    </a>
                    <p>In here you can:</p>
                    <ul style="list-style: disc; margin-left: 20px;">
                        <li>Process payments for customer orders.</li>
                        <li>Update payment status (Partial, Paid, Unpaid).</li>
                        <li>Calculate and display the change based on the amount given.</li>
                        <li>Automatically update remaining balance when partial payments are made.</li>
                        <li>View a history of all transactions and payment logs for a specific customer or order.</li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>