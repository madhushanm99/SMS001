<x-layout title="Dashboard">
    <div class="pagetitle">
      <h1>Purshase Order</h1>




      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Purshase Order</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
        <div class="container">
            <div class="d-flex mb-3">

                <div class="col-md-6">
                    <form method="GET" action="{{ route('purchaseOrder') }}" class="flex-grow-1 me-2">
                        <input type="text" id="search" name="search" class="form-control"
                            placeholder="Search PO Number, Supplier " autocomplete="off" />
                    </form>
                </div>
                <a href="{{ route('purchaseOrder.create') }}" class="btn btn-primary fontSize14">New Purchase Oder</a>
            </div>

            <div id="supplier-table">
                {{-- @include('Purchase.partials.suppliers_table', ['suppliers' => $suppliers]) --}}
            </div>

        </div>
    </section>
  </x-layout>
