<x-layout title="Sales Invoice">
    <div class="pagetitle">
        <h1>Sales Invoice</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Sales Invoice</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Sales Invoice Management</h5>
                        <p>This functionality has been moved to a new location.</p>
                        <a href="{{ route('sales_invoices.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right"></i> Go to Sales Invoices
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
