<x-layout title="Dashboard">
    <div class="pagetitle">
        <h1>Dashboard</h1>




        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h4>New User</h4> <a href="{{ route('register') }}" class="btn btn-primary">+ New User</a>
        </div>
    </section>
</x-layout>
