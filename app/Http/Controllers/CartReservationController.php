<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartReservation;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CartReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reservations = CartReservation::with('customer')->get();

            return DataTables::of($reservations)
                ->addIndexColumn()
                ->addColumn('actions', function ($reservation) {
                    $viewButton = '<a href="' . route('sites.view', $reservation->id) . '" class="btn btn-info"><i class="fas fa-eye"></i></a>';
                    $editButton = '<a href="' . route('reservations.payment.index', $reservation->cartid) . '" class="btn btn-primary"><i class="fas fa-edit"></i></a>';
                    $deleteButton =  '<button class="btn btn-danger btn-delete" data-url="' . route('cart-reservation.destroy', $reservation->id) . '"><i class="fas fa-trash"></i></button>';
                    return $viewButton . ' ' . $editButton . ' ' . $deleteButton;
                })
                ->editColumn('cid', function ($reservation) {
                    return Carbon::parse($reservation->cid)->format('F j, Y');
                })
                ->editColumn('cod', function ($reservation) {
                    return Carbon::parse($reservation->cod)->format('F j, Y');
                })
                ->addColumn('customer_name', function ($reservation) {
                    return $reservation->customer ? $reservation->customer->first_name . ' ' . $reservation->customer->last_name : 'N/A';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('cart-reservations.index');
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|string', 
            'siteid' => 'required|array'
        ]);

        if ($request->ajax()) {
            try {
                DB::beginTransaction(); 

                $reservation = CartReservation::where('cartid', $request->confirmation)->first();

                if (!$reservation) {
                    return response()->json(['success' => false, 'message' => 'Reservation not found'], 404);
                }

                $sites = Site::whereIn('siteid', $request->siteid)->get();

                if ($sites->isEmpty()) {
                    return response()->json(['success' => false, 'message' => 'Sites not found'], 404);
                }

                Site::whereIn('siteid', $request->siteid)->update([
                    'available' => 1,
                    'availableonline' => 1
                ]);

                $reservation->delete();

                DB::commit(); 

                return response()->json([
                    'success' => true,
                    'message' => 'Reservation and site(s) canceled successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 
                Log::error("Error canceling reservation: " . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while canceling the reservation'
                ], 500);
            }
        }

        return redirect()->back()->with('success', 'Reservation canceled successfully!');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CartReservation $cartReservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CartReservation $cartReservation)
    {
       
        $cartReservation->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
