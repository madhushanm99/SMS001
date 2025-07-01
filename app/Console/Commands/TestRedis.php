<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Exception;

class TestRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Redis connection and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Redis Connection...');
        $this->newLine();

        // Test basic Redis connection
        try {
            $this->info('1. Testing basic Redis connection...');
            Redis::ping();
            $this->info('✅ Redis connection successful!');
        } catch (Exception $e) {
            $this->error('❌ Redis connection failed: ' . $e->getMessage());
            $this->error('Make sure Redis server is running and check your .env configuration:');
            $this->error('REDIS_HOST=' . config('database.redis.default.host'));
            $this->error('REDIS_PORT=' . config('database.redis.default.port'));
            return 1;
        }

        $this->newLine();

        // Test Cache functionality
        try {
            $this->info('2. Testing Cache functionality...');
            $testKey = 'laravel_redis_test_' . time();
            $testValue = 'Redis cache is working!';
            
            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            
            if ($retrieved === $testValue) {
                $this->info('✅ Cache functionality working correctly!');
                Cache::forget($testKey); // Clean up
            } else {
                $this->error('❌ Cache test failed - values don\'t match');
            }
        } catch (Exception $e) {
            $this->error('❌ Cache test failed: ' . $e->getMessage());
        }

        $this->newLine();

        // Test Redis info
        try {
            $this->info('3. Redis Server Information:');
            $info = Redis::info();
            $this->info('Redis Version: ' . ($info['redis_version'] ?? 'Unknown'));
            $this->info('Connected Clients: ' . ($info['connected_clients'] ?? 'Unknown'));
            $this->info('Memory Used: ' . ($info['used_memory_human'] ?? 'Unknown'));
        } catch (Exception $e) {
            $this->warn('Could not retrieve Redis info: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('Redis test completed successfully! 🎉');
        
        return 0;
    }
}
