@extends('layouts.admin')

@section('title', 'Relocate & Re-Schedule')
@section('content-header', 'Relocate & Re-Schedule')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                Relocate
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($reservation)
                                <div class="row">
                                    <div class="col">
                                        <label for="">Name</label>
                                        <input type="text" class="form-control"
                                            value="{{ $reservation->fname }} {{ $reservation->lname }}" readonly>
                                    </div>
                                    <div class="col">
                                        <label for="">Site Class</label>
                                        <input type="text" class="form-control" value="{{ $reservation->siteclass }}"
                                            readonly>
                                    </div>
                                    <div class="col">
                                        <label for="">Site ID</label>
                                        <input type="text" class="form-control" value="{{ $reservation->siteid }}"
                                            readonly>
                                    </div>
                                </div>
                            @else
                                <p>No reservation found for this Cart ID.</p>
                            @endif
                            
                            @if($siteclasses)
                                <div class="row mt-3">
                                    <div class="col">
                                        <label for="">Select new Site Class</label>
                                        <select name="siteclass" class="form-control" id="sitelclass">
                                            @foreach ($siteclasses as $siteclass)
                                                <option value="{{ $siteclass->id }}">{{ $siteclass->siteclass }}</option>
                                            @endforeach 
                                        </select>
                                    </div>
                                </div>
                            @else   
                                <p>No site classes found.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                Re-Schedule
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')



    </script>
@endsection
