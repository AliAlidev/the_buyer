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

<script src="https://cdn.jsdelivr.net/npm/scandit-sdk@5.x"></script>
<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet" />
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
                            <h4>Edit Element: {{ $element->name }}</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <form method="POST" action="{{ route('edit-item', $element->id) }}">
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
                                                        onclick="startBarcodePicker()" class="btn btn-primary">
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-md-2"></div>
                                                <div id="barcode-result" class="result-text">&nbsp;</div>
                                                <div class="col-md-8">
                                                    <scandit-barcode-picker id="barcode-picker" class="scanner"
                                                        style="width: 100%; height: 80%;"
                                                        configure.licenseKey="{{ config('services.bar_code_key') }}"
                                                        configure.engineLocation="https://cdn.jsdelivr.net/npm/scandit-sdk@5.x/build/"
                                                        accessCamera="false" visible="false" playSoundOnScan="true"
                                                        vibrateOnScan="true",
                                                        scanSettings.enabledSymbologies='["ean8", "ean13", "upca", "upce"]'>
                                                    </scandit-barcode-picker>
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
                                                        value="{{ old('price') != null ? old('price') : 0 }}" name="price"
                                                        placeholder="PRICE" required>
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
                                            <div class="row">
                                                <div class="col-md-5"></div>
                                                <div class="col-md-1">
                                                    <button class="btn btn-primary">Update</button>
                                                </div>
                                                <div class="col-md-1">
                                                    <a href="{{ route('list-items') }}" class="btn btn-primary"
                                                        onclick="">Back</a>
                                                </div>
                                                <div class="col-md-5"></div>
                                            </div>
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
        $('#getdata').click(function() {
            // clear old
            $('#name').val('');
            $('#quantity').val('');
            $('#price').val('');
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
                    data = data.responseJSON;
                    if (data.success) {
                        data = data.data;
                        $('#name').val(data.name);
                        $('#name').removeClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#name').addClass('form-control is-valid  was-validated form-control:valid');
                        $('#quantity').val(data.quantity);
                        $('#quantity').removeClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#quantity').addClass(
                            'form-control is-valid  was-validated form-control:valid');
                        $('#price').val(data.price);
                        $('#price').removeClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#price').addClass('form-control is-valid  was-validated form-control:valid');
                        $('#expiry_date').val(data.expiry_date);
                        $('#expiry_date').removeClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#expiry_date').addClass(
                            'form-control is-valid  was-validated form-control:valid');
                        $('#description').text(data.description);
                        $('#description').removeClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#description').addClass(
                            'form-control is-valid  was-validated form-control:valid');
                    } else {
                        $('#name').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#quantity').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#price').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#expiry_date').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#description').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                    }
                }
            });
        })
    </script>

    <script>
        var barcodePickerElement = document.getElementById("barcode-picker");

        barcodePickerElement.addEventListener("scan", (scanResult) => {
            $('scandit-barcode-picker').attr('accesscamera', false);
            $('scandit-barcode-picker').attr('scanningpaused', true);
            $('scandit-barcode-picker').attr('hidden', true);

            $('#start_cam').val("Start Cam");
            $('#start_cam').data('id', 1);

            const barcode = scanResult.detail.barcodes[0];
            const symbology = ScanditSDK.Barcode.Symbology.toHumanizedName(barcode.symbology);

            $('#result').empty();
            $('#result').val(barcode.data);

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
        });

        function startBarcodePicker() {

            var curr_status = $('#start_cam').data('id');
            if (curr_status == 1) {
                $('#result').val('');

                $('scandit-barcode-picker').attr('accesscamera', true);
                $('scandit-barcode-picker').attr('scanningpaused', false);
                $('scandit-barcode-picker').attr('hidden', false);

                barcodePickerElement.addEventListener("ready", () => {
                    document.getElementById("lib-loading").hidden = true;
                    document.getElementById("barcode-picker-starter-button").hidden = false;
                });

                barcodePickerElement.barcodePicker.accessCamera().then(() => {
                    barcodePickerElement.barcodePicker.setVisible(true).resumeScanning();
                });

                $('#start_cam').val("Stop Cam");
                $('#start_cam').data('id', 2);

            } else if (curr_status == 2) {

                $('scandit-barcode-picker').attr('accesscamera', false);
                $('scandit-barcode-picker').attr('scanningpaused', true);
                $('scandit-barcode-picker').attr('hidden', true);

                $('#start_cam').val("Start Cam");
                $('#start_cam').data('id', 1);
            }
        }
    </script>
@endpush
