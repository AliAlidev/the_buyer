@extends('layouts.main')

@push('styles')
    <style>
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            /* add padding to account for vertical scrollbar */
            padding-right: 20px;
        }
    </style>
@endpush

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
                            <h4>Create Element</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <div id="alertdanger" class="alert alert-danger" hidden>
                                </div>
                                <div id="alertsuccess" class="alert alert-success" hidden>
                                </div>
                                <form id="form1" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <button id="start_flash" data-id="0" class="btn btn-warning">Flash
                                                    OFF</button>
                                            </div>
                                        </div>
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
                                                <div class="col-md-3">
                                                    <label class="mt-3" for="">Quantity</label>
                                                    <input id="quantity" class="form-control" type="number"
                                                        value="{{ old('quantity') != null ? old('quantity') : 0 }}"
                                                        name="quantity" required placeholder="QUANTITY">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mt-3" for="">Price</label>
                                                    <input id="price" class="form-control" type="number"
                                                        value="{{ old('price') != null ? old('price') : 0 }}"
                                                        name="price" placeholder="PRICE" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mt-3" for="">Quantity Parts</label>
                                                    <input id="quantityparts" class="form-control" type="number"
                                                        value="{{ old('quantityparts') != null ? old('quantityparts') : 0 }}"
                                                        name="quantityparts" required placeholder="QUANTITY PARTS">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mt-3" for="">Part Price</label>
                                                    <input id="partprice" class="form-control" type="number"
                                                        value="{{ old('partprice') != null ? old('partprice') : 0 }}"
                                                        name="partprice" required placeholder="PART PRICE">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label class="form-check-label mt-3" for="flexSwitchCheckDefault">Has
                                                        Parts</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="hasparts"
                                                            name="hasparts">
                                                    </div>
                                                </div>
                                                <div class="col-md-2" id="numofpartsdiv" hidden>
                                                    <label class="form-check-label mt-3"
                                                        for="flexSwitchCheckDefault">Parts Number</label>
                                                    <input id="numofparts" type="number" class="form-control"
                                                        value="{{ old('numofparts') != null ? old('numofparts') : 0 }}"
                                                        name="numofparts">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mt-3" for="">Start Date</label>
                                                    <input id="start_date" class="form-control" type="date"
                                                        value="{{ old('start_date') }}" name="start_date">
                                                </div>
                                                <div class="col-md-3">
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
                                            <button class="btn btn-primary" type="button" id="submitForm">Add</button>
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
        $('#hasparts').change(function() {
            if ($(this).is(':checked')) {
                $('#numofpartsdiv').removeAttr('hidden');
            } else {
                $('#numofpartsdiv').attr('hidden', 'hidden');
            }
        });
    </script>

    <script>
        $("#name").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('get-items-name') }}",
                    dataType: "json",
                    data: {
                        searchText: request.term
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.name
                            };
                        }));
                    }
                });
            }
        });
    </script>

    <script>
        $('#name').keyup(function(e) {
            if (e.keyCode == 13) {
                // clear old
                $('#code').val('');
                $('#quantity').val('0');
                $('#quantityparts').val('0');
                $('#price').val('0');
                $('#partprice').val('0');
                $('#expiry_date').val('');
                $('#description').val('');
                var elementName = $(this).val();
                $.ajax({
                    type: 'post',
                    dataType: "JSON",
                    url: "{{ route('get-data-by-name') }}",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        name: elementName
                    },
                    complete: function(data) {
                        $('#alertdanger').attr('hidden', true);
                        $('#alertsuccess').attr('hidden', true);
                        $('#name').removeClass(
                            'is-invalid  was-validated form-control:invalid');
                        $('#name').removeClass(
                            'is-valid  was-validated form-control:valid');
                        data = data.responseJSON;
                        if (data.success) {
                            data = data.data;
                            $('#name').addClass(
                                'is-invalid  was-validated form-control:invalid');
                        } else {
                            $('#name').addClass(
                                'is-valid  was-validated form-control:valid');
                        }
                    }
                });
            }
        });
    </script>

    <script>
        $('#getdata').click(function() {

            // clear old
            $('#code').val('');
            $('#quantity').val('0');
            $('#quantityparts').val('0');
            $('#price').val('0');
            $('#partprice').val('0');
            $('#expiry_date').val('');
            $('#description').val('');

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
                    $('#alertdanger').attr('hidden', true);
                    $('#alertsuccess').attr('hidden', true);
                    $('#result').removeClass(
                        'is-valid  was-validated form-control:valid');
                    $('#result').removeClass(
                        'is-invalid  was-validated form-control:invalid');
                    data = data.responseJSON;
                    if (data.success) {
                        data = data.data;
                        $('#result').addClass(
                            'is-invalid  was-validated form-control:invalid');
                    } else {
                        $('#result').addClass(
                            'is-valid  was-validated form-control:valid');
                    }
                }
            });
        })
    </script>

    <script>
        var html5QrCode;
        var track;

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

                            }
                        }
                    });
                };

                const s_height = $(window).height();
                const s_width = $(window).width();


                var config = null;
                if (s_width > 500) {
                    config = {
                        fps: 60,
                        qrbox: {
                            width: 250,
                            height: 100
                        },
                        experimentalFeatures: {
                            useBarCodeDetectorIfSupported: true
                        }
                    };
                } else {
                    config = {
                        fps: 60,
                        qrbox: {
                            width: 100,
                            height: 50
                        },
                        experimentalFeatures: {
                            useBarCodeDetectorIfSupported: true
                        }
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

                $('#start_flash').data('id', 0);
                $('#start_flash').text("Flash OFF");
                powerTorch(false);
            }
        }

        function powerTorch(powerOn) {
            html5QrCode.applyVideoConstraints({
                advanced: [{
                    torch: powerOn
                }]
            });
        }

        $('#start_flash').click(function(e) {
            e.preventDefault();
            if ($(this).data('id') == 0) {
                powerTorch(true);
                $(this).data('id', 1);
                $(this).text("Flash ON");
            } else {
                powerTorch(false);
                $(this).data('id', 0);
                $(this).text("Flash OFF");
            }
        });
    </script>


    <script>
        $('#submitForm').click(function(e) {
            var data = $('form').serialize();
            $.post("{{ route('create-item') }}", data).done(function(value) {
                if (value.success) {
                    $('#alertdanger').attr('hidden', true);
                    $('#alertsuccess').attr('hidden', false);
                    $('#alertsuccess').empty();
                    $('#alertsuccess').append(value.message);
                    $("#form1")[0].reset();
                    $('#result').removeClass(
                        'is-valid  was-validated form-control:valid');
                    $('#result').removeClass(
                        'is-invalid  was-validated form-control:invalid');
                    $('#name').removeClass(
                        'is-valid  was-validated form-control:valid');
                    $('#name').removeClass(
                        'is-invalid  was-validated form-control:invalid');

                    $('#numofpartsdiv').attr('hidden', 'hidden');
                }
            }).fail(function(xhr, status, error) {
                $('#alertsuccess').attr('hidden', true);
                $('#alertdanger').attr('hidden', false);
                $('#alertdanger').empty();
                $('#alertdanger').append("<ul>");
                $.each(xhr.responseJSON.errors, function(index, value) {
                    $('#alertdanger').append("<li>" + value + "</li>");
                });
                $('#alertdanger').append("</ul>");
            });
        });

        $('#form1').submit(function() {
            e.preventDefault();
        });
    </script>
@endpush
