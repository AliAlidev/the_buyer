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
                                        <div style="width: 10%">
                                            <input id="start_cam" type="button" value="Start Cam" data-id="1"
                                                onclick="startCam()" class="btn btn-primary">
                                        </div>
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
        var html5QrCode;

        function startCam() {
            var curr_status = $('#start_cam').data('id');
            if (curr_status == 1) {
                html5QrCode = new Html5Qrcode("my_camera", true);
                const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                    $('#result').empty();
                    $('#result').val(decodedResult.decodedText);
                    // stop
                    html5QrCode.stop();
                    $('#start_cam').val("Start Cam");
                    $('#start_cam').data('id', 1);
                    $('#my_camera').empty();
                };

                const config = {
                    fps: 20,
                    qrbox: 250
                };

                // Start back camera and if not found start front cam
                html5QrCode.start({
                    facingMode: {
                        exact: "environment"
                    }
                }, config, qrCodeSuccessCallback).catch((err) => {
                    html5QrCode.start({
                        facingMode: {
                            exact: "user"
                        }
                    }, config, qrCodeSuccessCallback)
                });

                $('#start_cam').val("Stop Cam");
                $('#start_cam').data('id', 2);
            } else if (curr_status == 2) {
                html5QrCode.stop();
                $('#start_cam').val("Start Cam");
                $('#start_cam').data('id', 1);
                $('#my_camera').empty();
            }
        }
    </script>
@endpush
