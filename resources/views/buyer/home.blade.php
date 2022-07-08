@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="state-information d-none d-sm-block">
                        <div class="state-graph">
                            <div id="header-chart-1"></div>
                        </div>
                        <div class="state-graph">
                            <div id="header-chart-2"></div>
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
                        <div class="card-header">
                            <h4>Inventory</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
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
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8 mt-3">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <input id="start_cam" type="button" value="Start Cam" data-id="1"
                                                        onclick="startCam()" class="btn btn-primary">
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-8">
                                                    <div id="my_camera"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="result">Code</label>
                                                <div class="col-md-10">
                                                    <input class="form-control" id="result" type="text" name="code"
                                                        value="{{ old('code') }}" placeholder="SCAN CODE">
                                                    <div class="valid-feedback">
                                                        Looks good!
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <input id="getdata" type="button" class="btn btn-primary"
                                                        value="Check">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <label for="name"> Element Name</label>
                                                    <input id="name" name="name" value="{{ old('name') }}"
                                                        type="text" class="form-control" placeholder="ENTER ELEMENT NAME"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="mt-3" for="">Quantity</label>
                                                    <input id="quantity" class="form-control" type="number"
                                                        value="{{ old('quantity') }}" name="quantity"
                                                        placeholder="QUANTITY" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="mt-3" for="">Price</label>
                                                    <input id="price" class="form-control" type="number"
                                                        value="{{ old('price') }}" name="price" placeholder="PRICE"
                                                        required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="mt-3" for="">Expiry Date</label>
                                                    <input id="expiry_date" class="form-control" type="date"
                                                        value="{{ old('expiry_date') }}" name="expiry_date">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="mt-3" for="">Description</label>
                                                    <textarea id="description" class="form-control" name="description" id="" cols="30" rows="10">{{ old('description') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 text-center">
                                            <br />
                                            <button class="btn btn-primary">Add</button>
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
        $('#getdata').click(function() {
            // get code details
            var serialCode = $('#result').val();
            $.ajax({
                type: 'post',
                dataType: "JSON",
                url: "{{ route('get-data-by-serial') }}",
                data: {
                    '_token': '{{ csrf_token() }}',
                    code: serialCode
                },
                complete: function(data) {
                    data = data.responseJSON;
                    if (data.success) {
                        data = data.data;
                        $('#name').val(data.name);
                        $('#name').removeClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#name').addClass('form-control is-valid  was-validated form-control:valid');
                        $('#quantity').val(data.quantity);
                        $('#quantity').removeClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#quantity').addClass('form-control is-valid  was-validated form-control:valid');
                        $('#price').val(data.price);
                        $('#price').removeClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#price').addClass('form-control is-valid  was-validated form-control:valid');
                        $('#expiry_date').val(data.expiry_date);
                        $('#expiry_date').removeClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#expiry_date').addClass('form-control is-valid  was-validated form-control:valid');
                        $('#description').text(data.description);
                        $('#description').removeClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#description').addClass('form-control is-valid  was-validated form-control:valid');
                    } else {
                        $('#name').addClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#quantity').addClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#price').addClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#expiry_date').addClass('form-control is-invalid  was-validated form-control:invalid');
                        $('#description').addClass('form-control is-invalid  was-validated form-control:invalid');
                    }
                }
            });
        })
    </script>
    <script>
        var html5QrCode;

        function startCam() {
            var curr_status = $('#start_cam').data('id');
            if (curr_status == 1) {
                html5QrCode = new Html5Qrcode("my_camera", false);
                const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                    $('#result').empty();
                    $('#result').val(decodedResult.decodedText);
                    // stop
                    var audio = new Audio('/sounds/alert.wav');
                    audio.play();

                    html5QrCode.stop();
                    $('#start_cam').val("Start Cam");
                    $('#start_cam').data('id', 1);
                    $('#my_camera').empty();

                    // get code details
                    $.ajax({
                        type: 'post',
                        dataType: "JSON",
                        url: "{{ route('get-data-by-serial') }}",
                        data: {
                            '_token': '{{ csrf_token() }}'
                        },
                        complete: function(data) {
                            if (data.success) {

                            } else {
                                alert('not ok');
                            }
                        }
                    });
                };

                const s_height = $(window).height();
                const s_width = $(window).width();

                var config = null;
                if (s_width > 500) {
                    config = {
                        fps: 20,
                        qrbox: 250
                    };
                } else {
                    config = {
                        fps: 20,
                        qrbox: 140
                    };
                }

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
