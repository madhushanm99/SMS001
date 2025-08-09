<?php

namespace App\Console\Commands;

use App\Events\AppointmentStatusChanged;
use App\Models\Appointment;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestWebSocketNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:websocket-notification';

    /**
     * The console command description.
     */
    protected $description = 'Test WebSocket notifications by broadcasting a sample event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing WebSocket Notification System...');
        $this->newLine();

        // Check if we have any appointments to use for testing
        $appointment = Appointment::with(['customer', 'vehicle.brand'])->first();

        if (!$appointment) {
            $this->error('❌ No appointments found in database.');
            $this->info('💡 Create an appointment first to test notifications.');
            return 1;
        }

        $this->info("📋 Using appointment: {$appointment->appointment_no}");
        $this->info('👤 Customer: ' . ($appointment->customer->name ?? 'Unknown'));
        $this->info("🚗 Vehicle: {$appointment->vehicle_no}");
        $this->newLine();

        // Test 1: Broadcast AppointmentStatusChanged event
        $this->info('🔄 Broadcasting AppointmentStatusChanged event...');

        try {
            event(new AppointmentStatusChanged(
                $appointment,
                'pending',
                'confirmed',
                'Test Staff Member'
            ));

            $this->info('✅ Event broadcasted successfully!');
        } catch (\Exception $e) {
            $this->error("❌ Error broadcasting event: {$e->getMessage()}");
            return 1;
        }

        $this->newLine();

        // Test 2: Send notification using NotificationService
        $this->info('📢 Sending notification via NotificationService...');

        try {
            NotificationService::appointmentConfirmed($appointment, 'Test Staff Member');
            $this->info('✅ Notification sent successfully!');
        } catch (\Exception $e) {
            $this->error("❌ Error sending notification: {$e->getMessage()}");
            return 1;
        }

        $this->newLine();
        $this->info('🎉 WebSocket notification test completed!');
        $this->newLine();

        $this->comment('📝 What to check:');
        $this->comment('1. Open your browser and go to the staff dashboard');
        $this->comment('2. Make sure the Reverb WebSocket server is running');
        $this->comment('3. Check the browser console for WebSocket messages');
        $this->comment('4. Look for notification count updates in the header');
        $this->newLine();

        $this->comment('🔧 To start WebSocket server:');
        $this->comment('php artisan reverb:start --host=127.0.0.1 --port=8080');
        $this->comment('OR double-click: start-websocket.bat');

        return 0;
    }
}
