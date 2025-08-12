<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vehicle Service Info</title>
    <style>
        :root {
            --fg: #1f2937; --muted: #6b7280; --primary: #0d6efd; --border: #e5e7eb; --warn-bg: #fff7ed; --warn-fg: #9a3412;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; color: var(--fg); }
        .wrap { max-width: 960px; margin: 0 auto; padding: 16px; }
        .card { border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .card-body { padding: 16px; }
        .header { display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 12px; }
        .title { margin: 0 0 4px 0; font-size: 20px; }
        .subtle { color: var(--muted); font-size: 14px; }
        .btn { display: inline-block; padding: 10px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-outline { border: 1px solid var(--border); color: var(--fg); background: #fff; }
        .grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
        @media (min-width: 768px) { .grid { grid-template-columns: 1fr 1fr; } }
        .box { border: 1px solid var(--border); border-radius: 10px; padding: 16px; }
        .box-title { font-weight: 700; margin-bottom: 8px; }
        .muted { color: var(--muted); }
        .badge-warn { background: var(--warn-bg); color: var(--warn-fg); padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .strong { font-weight: 700; }
        .mt-8 { margin-top: 16px; }
    </style>
    <meta name="robots" content="noindex, nofollow">
    <meta name="format-detection" content="telephone=no">
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="card-body">
            <div class="header">
                <div>
                    <h1 class="title">Vehicle: {{ $vehicle?->vehicle_no ?? 'Unknown' }}</h1>
                    @if($vehicle)
                        <div class="subtle">
                            {{ $vehicle->brand->name ?? '' }} {{ $vehicle->model }}
                            @if($vehicle->year_of_manufacture)
                                ({{ $vehicle->year_of_manufacture }})
                            @endif
                        </div>
                    @endif
                </div>
                <div style="display:flex; gap:8px; flex-wrap: wrap; justify-content:flex-end;">
                    <a class="btn btn-primary" href="{{ route('customer.appointments.create') }}">Book Appointment</a>
                    @if($vehicle)
                        <a class="btn btn-outline" href="{{ route('customer.services.index') }}">Show Service History</a>
                    @endif
                    @if($vehicle)
                        <a class="btn btn-outline" href="{{ route('customer.vehicles.index') }}">My Vehicles</a>
                    @endif
                </div>
            </div>

            <div class="grid">
                <div class="box">
                    <div class="box-title">Previous Service</div>
                    @if($schedule && $schedule->last_service_date)
                        <div class="muted">Date:</div>
                        <div class="strong" style="margin-bottom:6px;">{{ $schedule->last_service_date->format('d M Y') }}</div>
                        <div class="muted">Mileage:</div>
                        <div class="strong">{{ number_format($schedule->last_mileage) }} km</div>
                    @else
                        <div class="muted">no data to show</div>
                    @endif
                </div>

                <div class="box">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                        <div class="box-title">Next Service (approximate)</div>
                        <span class="badge-warn">Approximate</span>
                    </div>
                    @if($schedule && $schedule->next_service_date)
                        <div class="muted">Date:</div>
                        <div class="strong" style="margin-bottom:6px;">{{ $schedule->next_service_date->format('d M Y') }}</div>
                        <div class="muted">Mileage:</div>
                        <div class="strong">{{ number_format($schedule->next_service_mileage) }} km</div>
                        <div class="muted mt-8">This is an approximate date and may change depending on riding patterns.</div>
                    @else
                        <div class="muted">no data to show</div>
                    @endif
                </div>
            </div>

            <div class="mt-8 subtle">Tip: Booking your appointment early helps ensure timely service.</div>
        </div>
    </div>
</div>
</body>
</html>
