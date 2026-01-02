<!-- Thermal Receipt Modal -->
<div id="receipt-modal" class="modal fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-xs mx-4 z-50">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 text-center">
                <i class="fas fa-receipt mr-2 text-green-600"></i>
                RISITI YA MAUZO
            </h3>
        </div>
        
        <div class="p-4" id="receipt-content">
            <!-- Receipt content will be generated here -->
        </div>
        
        <div class="flex flex-col gap-2 p-4 border-t border-gray-200">
            <button 
                id="print-receipt-btn" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center justify-center gap-2"
            >
                <i class="fas fa-print"></i>
                Print Risiti
            </button>
            <button 
                id="close-receipt-btn" 
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium text-sm"
            >
                Funga
            </button>
        </div>
    </div>
</div>

<!-- Thermal printer CSS -->
<style>
@media print {
    /* Hide everything except receipt */
    body * {
        visibility: hidden;
    }
    
    #receipt-content, #receipt-content * {
        visibility: visible;
    }
    
    #receipt-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 58mm; /* Thermal printer width */
        font-family: 'Courier New', monospace;
    }
    
    /* Thermal printer styling */
    .receipt-header {
        text-align: center;
        margin-bottom: 8px;
    }
    
    .receipt-title {
        font-size: 16px;
        font-weight: bold;
    }
    
    .receipt-company {
        font-size: 12px;
    }
    
    .receipt-details {
        font-size: 11px;
        margin-bottom: 6px;
    }
    
    .receipt-items {
        border-top: 1px dashed #000;
        border-bottom: 1px dashed #000;
        padding: 8px 0;
        margin: 8px 0;
    }
    
    .receipt-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
        font-size: 11px;
    }
    
    .receipt-total {
        font-weight: bold;
        font-size: 14px;
        text-align: right;
        margin-top: 8px;
    }
    
    .receipt-footer {
        text-align: center;
        font-size: 10px;
        margin-top: 12px;
        color: #666;
    }
    
    /* Hide buttons when printing */
    button {
        display: none !important;
    }
}
</style>