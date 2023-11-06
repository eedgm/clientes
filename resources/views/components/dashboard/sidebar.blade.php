<aside class="sticky top-0 z-50 flex flex-col h-screen text-gray-300 transition-all duration-300 ease-in-out bg-gray-800" :class="isSidebarExpanded ? 'w-64' : 'w-16 md:w-20'">
    <a href="#" class="flex items-center h-20 px-4 overflow-hidden bg-gray-900 hover:text-gray-100 hover:bg-opacity-50 focus:outline-none focus:text-gray-100 focus:bg-opacity-50">
        <img src="/storage/clientes.svg" alt="" class="h-auto w-44">
        {{-- <span class="ml-2 text-xl font-medium duration-300 ease-in-out" :class="isSidebarExpanded ? 'opacity-100' : 'opacity-0'">Dashboard</span> --}}
    </a>
    <nav class="p-4 space-y-2 font-medium">
        <x-dashboard.sidebarmenu />
    </nav>
</aside>
