<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PackageExpiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $daysLeft;
    public $packageName;
    public $paymentUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Company $company, $daysLeft, $packageName)
    {
        $this->company = $company;
        $this->daysLeft = $daysLeft;
        $this->packageName = $packageName;
        $this->paymentUrl = route('payment.package.selection');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->getSubject();
        
        return $this->subject($subject)
                    ->markdown('emails.package-expiry')
                    ->with([
                        'companyName' => $this->company->company_name,
                        'ownerName' => $this->company->owner_name,
                        'ownerEmail' => $this->company->email,
                        'ownerPhone' => $this->company->phone,
                        'daysLeft' => $this->daysLeft,
                        'packageName' => $this->packageName,
                        'paymentUrl' => $this->paymentUrl,
                        'packageEndDate' => $this->company->package_end 
                            ? \Carbon\Carbon::parse($this->company->package_end)->format('d/m/Y') 
                            : 'N/A'
                    ]);
    }

    /**
     * Get email subject based on days left
     */
    private function getSubject()
    {
        if ($this->daysLeft <= 0) {
            return '⚠️ MUHIMU: Package Yako Imekwisha - Chagua Kifurushi Mpya';
        } elseif ($this->daysLeft <= 5) {
            return "⚠️ MUHIMU: Package Yako Itaisha Baada ya Siku {$this->daysLeft}";
        } elseif ($this->daysLeft <= 10) {
            return "📢 Kumbusho: Package Yako Itaisha Baada ya Siku {$this->daysLeft}";
        }
        
        return "Taarifa ya Package Yako";
    }
}