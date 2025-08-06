@extends('layouts.admin')

@section('content')
    <div class="container-fluid mt-4">
        <h4 class="mb-4 text-primary">📄 Contracts</h4>

        @if (session('error'))
            <div class="alert alert-warning">{{ session('error') }}</div>
        @endif

        @if ($rates->isEmpty())
            <div class="alert alert-info">
                No contract templates found for this customer.
            </div>
        @else
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Customer Name:</strong> {{ $user->f_name }} {{ $user->l_name }}<br>
                        <strong>Customer Email:</strong> {{ $user->email }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-secondary">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Document Name</th>
                                    <th>Actions</th>
                                    <th>Downloadable Files </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rates as $index => $rate)
                                    <tr class="text-center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $rate->rate_name }}</td>
                                        <td>

                                            {{ pathInfo($fileName, PATHINFO_FILENAME)}}

                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $fileName) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary me-2">
                                                    📄 Preview
                                                </a>

                                                <a href="{{ asset('storage/' . $fileName) }}" download
                                                    class="btn btn-sm btn-outline-secondary">
                                                    ⬇️ Download
                                                </a>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ asset('storage/' . $template) }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary">
                                                ⬇️ Download Template
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                ⬅️ Back to Renewals
            </a>
        </div>
    </div>
@endsection
