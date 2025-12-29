<?php

namespace App\Http\Controllers;

use App\CPU\ImageManager;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\Reservation;
use App\Models\SeasonalCustomerDiscount;

use Illuminate\Support\Facades\DB;
use App\Models\CardOnFile;
use App\Models\Receipt;
use App\Models\CartReservation;
use App\Models\SystemLog;
use App\Models\SeasonalRate;
use App\Models\GiftCard;

use App\Services\CustomerBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

use Illuminate\Support\Facades\Notification;
use App\Notifications\ReservationConfirmation;

class CustomerController extends Controller
{
    protected CustomerBalanceService $balanceService;
    public function __construct(CustomerBalanceService $balanceService)
    {
        $this->middleware('admin_has_permission:' . config('constants.role_modules.list_customers.value'))->only(['index']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.create_customers.value'))->only(['create', 'store']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.edit_customers.value'))->only(['edit', 'update']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.delete_customers.value'))->only(['destroy']);

        $this->balanceService = $balanceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = User::select('id', 'f_name', 'l_name', 'email', 'phone', 'street_address', 'seasonal', 'created_at')->where('id', '!=', 0)->latest();

            if ($request->only_seasonal) {
                $customers->whereJsonLength('seasonal', '>', 0);
            }

            return DataTables::of($customers)
                ->addColumn('full_name', function ($customer) {
                    return ucwords($customer->f_name . ' ' . $customer->l_name);
                })
                ->addColumn('email', function ($customer) {
                    return strtolower($customer->email) ?? 'N/A';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $query->where(function ($q) use ($search) {
                            $q->where('f_name', 'like', "%{$search}%")
                                ->orWhere('l_name', 'like', "%{$search}%")
                                ->orWhere(DB::raw("CONCAT(f_name, ' ', l_name)"), 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                            // ->orWhere('street_address', 'like', "%{$search}%");
                        });
                    }
                })
                ->addIndexColumn()
                ->addColumn('actions', function ($customer) {
                    $documents = '<a href="' . route('customers.documents', $customer->id) . '" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="See Files" role="tooltip"><i class="fa-solid fa-folder-open" ></i></a>';
                    $viewButton = '<a href="' . route('customers.show', $customer->id) . '" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="View Customer"><i class="fas fa-eye" ></i></a>';
                    $editButton = auth()->user()->hasPermission(config('constants.role_modules.edit_customers.value')) ? '<a href="' . route('customers.edit', $customer->id) . '" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Customer Details"><i class="fas fa-edit" ></i></a>' : '';

                    $deleteButton = auth()->user()->hasPermission(config('constants.role_modules.delete_customers.value')) ? '<button class="btn btn-danger btn-delete" data-url="' . route('customers.destroy', $customer->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Customer"><i class="fas fa-trash" ></i></button>' : '';

                    return $documents . '  ' . $viewButton . ' ' . $editButton . ' ' . $deleteButton;
                })
                ->addColumn('seasonal_names', function ($customer) {
                    $seasonalIds = is_array($customer->seasonal) ? $customer->seasonal : json_decode($customer->seasonal ?? '[]', true);

                    $rates = SeasonalRate::whereIn('id', $seasonalIds)->pluck('rate_name')->toArray();
                    $names = implode(', ', $rates);

                    $discounts = SeasonalCustomerDiscount::where('customer_id', $customer->id)
                        ->where('is_active', true)
                        ->get()
                        ->map(function ($d) {
                            return [
                                'type' => $d->discount_type,
                                'value' => $d->discount_value,
                            ];
                        });

                    $discountBadges = '';
                    foreach ($discounts as $discount) {
                        $type = $discount['type'];
                        $val = $discount['value'];

                        // Choose symbol for quick indicator
                        $symbol = '';
                        if ($type === 'percentage') {
                            $symbol = '%';
                        } elseif ($type === 'dollar') {
                            $symbol = '$';
                        }

                        $discountBadges .= '<span class="badge bg-info text-dark mx-1">' . ucfirst($type) . ': ' . $symbol . $val . '</span>';
                    }

                    return '<button class="btn btn-sm btn-outline-primary edit-seasonal"
                        data-id="' .
                        $customer->id .
                        '"
                        data-selected="' .
                        implode(',', $seasonalIds) .
                        '"
                        data-discounts=\'' .
                        json_encode($discounts) .
                        '\'>
                        ' .
                        ($names ?: 'None') .
                        '
                    </button>
                     <div class="mt-1">' .
                        $discountBadges .
                        '</div>';
                })
                ->editColumn('created_at', function ($customer) {
                    return Carbon::parse($customer->created_at)->format('F j, Y');
                })
                ->rawColumns(['actions', 'seasonal_names'])
                ->make(true);
        }

        return view('customers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = User::with([
            'reservations' => function ($query) {
                $query->latest();
            },
            'cart_reservations' => function ($query) {
                $query->latest();
            },
            'receipts' => function ($query) {
                $query->latest();
            },
            'cardsOnFile' => function ($query) {
                $query->latest();
            },
        ])->findOrFail($id);

        $groupedReservations = $customer->reservations->groupBy('cartid')->map(function ($groupRes) {
            $siteId = $groupRes->pluck('siteid')->unique()->implode(', ');
            return $siteId;
        });

        $groupedCartReservations = $customer->cart_reservations->groupBy('cartid')->map(function ($group) {
            $sites = $group->pluck('siteid')->unique()->implode(', ');
            return $sites;
        });

        $customer->setRelation('cardsOnFile', collect($customer->cardsOnFile)->unique('xmaskedcardnumber'));

        return view('customers.show', compact('customer', 'groupedReservations', 'groupedCartReservations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $request)
    {
        $avatar_path = '';

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $avatar_path = ImageManager::upload('customers/', $file->getClientOriginalExtension(), $file);
        }
        $password = rand('00000000', '99999999');

        $customer = User::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->first_name . ' ' . $request->last_name,
            'f_name' => $request->first_name,
            'l_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($password),
            'phone' => $request->phone ?? '',
            'street_address' => $request->address,
            'image' => $avatar_path,
            // 'user_id' => $request->user()->id,
        ]);
        if (isset($request->is_modal)) {
            if (!$customer) {
                return response()->json(['status' => 'error', 'message' => 'Sorry, Something went wrong while creating customer.'], 200);
            }
            return response()->json(['status' => 'success', 'message' => 'Success, New customer has been added successfully!', 'data' => $customer], 200);
        }
        if (!$customer) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating customer.');
        }
        return redirect()->route('customers.index')->with('success', 'Success, New customer has been added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(User $customer)
    {
        if ($customer->organization_id == auth()->user()->organization_id || auth()->user()->admin_role_id == 1) {
            return view('customers.edit', compact('customer'));
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $customer)
    {
        $customer->f_name = $request->first_name;
        $customer->l_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone ?? '';
        $customer->street_address = $request->address;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $avatar_path = ImageManager::update('customers/', $customer->image, $file->getClientOriginalExtension(), $file);
            $customer->image = $avatar_path;
        }

        if (!$customer->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('customers.index')->with('success', 'Success, The customer has been updated.');
    }

    public function destroy(User $customer)
    {
        if ($customer->image) {
            ImageManager::delete('customers/' . $customer->image);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function customerInfo(Request $request)
    {
        $customer = Customer::where('email', $request->email)->first();

        if ($customer) {
            return response()->json([
                'success' => true,
                'info' => [
                    'fname' => $customer->first_name,
                    'lname' => $customer->last_name,
                    'con' => $customer->phone,
                    'address' => $customer->address,
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No user found with this email.',
            ]);
        }
    }

    public function updateSeasonal(Request $request, User $user)
    {
        $user->seasonal = $request->input('seasonal', []);
        $user->save();
        return response()->json(['success' => true, 'message' => 'Seasonal updated successfully.']);
    }

    public function account(User $customer)
    {
        $customer->reservations = $customer->reservations()->orderBy('cartid', 'desc')->get();
        return view('admin.customers.account', compact('customer'));
    }

    public function balance(User $customer)
    {
        $balanceData = $this->balanceService->getDetailedBalance($customer);
        return response()->json($balanceData);
    }

    public function receipts(User $customer)
    {
        $resPayments = DB::table('payments')
            ->where('customernumber', $customer->id)
            ->select(['created_at as d', 'payment as amount', 'cartid as ref', DB::raw("'Reservation' as type")]);

        $utilPayments = DB::table('payment_bills')
            ->where('customer_id', $customer->id)
            ->select(['created_at as d', 'payment as amount', 'site as ref', DB::raw("'Utility' as type")]);

        $posPayments = DB::table('pos_payments')->select(['created_at as d', 'amount as amount', 'order_id as ref', DB::raw("'POS' as type")]);

        $rows = $resPayments
            ->union($utilPayments)
            ->union($posPayments)
            ->orderByDesc('d')
            ->limit(20)
            ->get()
            ->map(function ($p) {
                return [
                    'date' => \Carbon\Carbon::parse($p->d)->format('M d, Y'),
                    'type' => $p->type,
                    'reference' => '#' . $p->ref,
                    'amount' => (float) $p->amount,
                    'status_badge' => '<span class="badge bg-success">Paid</span>',
                ];
            });

        return response()->json(['rows' => $rows]);
    }

    protected function fmtDate($value, $format = 'Y-m-d')
    {
        if (!$value) {
            return '';
        }

        try {
            return Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return $value; // fallback if parsing fails
        }
    }

    public function send(Request $request, $customerId)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'to' => 'required|string',
            'cc' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);

        $to = array_map('trim', explode(',', $request->to));
        $cc = $request->filled('cc') ? array_map('trim', explode(',', $request->cc)) : [];

        Notification::route('mail', $to)->notify(new ReservationConfirmation($reservation, $cc, $request->content));

        SystemLog::create([
            'transaction_type' => 'Email Resent',
            'reference_id' => $reservation->id,
            'description' => 'Confirmation email resent',
            'performed_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Confirmation email resent successfully.',
        ]);
    }
}
