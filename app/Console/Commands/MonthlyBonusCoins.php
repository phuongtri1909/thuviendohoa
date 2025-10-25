<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Package;
use App\Models\CoinTransaction;
use App\Models\MonthlyBonus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyBonusCoins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coins:monthly-bonus 
                            {--package= : Chỉ cộng cho user của package cụ thể (package_id)}
                            {--batch-size=100 : Số lượng user xử lý mỗi batch}
                            {--delay=1 : Delay giữa các batch (giây)}
                            {--test : Chỉ hiển thị thông tin, không thực hiện cộng xu}
                            {--setup-cron : Hiển thị hướng dẫn setup cron job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cộng bonus coins hàng tháng cho user có package hợp lệ - Tối ưu nhất';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Setup cron job
        if ($this->option('setup-cron')) {
            return $this->setupCronJob();
        }
        
        $this->info('Cộng bonus coins hàng tháng cho user có package hợp lệ');
        
        $packageId = $this->option('package');
        $batchSize = (int) $this->option('batch-size');
        $delay = (int) $this->option('delay');
        $isTest = $this->option('test');
        
        try {
            if ($packageId) {
                // Chỉ cộng cho user của package cụ thể
                $this->processPackage($packageId, $batchSize, $delay, $isTest);
            } else {
                // Cộng cho tất cả packages
                $this->processAllPackages($batchSize, $delay, $isTest);
            }
            
            if (!$isTest) {
                $this->info('Hoàn thành cộng bonus coins hàng tháng!');
            }
            
        } catch (\Exception $e) {
            $this->error('Lỗi: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Xử lý tất cả packages
     */
    private function processAllPackages($batchSize, $delay, $isTest)
    {
        $packages = Package::where('bonus_coins', '>', 0)->get();
        
        if ($packages->isEmpty()) {
            $this->warn('Không có package nào có bonus_coins > 0');
            return;
        }
        
        $this->info("Tìm thấy {$packages->count()} packages có bonus coins");
        
        $totalUsers = 0;
        $totalCoins = 0;
        
        foreach ($packages as $package) {
            $userCount = User::where('package_id', $package->id)
                ->where('package_expired_at', '>', now())
                ->count();
                
            $totalUsers += $userCount;
            $totalCoins += $userCount * $package->bonus_coins;
            
            if ($isTest) {
                $this->info("{$package->name}: {$userCount} user, {$package->bonus_coins} xu/user");
            } else {
                $this->info("Đang xử lý package: {$package->name} (ID: {$package->id})");
                $this->processPackage($package->id, $batchSize, $delay, $isTest);
                
                if ($delay > 0) {
                    $this->info("Chờ {$delay} giây...");
                    sleep($delay);
                }
            }
        }
        
        if ($isTest) {
            $this->info("Tổng: {$totalUsers} user sẽ nhận {$totalCoins} xu");
        }
    }
    
    /**
     * Xử lý một package cụ thể
     */
    private function processPackage($packageId, $batchSize, $delay, $isTest)
    {
        $package = Package::find($packageId);
        
        if (!$package) {
            $this->error("Không tìm thấy package với ID: {$packageId}");
            return;
        }
        
        if ($package->bonus_coins <= 0) {
            $this->warn("Package {$package->name} không có bonus coins");
            return;
        }
        
        // Kiểm tra xem đã có monthly bonus cho package này trong tháng này chưa
        $currentMonth = now()->format('Y-m');
        $existingBonus = MonthlyBonus::where('package_id', $packageId)
            ->where('month', $currentMonth)
            ->first();
            
        if ($existingBonus) {
            $this->warn("Package {$package->name} đã được cộng bonus trong tháng {$currentMonth} (ID: {$existingBonus->id})");
            $this->info("Số user đã cộng: {$existingBonus->total_users}, Tổng xu: {$existingBonus->total_coins}");
            return;
        }
        
        // Đếm tổng số user cần xử lý
        $totalUsers = User::where('package_id', $packageId)
            ->where('package_expired_at', '>', now())
            ->count();
            
        if ($totalUsers === 0) {
            $this->warn("Không có user nào có package {$package->name} hợp lệ");
            return;
        }
        
        if ($isTest) {
            $this->info("Package: {$package->name}");
            $this->info("Bonus coins: {$package->bonus_coins}");
            $this->info("User hợp lệ: {$totalUsers}");
            $this->info("Tổng xu sẽ cộng: " . number_format($totalUsers * $package->bonus_coins));
            return;
        }
        
        $this->info("Tìm thấy {$totalUsers} user có package {$package->name} hợp lệ");
        $this->info("Sẽ cộng {$package->bonus_coins} xu cho mỗi user");
        $this->info("Xử lý theo batch: {$batchSize} user/batch");
        
        $processed = 0;
        $offset = 0;
        $batchNumber = 1;
        $allUserIds = [];
        $currentMonth = now()->format('Y-m');
        
        // Xử lý theo batch để tránh overload
        do {
            $users = User::where('package_id', $packageId)
                ->where('package_expired_at', '>', now())
                ->offset($offset)
                ->limit($batchSize)
                ->get();
                
            $usersToProcess = collect();
            foreach ($users as $user) {
                $alreadyReceived = MonthlyBonus::where('package_id', $packageId)
                    ->where('month', $currentMonth)
                    ->whereJsonContains('user_ids', (string)$user->id)
                    ->exists();
                    
                if (!$alreadyReceived) {
                    $usersToProcess->push($user);
                } else {
                    $this->info("User {$user->full_name} (ID: {$user->id}) đã nhận bonus trong tháng này, bỏ qua");
                }
            }
            
            if ($users->isEmpty()) {
                break;
            }
            
            if ($usersToProcess->isEmpty()) {
                $offset += $batchSize;
                continue;
            }
            
            $this->info("Batch #{$batchNumber}: Xử lý {$usersToProcess->count()} user...");
            $this->processBatch($usersToProcess, $package);
            
            $allUserIds = array_merge($allUserIds, $usersToProcess->pluck('id')->map(function($id) {
                return (string)$id;
            })->toArray());
            
            $processed += $usersToProcess->count();
            $offset += $batchSize;
            $batchNumber++;
            
            $this->info("Đã xử lý {$processed}/{$totalUsers} user");
            
            // Delay giữa các batch
            if ($delay > 0 && $processed < $totalUsers) {
                $this->info("⏳ Chờ {$delay} giây trước batch tiếp theo...");
                sleep($delay);
            }
            
        } while ($processed < $totalUsers);
        
        // Lưu MonthlyBonus record
        if ($processed > 0) {
            MonthlyBonus::create([
                'package_id' => $package->id,
                'month' => $currentMonth,
                'total_users' => $processed,
                'total_coins' => $processed * $package->bonus_coins,
                'bonus_per_user' => $package->bonus_coins,
                'user_ids' => $allUserIds,
                'notes' => "Tự động cộng bonus coins hàng tháng cho {$processed} user",
                'processed_at' => now(),
            ]);
            
            $this->info("Đã lưu record MonthlyBonus cho package {$package->name}");
        }
        
        $this->info("Hoàn thành xử lý package {$package->name}");
    }
    
    /**
     * Xử lý một batch user
     */
    private function processBatch($users, $package)
    {
        DB::beginTransaction();
        
        try {
            $coinHistories = [];
            $now = now();
            
            foreach ($users as $user) {
                $user->increment('coins', $package->bonus_coins);
                
                // Tạo CoinHistory record
                $coinHistories[] = [
                    'user_id' => $user->id,
                    'amount' => $package->bonus_coins,
                    'type' => \App\Models\CoinHistory::TYPE_MONTHLY_BONUS,
                    'source' => 'monthly_bonus_' . $package->id . '_' . $now->format('Y-m'),
                    'reason' => 'Thưởng xu hàng tháng',
                    'description' => "Nhận {$package->bonus_coins} xu từ gói {$package->name}",
                    'metadata' => json_encode([
                        'package_id' => $package->id,
                        'package_name' => $package->name,
                        'month' => $now->format('Y-m'),
                        'bonus_coins' => $package->bonus_coins
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
            
            // Bulk insert coin histories
            if (!empty($coinHistories)) {
                \App\Models\CoinHistory::insert($coinHistories);
            }
            
            DB::commit();
            
            $this->info("Batch hoàn thành: Đã cộng {$package->bonus_coins} xu cho {$users->count()} user");
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Setup cron job
     */
    private function setupCronJob()
    {
        $this->info('Setup Monthly Bonus Cron Job...');
        
        $projectPath = base_path();
        $command = "cd {$projectPath} && php artisan coins:monthly-bonus --batch-size=50 --delay=2";
        
        $cronEntry = "0 2 1 * * {$command} >> /var/log/monthly-bonus.log 2>&1";
        
        $this->info('Cron entry cần thêm vào crontab:');
        $this->line('');
        $this->line($cronEntry);
        $this->line('');
        
        $this->info('Hướng dẫn setup:');
        $this->line('1. Mở crontab: crontab -e');
        $this->line('2. Thêm dòng trên vào cuối file');
        $this->line('3. Lưu và thoát');
        $this->line('');
        
        $this->info('Test command trước khi setup:');
        $this->line('php artisan coins:monthly-bonus --test');
        $this->line('');
        
        $this->info('Monitor logs:');
        $this->line('tail -f /var/log/monthly-bonus.log');
        $this->line('tail -f storage/logs/laravel.log');
        $this->line('');
        
        $this->info('Setup hoàn tất!');
        
        return 0;
    }
}