<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Mail\PackageExpiryNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendPackageNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'emails:package-expiry {--force : Send to all companies regardless of last notification}';

    /**
     * The console command description.
     */
    protected $description = 'Send package expiry email notifications to companies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📧 Checking package expiry for email notifications...');
        
        $force = $this->option('force');
        
        // Get companies with package_end date
        $companies = Company::whereNotNull('package_end')
            ->whereNotNull('package')
            ->whereNotNull('email') // Only companies with email
            ->get();

        if ($companies->isEmpty()) {
            $this->warn('No companies with email found.');
            return 0;
        }

        $this->info("Found {$companies->count()} companies to check.");
        
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($companies as $company) {
            $daysLeft = $this->calculateDaysLeft($company);
            
            // Determine if we should send notification
            $shouldSend = $force || $this->shouldSendNotification($company, $daysLeft);
            
            if ($shouldSend) {
                $this->sendNotification($company, $daysLeft);
                $sentCount++;
                
                // Update last notification sent time
                $company->last_package_notification_sent = now();
                $company->save();
                
                $this->info("✓ Email sent to: {$company->email} (Days left: {$daysLeft})");
            } else {
                $skippedCount++;
            }
        }

        $this->newLine();
        $this->info("✅ Process completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['📧 Emails Sent', $sentCount],
                ['⏭️ Skipped', $skippedCount],
                ['📊 Total Checked', $companies->count()],
            ]
        );

        return 0;
    }

    /**
     * Calculate days left until package expiry
     */
    private function calculateDaysLeft($company)
    {
        if (!$company->package_end) {
            return null;
        }

        $now = Carbon::now();
        $endDate = Carbon::parse($company->package_end);
        
        if ($endDate->isPast()) {
            return 0;
        }
        
        return (int) ceil($now->diffInDays($endDate, false));
    }

    /**
     * Determine if we should send notification based on days left
     */
    private function shouldSendNotification($company, $daysLeft)
    {
        // Don't send if no days left calculated
        if ($daysLeft === null) {
            return false;
        }

        // Check if we've sent a notification recently (within last 24 hours)
        if ($company->last_package_notification_sent) {
            $lastSent = Carbon::parse($company->last_package_notification_sent);
            if ($lastSent->diffInHours(now()) < 24) {
                return false;
            }
        }

        // Send notifications at specific thresholds
        $thresholds = [10, 5, 3, 1, 0];
        
        // Also send if days left is between 1-5 (daily)
        if ($daysLeft <= 5 && $daysLeft > 0) {
            return true;
        }
        
        return in_array($daysLeft, $thresholds);
    }

    /**
     * Send email notification
     */
    private function sendNotification($company, $daysLeft)
    {
        try {
            Mail::to($company->email)->send(
                new PackageExpiryNotification($company, $daysLeft, $company->package)
            );
            
            Log::info('Package expiry email sent', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'email' => $company->email,
                'days_left' => $daysLeft,
                'package' => $company->package
            ]);
            
        } catch (\Exception $e) {
            $this->error("Failed to send email to {$company->email}: " . $e->getMessage());
            
            Log::error('Package expiry email failed', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'email' => $company->email,
                'error' => $e->getMessage()
            ]);
        }
    }
}