<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Return Order</h5>
                <button type="button" class="btn-close border-0 bg-transparent" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- List of products will be injected here via JS -->
            </div>
            <div class="modal-footer">
                <div class="footer-content">
                    <p id="totalAmount" class="text-primary mb-0">Total Amount to Refund: <span class="amount">$0.00</span></p>
                    <button class="btn btn-danger">Refund</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
   .modal-footer {
        border-top: none;
        padding: 15px 30px;
        background-color: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .footer-content {
        display: flex;
        align-items: center;
        width: 100%;
        justify-content: space-between;
    }

    #totalAmount {
        font-size: 1.1em;
        font-weight: 600;
        color: #007bff;
    }

    .amount {
        font-size: 1.2em;
        font-weight: bold;
    }

    .modal-footer .btn {
        font-size: 1em;
        padding: 10px 20px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .modal-footer .btn-danger {
        background-color: #dc3545;
        color: #ffffff;
    }

    .modal-footer .btn-danger:hover {
        background-color: #c82333;
    }
    .list-group {
        padding-left: 0;
        margin-bottom: 20px;
        list-style: none;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 20px;
        font-size: 12px;
    }

    .modal-body {
        overflow-y: scroll;
        height: 70vh;
    }

    .list-group-item {
        position: relative;
        display: block;
        padding: 15px 20px;
        margin-bottom: 10px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .list-group-item:hover {
        background-color: #f0f8ff;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .list-group-item.selected {
        border: 2px solid #00acc1;
        background-color: #e0f7fa;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .list-group-item strong {
        display: block;
        font-size: 1.1em;
        margin-bottom: 5px;
    }

    .list-group-item small {
        color: #666;
        font-size: 0.9em;
    }

    .list-group-item .price,
    .list-group-item .quantity {
        color: #555;
        font-size: 1em;
    }

    /* Modal body padding adjustment */
    #returnModal .modal-body {
        padding: 20px;
    }
</style><?php /**PATH C:\Users\THOMAS JON\OneDrive\Desktop\pos.kayuta\resources\views/orders/modals/return-modal.blade.php ENDPATH**/ ?>