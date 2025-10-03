{{-- resources/views/admin/seasonal_discounts/index.blade.php --}}
@extends('layouts.admin')

@section('title', "Seasonal Discounts — {$customer->f_name} {$customer->l_name} — {$year}")

@section('content')
    <style>
        html,
        body {
            height: 100%;
            -webkit-text-size-adjust: 100%;
        }

        .page-content,
        .content,
        .container,
        .main,
        .page-wrapper {
            max-width: 100% !important;
            width: 100% !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
            box-sizing: border-box !important;
            margin-left: 0 !important;
        }


        .card {
            width: 100%;
            box-sizing: border-box;
        }

        .card.aside,
        aside.card {
            box-sizing: border-box;
            max-width: 100%;
        }

        .muted {
            color: #6b7280;
        }

        /* soft grey */
        .small {
            font-size: .9rem;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid #e5e7eb;
            color: #111827;
        }

        .grid {
            display: grid;
            gap: 12px;
            width: 100%;
            box-sizing: border-box;
        }

        .grid.grid-2 {
            grid-template-columns: 1fr;
        }


        @media (min-width: 900px) {
            .grid.grid-2 {
                grid-template-columns: 1fr 360px;
                max-width: 1100px;
                margin: 0 auto;
            }
        }


        /* table styles (desktop) */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            text-align: left;
            vertical-align: middle;
        }

        .muted-small {
            color: #6b7280;
            font-size: .9rem;
        }

        @media (min-width: 861px) {
            .discount-cards {
                display: none;
            }
        }


        @media (max-width: 860px) {
            table {
                display: none;
            }

            .discount-cards {
                display: block;
            }

            .discount-card {
                border: 1px solid #eef2ff;
                background: #fff;
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 10px;
            }

            .btn {
                min-height: 44px;
            }
        }


        /* ---------- Mobile transforms ---------- */
        @media (max-width: 860px) {
            table {
                display: none;
            }

            /* hide desktop table on small devices */
            .discount-cards {
                display: block;
            }

            .discount-card {
                border: 1px solid #eef2ff;
                background: #ffffff;
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 10px;
            }

            .discount-row {
                display: flex;
                flex-direction: row;
                gap: 8px;
                align-items: center;
                justify-content: space-between;
            }

            .discount-meta {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .discount-actions {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 8px;
            }

            .btn {
                padding: 10px;
                min-height: 44px;
            }

            /* touch target */
            .btn.block {
                display: block;
                width: 100%;
                text-align: center;
            }
        }

        @media (min-width:861px) {
            .discount-cards {
                display: none;
            }
        }

        #addModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1200;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        /* Modal document */
        #addModal .modal-inner {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            position: relative;
            box-shadow: 0 12px 40px rgba(2, 6, 23, 0.12);
            box-sizing: border-box;
        }

        /* Header close */
        #addModal .close-btn {
            position: absolute;
            right: 12px;
            top: 12px;
            border: 0;
            background: transparent;
            font-size: 1.25rem;
            line-height: 1;
            cursor: pointer;
        }

        /* Form layout */
        #addModal .form-row {
            display: flex;
            gap: 12px;
            align-items: stretch;
        }

        #addModal .form-row .col {
            box-sizing: border-box;
        }

        #addModal input[type="number"],
        #addModal select,
        #addModal textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e6e6e6;
            font-size: 15px;
            box-sizing: border-box;
        }

        #addModal label {
            display: block;
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 6px;
        }

        /* Buttons */
        #addModal .btn {
            padding: 10px 14px;
            border-radius: 8px;
            border: 0;
            cursor: pointer;
            font-size: 0.95rem;
        }

        #addModal .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        #addModal .btn-ghost {
            background: transparent;
            border: 1px solid #e5e7eb;
            color: #111827;
        }

        /* Preview area */
        #addModal .preview {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }

        /* Mobile: full-screen dialog */
        @media (max-width:640px) {
            #addModal {
                align-items: flex-end;
                justify-content: center;
                padding: 0;
            }

            #addModal .modal-inner {
                width: 100%;
                height: 100%;
                max-width: 100%;
                border-radius: 0;
                padding: 18px 14px;
                overflow: auto;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
            }

            #addModal .close-btn {
                right: 10px;
                top: 10px;
            }

            #addModal .modal-footer {
                position: sticky;
                bottom: 0;
                background: linear-gradient(transparent, #fff);
                padding-top: 12px;
                padding-bottom: 12px;
            }

            #addModal .form-row {
                flex-direction: column;
            }
        }

        /* Small helpers */
        .muted-small {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .hidden {
            display: none !important;
        }



        /* badges */
        .badge-pill {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f3f4f6;
            font-weight: 600;
        }

        .badge-active {
            background: #ecfdf5;
            color: #064e3b;
            padding: 6px 10px;
            border-radius: 999px;
        }

        .badge-inactive {
            background: #fff1f2;
            color: #7f1d1d;
            padding: 6px 10px;
            border-radius: 999px;
        }
    </style>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" style="margin-bottom:12px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-2 p-4" style="margin-bottom:16px;">
        <!-- left: discount list + actions -->
        <div class="card p-2">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <div>
                    <h2 style="margin:0;">Discounts — {{ $customer->f_name . ' ' . $customer->l_name }}</h2>
                    <div class="muted small">Season: <strong>{{ $year }}</strong></div>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button id="openAddBtn" class="btn btn-primary" aria-haspopup="dialog" aria-controls="addModal">Add
                        discount</button>
                    <a href="{{ route('seasonal.customer.discounts.index', [$customer->id, 'year' => $year]) }}"
                        class="btn btn-ghost">Refresh</a>
                </div>
            </div>



            <div>
                <table aria-hidden="false">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Description</th>
                            <th>Active</th>
                            <th class="muted-small">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="discountList">
                        @forelse($discounts as $d)
                            <tr data-id="{{ $d->id }}" data-type="{{ $d->discount_type }}"
                                data-value="{{ $d->discount_value }}" data-desc="{{ e($d->description) }}">
                                <td>
                                    @if ($d->discount_type === 'percentage')
                                        <span class="badge-pill">Percent</span>
                                    @else
                                        <span class="badge-pill">$ Dollar</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($d->discount_type === 'percentage')
                                        {{ number_format($d->discount_value) }}%
                                    @else
                                        ${{ number_format($d->discount_value, 2) }}
                                    @endif
                                </td>
                                <td class="small">{{ $d->description }}</td>
                                <td>
                                    @if ($d->is_active)
                                        <span class="badge-active">Active</span>
                                    @else
                                        <span class="badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($d->is_active)
                                        <form action="{{ route('seasonal.customer.discounts.deactivate', $d->id) }}"
                                            method="POST" style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to deactivate this discount?');">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost"
                                                title="Deactivate this discount">Deactivate</button>
                                        </form>
                                    @else
                                        <form action="{{ route('seasonal.customer.discounts.activate', $d->id) }}"
                                            method="POST" style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to activate this discount?');">
                                            @csrf
                                            <button type="submit" class="btn btn-primary"
                                                title="Activate this discount">Activate</button>
                                        </form>
                                    @endif

                                    {{-- Delete --}}
                                    <form action="{{ route('seasonal.customer.discounts.destroy', $d->id) }}"
                                        method="POST" style="display:inline;"
                                        onsubmit="return confirm('Are you sure you want to permanently delete this discount?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn"
                                            style="background:#ef4444;color:#fff;border-radius:6px;margin-left:8px;">Delete</button>
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted-small">No discounts for this customer / season.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>


            </div>
        </div>

        <!-- right: preview & instructions -->
        <aside class="card p-2" aria-labelledby="previewHeading">
            <h3 id="previewHeading">Quick preview & instructions</h3>

            <p class="muted-small">When adding discounts, percentages are applied <strong>first</strong> (multiplicatively),
                then dollar discounts are subtracted. The system prevents combined discounts from exceeding the base rate.
            </p>



            <hr style="margin:12px 0; border-color:#f3f4f6;" />

            <div>
                <p class="small muted-small"><strong>Tips</strong></p>
                <ul style="padding-left:18px;">
                    <li>Always include a clear description — it appears in the guest contract.</li>
                    <li>If your renewals screen knows the base rate, we recommend passing it to this modal (hidden).</li>
                    <li>Deactivate rather than delete when you want to preserve history for audit.</li>
                </ul>
            </div>
        </aside>
    </div>

    {{-- Add discount modal --}}
    <div id="addModal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal-inner" role="document">
            <button id="closeAddBtn" aria-label="Close"
                style="position:absolute; right:12px; top:12px; border:0; background:transparent; font-size:1.25rem;">✕</button>
            <h3 style="margin-top:0;">Add Discount — {{ $customer->name }} ({{ $year }})</h3>

            @if (is_null($baseRate))
                <div
                    style="background:#fffbeb; border:1px solid #fcefc7; padding:10px; border-radius:6px; margin-bottom:12px;">
                    <strong>Note:</strong> This view doesn't have a base rate available.
                </div>
            @endif

            <form id="discountForm" action="{{ route('seasonal.customer.discounts.store') }}" method="POST" novalidate>
                @csrf
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <input type="hidden" name="season_year" value="{{ $year }}">
                {{-- ensure is_active always posted --}}
                <input type="hidden" name="is_active" value="0">

                <div style="display:flex; gap:12px; margin-bottom:8px;" class="form-row">
                    <div style="flex:1;">
                        <label class="muted-small">Type</label>
                        <select id="discountType" name="discount_type" required
                            style="width:100%; padding:8px; border-radius:6px;">
                            <option value="percentage">Percentage (%)</option>
                            <option value="dollar">Dollar ($)</option>
                        </select>
                    </div>

                    <div style="width:160px;">
                        <label class="muted-small">Value</label>
                        <input id="discountValue" name="discount_value" inputmode="decimal" pattern="[0-9]*[.,]?[0-9]*"
                            type="number" step="0.01" min="0" required placeholder="10 or 50"
                            style="width:100%; padding:8px; border-radius:6px;">
                    </div>
                </div>

                <div style="margin-bottom:8px;">
                    <label class="muted-small">Description (required)</label>
                    <textarea id="discountDescription" name="description" rows="3" required
                        style="width:100%; padding:8px; border-radius:6px;"></textarea>
                </div>

                <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
                    <label class="muted-small" style="display:flex; align-items:center; gap:8px;">
                        <input id="discountActive" name="is_active" value="1" type="checkbox" checked> Active
                    </label>

                    {{-- <div style="flex:1;">
                        <label class="muted-small">Base rate (required for client preview)</label>
                        <input id="baseRateInput" name="base_rate" inputmode="decimal" pattern="[0-9]*[.,]?[0-9]*"
                            type="number" step="0.01" min="0" value="{{ $baseRate ?? '' }}"
                            style="width:100%; padding:8px; border-radius:6px;">
                    </div> --}}
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">

                    <div style="display:flex; align-items: flex-end; gap:8px;">
                        <button id="previewBtn" type="button" class="btn btn-ghost d-none">Recalculate</button>
                        <button id="submitBtn" type="submit" class="btn btn-primary">Save discount</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            // DOM references
            const openBtn = document.getElementById('openAddBtn');
            const closeBtn = document.getElementById('closeAddBtn');
            const modal = document.getElementById('addModal');
            const modalInner = modal ? modal.querySelector('.modal-inner') : null;

            const discountType = document.getElementById('discountType');
            const discountValue = document.getElementById('discountValue');
            const discountDescription = document.getElementById('discountDescription');
            const discountActive = document.getElementById('discountActive');
            const previewBtn = document.getElementById('previewBtn');
            const submitBtn = document.getElementById('submitBtn');

            const previewRemoved = document.getElementById('previewRemoved');
            const previewFinal = document.getElementById('previewFinal');
            const previewFinalValue = document.getElementById('previewFinalValue');
            const previewWarning = document.getElementById('previewWarning');
            const removedTotalClient = document.getElementById('removedTotalClient');
            const finalTotalClient = document.getElementById('finalTotalClient');

            function collectExistingDiscounts() {
                const rows = document.querySelectorAll('#discountList tr[data-id]');
                const arr = [];
                rows.forEach(r => {
                    const activeText = r.querySelector('td:nth-child(4)').innerText || '';
                    const active = activeText.toLowerCase().includes('active');
                    if (!active) return;
                    arr.push({
                        discount_type: r.getAttribute('data-type'),
                        discount_value: parseFloat(r.getAttribute('data-value')),
                        description: r.getAttribute('data-desc'),
                        is_active: true
                    });
                });
                return arr;
            }

            function applyDiscounts(discounts) {
                let percentRemoved = 0;
                let dollarRemoved = 0;

                discounts.filter(d => d.discount_type === 'percentage').forEach(d => {
                    const p = Number(d.discount_value) || 0;
                    if (p > 0) percentRemoved += p;
                });

                discounts.filter(d => d.discount_type === 'dollar').forEach(d => {
                    const a = Number(d.discount_value) || 0;
                    if (a > 0) dollarRemoved += a;
                });

                return {
                    percent_removed: percentRemoved,
                    dollar_removed: dollarRemoved,
                    total_removed: percentRemoved + dollarRemoved,
                    final: null,
                };
            }

            function recalcPreview() {
                const newd = {
                    discount_type: discountType.value,
                    discount_value: parseFloat(discountValue.value) || 0,
                    description: discountDescription.value || '',
                    is_active: discountActive.checked
                };

                const applied = applyDiscounts([newd]);

                previewRemoved.innerText = applied.total_removed.toFixed(2);
                previewFinal.innerText = '—';
                previewFinalValue.innerText = '—';
                previewWarning.innerText = '';

                submitBtn.disabled = false;
            }


            openBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
                modal.setAttribute('aria-hidden', 'false');
                setTimeout(() => {
                    if (discountType) discountType.focus();
                    recalcPreview();
                }, 50);
            });

            const closeModal = () => {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
                openBtn.focus();
            };
            closeBtn.addEventListener('click', closeModal);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    closeModal();
                }
            });

            [discountType, discountValue, discountDescription, discountActive, baseRateInput].forEach(el => {
                if (!el) return;
                el.addEventListener('input', recalcPreview);
                el.addEventListener('change', recalcPreview);
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('DOMContentLoaded', () => {
                if (previewFinalValue && previewFinalValue.innerText.trim() === '—') {
                    previewFinalValue.innerText = {!! json_encode(is_null($baseRate) ? '—' : number_format($applied['final'] ?? $baseRate, 2)) !!};
                }
            });
        })();
    </script>
@endsection
