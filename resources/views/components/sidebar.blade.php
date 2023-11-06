<!-- sidebar -->
<div class="fixed inset-y-0 top-0 left-0 z-50 w-64 h-screen max-h-screen text-blue-100 transition duration-200 ease-in-out transform bg-gray-800 sidebar"
x-data="{ open: $persist(false) }" x-on:togglesidebar.window=" open = !open" x-show="true"
:class="open === true ? 'md:translate-x-0 md:sticky ':'-translate-x-full' ">

<header class="h-[64px] py-2 shadow-lg px-4 md:sticky top-0 bg-gray-800 z-40">
    <!-- logo -->
    <a href="#" class="flex items-center space-x-2 text-white group hover:text-white">
        <div>
            <a href="{{ route('dashboard') }}">
                <img src="/storage/clientes.svg" alt="" class="h-auto w-44">
            </a>
        </div>
    </a>
</header>


<!-- nav -->
<nav class="px-4 pt-4 overflow-y-scroll max-h-[calc(100vh-64px)]"">
    <ul class="flex flex-col space-y-2">

        <x-dashboard.sidebarmenu />

    </ul>
</nav>


</div>
<!-- End sidebar -->
