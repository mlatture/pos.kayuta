<?php

namespace App\Http\Controllers;

use App\CPU\ImageManager;
use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use App\Models\CardOnFile;
use App\Models\Receipt;
use App\Models\CartReservation;
use App\Models\SystemLog;
use App\Models\SeasonalRate;
use App\Models\GiftCard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin_has_permission:' . config('constants.role_modules.list_customers.value'))->only(['index']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.create_customers.value'))->only(['create', 'store']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.edit_customers.value'))->only(['edit', 'update']);
        $this->middleware('admin_has_permission:' . config('constants.role_modules.delete_customers.value'))->only(['destroy']);
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
                ->addIndexColumn()
                ->addColumn('actions', function ($customer) {
                    $documents = '<a href="' . route('customers.documents', $customer->id) . '" class="btn btn-secondary"><i class="fa-solid fa-folder-open"></i></a>';
                    $viewButton = '<a href="' . route('customers.show', $customer->id) . '" class="btn btn-info"><i class="fas fa-eye"></i></a>';
                    $editButton = auth()->user()->hasPermission(config('constants.role_modules.edit_customers.value')) ? '<a href="' . route('customers.edit', $customer->id) . '" class="btn btn-primary"><i class="fas fa-edit"></i></a>' : '';

                    $deleteButton = auth()->user()->hasPermission(config('constants.role_modules.delete_customers.value')) ? '<button class="btn btn-danger btn-delete" data-url="' . route('customers.destroy', $customer->id) . '"><i class="fas fa-trash"></i></button>' : '';

                    return $documents . '  ' . $viewButton . ' ' . $editButton . ' ' . $deleteButton;
                })
                ->addColumn('seasonal_names', function ($customer) {
                    $seasonalIds = is_array($customer->seasonal) ? $customer->seasonal : json_decode($customer->seasonal ?? '[]', true);

                    $rates = SeasonalRate::whereIn('id', $seasonalIds)->pluck('rate_name')->toArray();
                    $names = implode(', ', $rates);

                    return '<button class="btn btn-sm btn-outline-primary edit-seasonal" data-id="' . $customer->id . '" data-selected="' . implode(',', $seasonalIds) . '">' . ($names ?: 'None') . '</button>';
                })

                ->editColumn('created_at', function ($customer) {
                    return Carbon::parse($customer->created_at)->format('F j, Y'); // Format date
                })
                ->rawColumns(['actions', 'seasonal_names']) // Ensures buttons render properly
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
        return view('admin.customers.account', compact('customer'));
    }

    public function balance(User $customer)
    {
        $rDue = (float) Reservation::where('customernumber', $customer->id)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!==', 'Cancelled');
            })
            ->sum(DB::raw('COALESCE(totalcharges,0) - COALESCE(totalpayments,0)'));

        $uDue = 0.0;
        $sDue = 0.0;
        $pDue = 0.0;
        $gCredit = 0.0;

        $total = round($rDue + $uDue + $sDue + $pDue - $gCredit, 2);

        return response()->json([
            'success' => true,
            'total' => $total,
            'parts' => [
                'r' => ['due' => $rDue],
                'u' => ['due' => $uDue],
                's' => ['due' => $sDue],
                'p' => ['due' => $pDue],
                'g' => ['credit' => $gCredit],
            ],
        ]);
    }

    public function receipts(User $customer)
    {
        $rows = Reservation::where('customernumber', $customer->id)
            ->select([DB::raw('COALESCE(created, created_at, createdate) as d'), 'status', 'confirmation', 'xconfnum', 'rid', 'id', 'totalpayments'])
            ->orderByDesc('d')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                $status = (string) ($r->status ?? 'N/A');
                $cls = match ($status) {
                    'Paid', 'Success', 'Confirmed' => 'bg-success',
                    'Pending', 'Processing' => 'bg-warning text-dark',
                    'Cancelled', 'Failed' => 'bg-danger',
                    default => 'bg-secondary',
                };

                $reference = $r->confirmation ?: ($r->xconfnum ?: ($r->rid ?: $r->id));

                return [
                    'date' => $this->fmtDate($r->d),
                    'type' => 'Reservation',
                    'reference' => (string) $reference,
                    'amount' => (float) ($r->totalpayments ?? 0),
                    'status_badge' => '<span class="badge ' . $cls . '">' . e($status) . '</span>',
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
}
