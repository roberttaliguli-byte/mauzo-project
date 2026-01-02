class ReceiptManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Print receipt checkbox
        const printReceiptCheckbox = document.getElementById('print-receipt-checkbox');
        if (printReceiptCheckbox) {
            printReceiptCheckbox.addEventListener('change', () => {
                localStorage.setItem('printReceipt', printReceiptCheckbox.checked);
            });
        }

        // Check if checkbox should be checked from localStorage
        const savedPrintReceipt = localStorage.getItem('printReceipt') === 'true';
        if (printReceiptCheckbox) {
            printReceiptCheckbox.checked = savedPrintReceipt;
        }

        // Close receipt modal
        const closeReceiptBtn = document.getElementById('close-receipt-btn');
        if (closeReceiptBtn) {
            closeReceiptBtn.addEventListener('click', () => {
                this.closeReceiptModal();
            });
        }

        // Print receipt button
        const printReceiptBtn = document.getElementById('print-receipt-btn');
        if (printReceiptBtn) {
            printReceiptBtn.addEventListener('click', () => {
                window.print();
            });
        }

        // Close modal on overlay click
        const receiptModal = document.getElementById('receipt-modal');
        if (receiptModal) {
            receiptModal.addEventListener('click', (e) => {
                if (e.target === receiptModal || e.target.classList.contains('modal-overlay')) {
                    this.closeReceiptModal();
                }
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeReceiptModal();
            }
        });
    }

    generateReceipt(saleData) {
        const receiptContent = document.getElementById('receipt-content');
        if (!receiptContent) return;

        const now = new Date();
        const dateStr = now.toLocaleDateString('sw-TZ');
        const timeStr = now.toLocaleTimeString('sw-TZ', { hour: '2-digit', minute: '2-digit' });
        
        let itemsHtml = '';
        let total = 0;
        
        if (Array.isArray(saleData.items)) {
            saleData.items.forEach((item, index) => {
                const itemTotal = (item.bei || 0) * (item.idadi || 0);
                total += itemTotal;
                
                itemsHtml += `
                    <div class="receipt-item">
                        <div class="flex-1 truncate">${index + 1}. ${item.jina || item.product_name || 'Bidhaa'}</div>
                        <div class="text-right">
                            ${item.idadi || 1} x ${this.formatCurrency(item.bei || 0)}
                        </div>
                    </div>
                    <div class="receipt-item">
                        <div class="flex-1"></div>
                        <div class="text-right font-medium">${this.formatCurrency(itemTotal)}</div>
                    </div>
                `;
            });
        } else {
            // Single item sale
            const itemTotal = (saleData.bei || 0) * (saleData.idadi || 0);
            total = itemTotal;
            
            itemsHtml = `
                <div class="receipt-item">
                    <div class="flex-1 truncate">1. ${saleData.jina || saleData.product_name || 'Bidhaa'}</div>
                    <div class="text-right">
                        ${saleData.idadi || 1} x ${this.formatCurrency(saleData.bei || 0)}
                    </div>
                </div>
                <div class="receipt-item">
                    <div class="flex-1"></div>
                    <div class="text-right font-medium">${this.formatCurrency(itemTotal)}</div>
                </div>
            `;
        }

        const receiptHtml = `
            <div class="receipt-header">
                <div class="receipt-title">DEMODAY STORE</div>
                <div class="receipt-company">Karibu Tena</div>
            </div>
            
            <div class="receipt-details">
                <div>Tarehe: ${dateStr}</div>
                <div>Saa: ${timeStr}</div>
                <div>Namba ya Risiti: ${saleData.receipt_number || '00000'}</div>
                <div>Kass: ${saleData.user_name || 'Mfanyakazi'}</div>
            </div>
            
            <div class="receipt-items">
                ${itemsHtml}
            </div>
            
            <div class="receipt-total">
                Jumla: ${this.formatCurrency(total)}
            </div>
            
            ${saleData.punguzo ? `
                <div class="receipt-item">
                    <div>Punguzo:</div>
                    <div>${this.formatCurrency(saleData.punguzo)}</div>
                </div>
                <div class="receipt-total">
                    Kulipwa: ${this.formatCurrency(total - (saleData.punguzo || 0))}
                </div>
            ` : ''}
            
            <div class="receipt-footer">
                <div>Asante kwa Kununua!</div>
                <div>Mauzo hayarudishwi</div>
                <div>Simu: 0757XXXXXX</div>
            </div>
        `;

        receiptContent.innerHTML = receiptHtml;
        this.openReceiptModal();
    }

    openReceiptModal() {
        const receiptModal = document.getElementById('receipt-modal');
        if (receiptModal) {
            receiptModal.classList.remove('hidden');
            
            // Focus print button after a short delay
            setTimeout(() => {
                const printBtn = document.getElementById('print-receipt-btn');
                if (printBtn) printBtn.focus();
            }, 100);
        }
    }

    closeReceiptModal() {
        const receiptModal = document.getElementById('receipt-modal');
        if (receiptModal) {
            receiptModal.classList.add('hidden');
        }
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('sw-TZ', {
            style: 'currency',
            currency: 'TZS',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Method to be called after successful sale
    showReceiptAfterSale(saleResponse) {
        const printReceiptCheckbox = document.getElementById('print-receipt-checkbox');
        
        if (printReceiptCheckbox && printReceiptCheckbox.checked) {
            // Generate receipt from sale response
            this.generateReceipt(saleResponse);
            
            // Auto-print after 1 second if supported
            setTimeout(() => {
                if (window.print) {
                    window.print();
                }
            }, 1000);
        }
    }
}

// Export for use in your main MauzoManager
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ReceiptManager;
} else {
    window.ReceiptManager = ReceiptManager;
}