<x-layout>
    <x-slot name="title">Appointment Calendar</x-slot>

@push('styles')
<style>
.calendar {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.calendar-header {
    background: #4e73df;
    color: white;
    padding: 1rem;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.calendar-day-header {
    background: #f8f9fa;
    padding: 0.75rem;
    text-align: center;
    font-weight: bold;
    border-bottom: 1px solid #dee2e6;
}

.calendar-day {
    min-height: 120px;
    border: 1px solid #dee2e6;
    padding: 0.5rem;
    position: relative;
}

.calendar-day.other-month {
    background: #f8f9fa;
    color: #6c757d;
}

.calendar-day.today {
    background: #e7f3ff;
}

.day-number {
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.appointment-item {
    background: #4e73df;
    color: white;
    padding: 0.25rem;
    margin-bottom: 0.25rem;
    border-radius: 3px;
    font-size: 0.75rem;
    cursor: pointer;
}

.appointment-item.pending {
    background: #ffc107;
    color: #856404;
}

.appointment-item.confirmed {
    background: #28a745;
}

.appointment-item.rejected {
    background: #dc3545;
}

.appointment-item.completed {
    background: #6c757d;
}

.appointment-item.cancelled {
    background: #6f42c1;
}

.appointment-count {
    position: absolute;
    top: 0.25rem;
    right: 0.25rem;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
}
</style>
@endpush


<div class="pagetitle">
    <h1>Appointment Calendar</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Calendar</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="calendar">
                        <div class="calendar-header">
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-light btn-sm me-2" onclick="changeMonth(-1)">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <h4 class="mb-0" id="currentMonth">{{ $currentMonth }}</h4>
                                <button type="button" class="btn btn-light btn-sm ms-2" onclick="changeMonth(1)">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('appointments.index') }}" class="btn btn-light btn-sm">
                                    <i class="bi bi-list-ul"></i> List View
                                </a>
                            </div>
                        </div>

                        <div class="calendar-grid">
                            <!-- Day Headers -->
                            <div class="calendar-day-header">Sun</div>
                            <div class="calendar-day-header">Mon</div>
                            <div class="calendar-day-header">Tue</div>
                            <div class="calendar-day-header">Wed</div>
                            <div class="calendar-day-header">Thu</div>
                            <div class="calendar-day-header">Fri</div>
                            <div class="calendar-day-header">Sat</div>

                            <!-- Calendar Days -->
                            @foreach($calendarDays as $day)
                            <div class="calendar-day {{ $day['isOtherMonth'] ? 'other-month' : '' }} {{ $day['isToday'] ? 'today' : '' }}"
                                 data-date="{{ $day['date'] }}">
                                <div class="day-number">{{ $day['day'] }}</div>

                                @if(($day['appointments'] instanceof \Illuminate\Support\Collection ? $day['appointments']->count() : count($day['appointments'])) > 3)
                                    <div class="appointment-count">{{ $day['appointments'] instanceof \Illuminate\Support\Collection ? $day['appointments']->count() : count($day['appointments']) }}</div>
                                @endif

                                @foreach(($day['appointments'] instanceof \Illuminate\Support\Collection ? $day['appointments']->take(3) : collect($day['appointments'])->take(3)) as $appointment)
                                <div class="appointment-item {{ $appointment->status }}"
                                     onclick="showAppointment('{{ $appointment->id }}')"
                                     title="{{ $appointment->getFormattedTime() }} - {{ $appointment->customer->name ?? 'N/A' }}">
                                    {{ $appointment->getFormattedTime() }} {{ $appointment->customer->name ?? 'N/A' }}
                                </div>
                                @endforeach

                                @if(($day['appointments'] instanceof \Illuminate\Support\Collection ? $day['appointments']->count() : count($day['appointments'])) > 3)
                                <div class="appointment-item" style="background: #6c757d;">
                                    +{{ ($day['appointments'] instanceof \Illuminate\Support\Collection ? $day['appointments']->count() : count($day['appointments'])) - 3 }} more
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Status Legend</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="appointment-item pending me-2" style="display: inline-block; width: 20px; height: 20px;"></div>
                            <span>Pending</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="appointment-item confirmed me-2" style="display: inline-block; width: 20px; height: 20px;"></div>
                            <span>Confirmed</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="appointment-item rejected me-2" style="display: inline-block; width: 20px; height: 20px;"></div>
                            <span>Rejected</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="appointment-item completed me-2" style="display: inline-block; width: 20px; height: 20px;"></div>
                            <span>Completed</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="appointment-item cancelled me-2" style="display: inline-block; width: 20px; height: 20px;"></div>
                            <span>Cancelled</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
let currentDate = new Date('{{ $currentDate }}');

function changeMonth(direction) {
    currentDate.setMonth(currentDate.getMonth() + direction);
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');

    window.location.href = `{{ route('appointments.calendar') }}?year=${year}&month=${month}`;
}

function showAppointment(appointmentId) {
    window.location.href = `/appointments/${appointmentId}`;
}

// Add click handler for empty days to potentially add appointments
document.querySelectorAll('.calendar-day').forEach(day => {
    day.addEventListener('dblclick', function() {
        const date = this.getAttribute('data-date');
        if (date && !this.classList.contains('other-month')) {
            // Could redirect to appointment creation with pre-filled date
            console.log('Double-clicked on date:', date);
        }
    });
});
</script>
@endpush

</x-layout>
