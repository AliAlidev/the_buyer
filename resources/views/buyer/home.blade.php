@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="page-title">
                        <h4 class="mb-0 font-size-18">Starter Page</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Agroxa</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                            <li class="breadcrumb-item active">Starter Page</li>
                        </ol>
                    </div>

                    <div class="state-information d-none d-sm-block">
                        <div class="state-graph">
                            <div id="header-chart-1"></div>
                            <div class="info">Balance $ 2,317</div>
                        </div>
                        <div class="state-graph">
                            <div id="header-chart-2"></div>
                            <div class="info">Item Sold 1230</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <p>Main</p>
                                <form method="POST" action="{{ route('add-data') }}">
                                    @csrf
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if (session()->has('success'))
                                        <div class="alert alert-success">
                                            {{ session()->get('success') }}
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div id="my_camera"></div>
                                            <br />
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                            </div>
                                            <label for="result">Code</label>
                                            <input class="form-control" id="result" type="text" name="code"
                                                value="{{ old('code') }}" placeholder="SCAN CODE">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="mt-3" for="">Quantity</label>
                                                    <input class="form-control" type="number"
                                                        value="{{ old('quantity') }}" name="quantity">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="mt-3" for="">Price</label>
                                                    <input class="form-control" type="number" value="{{ old('price') }}"
                                                        name="price">
                                                </div>
                                            </div>
                                            <label class="mt-3" for="">Expiry Date</label>
                                            <input class="form-control" type="date" value="{{ old('expiry_date') }}"
                                                name="expiry_date">
                                            <label class="mt-3" for="">Description</label>
                                            <textarea class="form-control" name="description" id="" cols="30" rows="10">{{ old('description') }}</textarea>
                                        </div>

                                        <div class="col-md-12 text-center">
                                            <br />
                                            <button class="btn btn-success">Add</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page content-->

    </div> <!-- container-fluid -->
@endsection

@push('scripts')
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            $('#result').empty();
            $('#result').val(decodedResult.decodedText);
            console.log(decodedResult);
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
            // for example:
            console.warn(`Code scan error = ${error}`);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "my_camera", {
                fps: 10,
                qrbox: 250
            },
            /* verbose= */
            false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
@endpush
