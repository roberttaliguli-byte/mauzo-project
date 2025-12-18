@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Payment Status</h4>
                </div>
                
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="payment-details">
                        <h5 class="mb-4">Payment Details</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Transaction ID:</strong>
                                <p class="text-muted">{{ $transaction->id }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Package:</strong>
                                <p class="text-muted">{{ $transaction->package }}</p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Amount:</strong>
                                <p class="text-muted">TZS {{ number_format($transaction->amount) }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <p>
                                    @if($transaction->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($transaction->status === 'processing')
                                        <span class="badge bg-info">Processing</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($transaction->status) }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <strong>Created:</strong>
                                <p class="text-muted">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Last Updated:</strong>
                                <p class="text-muted">{{ $transaction->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div id="statusMessage" class="alert alert-info d-none">
                            <!-- Dynamic status messages will appear here -->
                        </div>
                        
                        <div class="action-buttons mt-4">
                            @if($transaction->status === 'completed')
                                <a href="{{ route('dashboard') }}" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Go to Dashboard
                                </a>
                            @elseif(in_array($transaction->status, ['pending', 'processing']))
                                <button id="checkStatusBtn" class="btn btn-primary">
                                    <i class="fas fa-sync-alt"></i> Check Status
                                </button>
                                <a href="{{ route('package.retry', $transaction->id) }}" class="btn btn-warning">
                                    <i class="fas fa-redo"></i> Retry Payment
                                </a>
                                <a href="{{ route('package.cancel', $transaction->id) }}" class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to cancel this payment?')">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            @elseif(in_array($transaction->status, ['failed', 'expired']))
                                <a href="{{ route('package.retry', $transaction->id) }}" class="btn btn-primary">
                                    <i class="fas fa-redo"></i> Try Again
                                </a>
                                <a href="{{ route('package.choose') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Choose Different Package
                                </a>
                            @else
                                <a href="{{ route('package.choose') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left"></i> Back to Packages
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-footer text-muted">
                    <small>Need help? Contact our support team.</small>
                </div>
            </div>
        </div>
    </div>
</div>

@if(in_array($transaction->status, ['pending', 'processing']))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkStatusBtn = document.getElementById('checkStatusBtn');
    const statusMessage = document.getElementById('statusMessage');
    
    function checkPaymentStatus() {
        fetch('{{ route("package.check-status", $transaction->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'completed') {
                    statusMessage.className = 'alert alert-success';
                    statusMessage.innerHTML = `<i class="fas fa-check-circle"></i> ${data.message}`;
                    statusMessage.classList.remove('d-none');
                    
                    // Redirect to dashboard after 3 seconds
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 3000);
                    
                } else if (data.status === 'error') {
                    statusMessage.className = 'alert alert-danger';
                    statusMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${data.message}`;
                    statusMessage.classList.remove('d-none');
                    
                } else {
                    statusMessage.className = 'alert alert-info';
                    statusMessage.innerHTML = `<i class="fas fa-info-circle"></i> ${data.message}`;
                    statusMessage.classList.remove('d-none');
                    
                    // If refresh is true, reload page after 5 seconds
                    if (data.refresh) {
                        setTimeout(() => {
                            location.reload();
                        }, 5000);
                    }
                }
            })
            .catch(error => {
                statusMessage.className = 'alert alert-danger';
                statusMessage.innerHTML = `<i class="fas fa-exclamation-circle"></i> Error checking status. Please try again.`;
                statusMessage.classList.remove('d-none');
            });
    }
    
    // Check status on button click
    checkStatusBtn.addEventListener('click', checkPaymentStatus);
    
    // Auto-check every 10 seconds for pending payments
    if ('{{ $transaction->status }}' === 'pending') {
        setInterval(checkPaymentStatus, 10000);
    }
});
</script>
@endif
@endsection