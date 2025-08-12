<x-layout title="Overview">
  <div class="pagetitle">
    <h1>Overview</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Statistics</a></li>
        <li class="breadcrumb-item active">Overview</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span>Payments ({{ $days }} days)</span>
          </div>
          <div class="card-body">
            <canvas id="chartPayments" height="150"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">Sales Invoices ({{ $days }} days)</div>
          <div class="card-body">
            <canvas id="chartInvoices" height="150"></canvas>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">GRN ({{ $days }} days)</div>
          <div class="card-body">
            <canvas id="chartGrn" height="150"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">Purchase Orders ({{ $days }} days)</div>
          <div class="card-body">
            <canvas id="chartPo" height="150"></canvas>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">Purchase Returns ({{ $days }} days)</div>
          <div class="card-body">
            <canvas id="chartReturns" height="150"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="card">
          <div class="card-header">Registrations ({{ $days }} days)</div>
          <div class="card-body">
            <canvas id="chartRegistrations" height="150"></canvas>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script>
    (function(){
      const currency = 'LKR';
      const payments = @json($paymentsChart);
      const invoices = @json($invoicesChart);
      const grn = @json($grnChart);
      const po = @json($poChart);
      const returnsC = @json($returnsChart);
      const customers = @json($customersChart);
      const vehicles = @json($vehiclesChart);

      const primary = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary') || '#0d6efd';
      const success = getComputedStyle(document.documentElement).getPropertyValue('--bs-success') || '#198754';
      const danger = getComputedStyle(document.documentElement).getPropertyValue('--bs-danger') || '#dc3545';
      const info = getComputedStyle(document.documentElement).getPropertyValue('--bs-info') || '#0dcaf0';
      const warning = getComputedStyle(document.documentElement).getPropertyValue('--bs-warning') || '#ffc107';

      const ctxPayments = document.getElementById('chartPayments').getContext('2d');
      new Chart(ctxPayments, {
        type: 'line',
        data: {
          labels: payments.labels,
          datasets: [
            {label: 'Cash In', data: payments.cash_in, borderColor: success, backgroundColor: success, tension: .3},
            {label: 'Cash Out', data: payments.cash_out, borderColor: danger, backgroundColor: danger, tension: .3}
          ]
        },
        options: {responsive: true, maintainAspectRatio: false}
      });

      const ctxInvoices = document.getElementById('chartInvoices').getContext('2d');
      new Chart(ctxInvoices, {
        type: 'bar',
        data: {
          labels: invoices.labels,
          datasets: [
            {label: 'Total', data: invoices.total, backgroundColor: primary},
            {label: 'Count', data: invoices.count, backgroundColor: info}
          ]
        },
        options: {responsive: true, maintainAspectRatio: false}
      });

      const ctxGrn = document.getElementById('chartGrn').getContext('2d');
      new Chart(ctxGrn, {
        type: 'bar',
        data: { labels: grn.labels, datasets: [{label: 'Total', data: grn.total, backgroundColor: warning}] },
        options: {responsive: true, maintainAspectRatio: false}
      });

      const ctxPo = document.getElementById('chartPo').getContext('2d');
      new Chart(ctxPo, {
        type: 'bar',
        data: {
          labels: po.labels,
          datasets: [
            {label: 'Total', data: po.total, backgroundColor: primary},
            {label: 'Count', data: po.count, backgroundColor: info}
          ]
        },
        options: {responsive: true, maintainAspectRatio: false}
      });

      const ctxReturns = document.getElementById('chartReturns').getContext('2d');
      new Chart(ctxReturns, {
        type: 'bar',
        data: { labels: returnsC.labels, datasets: [{label: 'Total', data: returnsC.total, backgroundColor: danger}] },
        options: {responsive: true, maintainAspectRatio: false}
      });

      const ctxRegs = document.getElementById('chartRegistrations').getContext('2d');
      new Chart(ctxRegs, {
        type: 'line',
        data: {
          labels: customers.labels,
          datasets: [
            {label: 'Customers', data: customers.count, borderColor: primary, backgroundColor: primary, tension: .3},
            {label: 'Vehicles', data: vehicles.count, borderColor: info, backgroundColor: info, tension: .3}
          ]
        },
        options: {responsive: true, maintainAspectRatio: false}
      });
    })();
  </script>
</x-layout>


