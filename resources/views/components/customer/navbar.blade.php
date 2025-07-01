<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('customer.dashboard') }}">Customer Portal</a>
        <button class="navbar-toggler d-md-none collapsed" type="button" data-bs-toggle="collapse" 
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="d-flex align-items-center">
            <span class="text-light me-3">{{ auth()->guard('customer')->user()->customer->name }}</span>
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav> 