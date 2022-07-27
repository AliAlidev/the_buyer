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

        .table-responsive {
            max-height: 300px;
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
                            <h4>Create Sell Invoice</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <form>
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
                                                    <input id="getdata" onclick="getItemDetailsByCode()" type="button" class="btn btn-primary"
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

                                        <div class="row mt-5">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-8">
                                                <h4>Items</h4>
                                                <div class="panel-body table-responsive">
                                                    <table id="table_data" class="table table-striped" cellspacing="0">
                                                        <thead>
                                                            <div class="tr">
                                                                <th hidden>id</th>
                                                                <th>Code</th>
                                                                <th>Name</th>
                                                                <th>Quantity</th>
                                                                <th>Price</th>
                                                                <th>QuantityP</th>
                                                                <th>PriceP</th>
                                                                <th>Total</th>
                                                                <th>Action</th>
                                                            </div>
                                                        </thead>
                                                        <tbody id="table_items">
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-3 mt-4" style="font-weight: 800; font-size: 200%">
                                                        Total:
                                                    </div>
                                                    <div class="col-md-3 mt-4"
                                                        style="font-weight: bolder; font-size: 200%">
                                                        <div id="total_price" style="font-weight: 500;">0
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="" class="mt-3"
                                                            style="font-size: 110%; font-weight: 600">Discount Type</label>
                                                        <select name="" id="discount_type" class="form-control">
                                                            <option value="perc">Perc</option>
                                                            <option value="val">Val</option>
                                                        </select>
                                                    </div>
                                                    <div id="discount_value_div" class="col-md-3">
                                                        <label class="mt-3" for=""
                                                            style="font-size: 110%; font-weight: 600">Percentage%</label>
                                                        <input id="discount" class="form-control" type="number"
                                                            value="0" name="discount" style="text-align: center"
                                                            step="0.1" min="0" max="100">
                                                    </div>
                                                    <div id="paid_amount_div" class="col-md-3" hidden>
                                                        <label class="mt-3" for=""
                                                            style="font-size: 110%; font-weight: 600">Paid Amount</label>
                                                        <input id="paid_amount" class="form-control" type="number"
                                                            value="0" name="paid_amount" style="text-align: center"
                                                            min="0">
                                                    </div>
                                                </div>
                                                <div class="row mt-5">
                                                    <label for="notes">Notes</label>
                                                    <textarea id="notes" class="form-control" name="notes" id="" cols="30" rows="2"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 text-center pb-5">
                                            <br />
                                            <button id="sell_button" class="btn btn-primary btn-lg"
                                                type="button">Sell</button>
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
    {{-- sell button --}}
    <script>
        $('#sell_button').click(function(e) {
            e.preventDefault();
            var arrItems = [];
            $("#table_data > tbody  > tr").each(function() {
                var self = $(this);
                arrItems.push({
                    data_id: self.find("td:eq(0)").text().trim(),
                    code: self.find("td:eq(1)").text().trim(),
                    name: self.find("td:eq(2)").text().trim(),
                    quantity: self.find("td:eq(3)").text().trim(),
                    price: self.find("td:eq(4)").text().trim(),
                    quantityP: self.find("td:eq(5)").text().trim(),
                    priceP: self.find("td:eq(6)").text().trim()
                });
            });
            $.ajax({
                url: "{{ route('store-sell-invoice') }}",
                type: "post",
                dataType: 'JSON',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "items": JSON.stringify(arrItems),
                    "total_invoice_value": $('#total_price').text(),
                    "paid_amount": $('#paid_amount').val(),
                    "discount": $('#discount').val(),
                    "invoice_type": 1,
                    "notes": $('#notes').val()
                },
                success: function(data) {
                    if (data.success) {
                        $("#table_data tbody").empty();
                        $("#total_price").text('0');
                        $('#notes').val('');
                    }
                }
            });
        });
    </script>

    {{-- add item to invoice table --}}
    <script>
        $('#add_table_item').click(function(e) {
            e.preventDefault();

            var dataId = $('#data_id').val();
            var code = $('#result').val();
            var name = $('#name').val();
            var quantity = $('#selected_quantity').val();
            var quantityP = $('#selected_quantityparts').val();
            if (quantity != 0 || quantityP != 0) {
                var price = $('#price').val();
                var priceP = $('#partprice').val();
                var total = price * quantity + quantityP * priceP;
                var deleteRow =
                    "<button class='btn btn-danger btn-sm delete_table_row' type='button'> X </button>";

                var tableitem = "<tr><td hidden>" + dataId + "</td><td>" + code + "</td><td>" + name + "</td><td>" +
                    quantity + "</td><td>" + price + "</td><td>" + quantityP + "</td><td>" + priceP +
                    "</td><td>" + total + "</td><td> " + deleteRow + " </td></tr>";
                $('#table_items').append(tableitem);

                $('#data_id').val('');
                $('#result').val('');
                $('#name').val('');
                $('#quantity').val('0');
                $('#selected_quantity').val('0');
                $('#selected_quantity').attr('max', 0);
                $('#quantityparts').val('0');
                $('#selected_quantityparts').val('0');
                $('#selected_quantityparts').attr('max', 0);
                $('#price').val('0');
                $('#price').css('color', 'black');
                $('#partprice').val('0');
                $('#partprice').css('color', 'black');
                $('#expiry_date').val('');
                $('#max_price_from_another_merchants').attr('hidden', true);
                $('#max_price_from_another_merchants').text('');
                $('#max_part_price_from_another_merchants').attr('hidden', true);
                $('#max_part_price_from_another_merchants').text('');

                calcTotalPrice();


            }
        });

        function calcTotalPrice() {
            var total = 0;
            $("#table_data tr").each(function() {
                var self = $(this);
                var quantity = self.find("td:eq(3)").text().trim();
                var price = self.find("td:eq(4)").text().trim();
                var quantityP = self.find("td:eq(5)").text().trim();
                var priceP = self.find("td:eq(6)").text().trim();
                total += quantity * price + quantityP * priceP;
            });
            $('#total_price').text(total + ' ' + 'sp');
        }
    </script>

    {{-- select discount type --}}
    <script>
        $('#discount_type').change(function() {
            if ($(this).val() == "perc") {
                $('#paid_amount_div').attr('hidden', true);
                $('#discount_value_div').attr('hidden', false);
            } else {
                $('#paid_amount_div').attr('hidden', false);
                $('#discount_value_div').attr('hidden', true);
            }
        });
    </script>

    {{-- get items count and parts count inside current invoice --}}
    <script>
        function getItemAmountsInInvoice(itemId) {
            var total = 0;
            $("#table_data tr").each(function() {
                var self = $(this);
                var item_id = self.find("td:eq(0)").text().trim();
                if (item_id == itemId) {
                    var quantity = parseInt(self.find("td:eq(3)").text().trim());
                    total = total + quantity;
                }
            });
            return total;
        }

        function getItemPartAmountsInInvoice(itemId) {
            var total = 0;
            $("#table_data tr").each(function() {
                var self = $(this);
                var item_id = self.find("td:eq(0)").text().trim();
                if (item_id == itemId) {
                    var quantityP = parseInt(self.find("td:eq(5)").text().trim());
                    total = total + quantityP;
                }
            });
            return total;
        }
    </script>

    {{-- delete row from invoice table --}}
    <script>
        $('body').on('click', '.delete_table_row', function(event) {
            event.preventDefault();
            $($(this).closest("tr")).remove();
            calcTotalPrice();
        });
    </script>

    {{-- auto complete item name --}}
    <script>
        $("#name").autocomplete({
            maxShowItems: 5,
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
            },
            select: function(event, ui) {
                $('#data_id').val('');
                $('#result').val('');
                $('#quantity').val('0');
                $('#quantityparts').val('0');
                $('#price').val('0');
                $('#partprice').val('0');
                $('#expiry_date').val('');
                $('#description').val('');
                var elementName = ui.item.value;
                $.ajax({
                    type: 'post',
                    dataType: "JSON",
                    url: "{{ route('sell-get-data-by-name') }}",
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
                            var itemQuantityInCurrInvoice = getItemAmountsInInvoice(data.data.id);
                            var itemPartQuantityInCurrInvoice = getItemPartAmountsInInvoice(data
                                .data
                                .id);

                            $('#selected_quantity').val('0');
                            $('#selected_quantity').attr('max', data.amounts.amounts -
                                itemQuantityInCurrInvoice);
                            $('#selected_quantityparts').val('0');
                            $('#selected_quantityparts').attr('max', data.amounts.part_amounts -
                                itemPartQuantityInCurrInvoice);
                            $('#quantity').val(data.amounts.amounts - itemQuantityInCurrInvoice);
                            $('#quantityparts').val(data.amounts.part_amounts -
                                itemPartQuantityInCurrInvoice);
                            $('#price').val(data.prices.price);
                            $('#partprice').val(data.prices.partprice);
                            $('#expiry_date').val(data.expiry_date);
                            $('#data_id').val(data.data.id);
                            $('#result').val(data.data.code);
                            if (data.hasMultipleExpiryDate) {
                                $('#expiry_date').css('color', 'red');
                            } else {
                                $('#expiry_date').css('color', 'black');
                            }
                            if (data.has_greater_price) {
                                $('#price').css('color', 'red');
                                $('#max_price_from_another_merchants').attr('hidden', false);
                                $('#max_price_from_another_merchants').text(data.max_price);
                            } else {
                                $('#price').css('color', 'black');
                                $('#max_price_from_another_merchants').attr('hidden', true);
                                $('#max_price_from_another_merchants').text('');
                            }
                            if (data.has_greater_part_price) {
                                $('#partprice').css('color', 'red');
                                $('#max_part_price_from_another_merchants').attr('hidden', false);
                                $('#max_part_price_from_another_merchants').text(data
                                    .max_part_price);
                            } else {
                                $('#partprice').css('color', 'black');
                                $('#max_part_price_from_another_merchants').attr('hidden', true);
                                $('#max_part_price_from_another_merchants').text('');
                            }
                            $('#name').addClass(
                                'is-valid  was-validated form-control:valid');
                        } else {
                            $('#name').addClass(
                                'is-invalid  was-validated form-control:invalid');
                        }
                    }
                });
            }
        });
    </script>

    {{-- get element details from code --}}
    <script>
        function getItemDetailsByCode() {
            if ($('#result').val() != '') {
                // clear old
                $('#data_id').val('');
                $('#name').val('');
                $('#quantity').val('0');
                $('#quantityparts').val('0');
                $('#price').val('0');
                $('#partprice').val('0');
                $('#expiry_date').val('');
                $('#description').val('');
                var serialCode = $('#result').val();
                $.ajax({
                    type: 'post',
                    dataType: "JSON",
                    url: "{{ route('sell-get-data-by-code') }}",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        code: serialCode
                    },
                    complete: function(data) {
                        $('#alertdanger').attr('hidden', true);
                        $('#alertsuccess').attr('hidden', true);
                        $('#result').removeClass(
                            'is-invalid  was-validated form-control:invalid');
                        $('#result').removeClass(
                            'is-valid  was-validated form-control:valid');
                        data = data.responseJSON;
                        if (data.success) {
                            $('#selected_quantity').val('0');
                            $('#selected_quantity').attr('max', data.amounts.amounts);
                            $('#selected_quantityparts').val('0');
                            $('#selected_quantityparts').attr('max', data.amounts.part_amounts);
                            $('#name').val(data.data.name);
                            $('#quantity').val(data.amounts.amounts);
                            $('#quantityparts').val(data.amounts.part_amounts);
                            $('#price').val(data.prices.price);
                            $('#partprice').val(data.prices.partprice);
                            $('#expiry_date').val(data.expiry_date);
                            $('#data_id').val(data.data.id);
                            if (data.hasMultipleExpiryDate) {
                                $('#expiry_date').css('color', 'red');
                            } else {
                                $('#expiry_date').css('color', 'black');
                            }
                            if (data.has_greater_price) {
                                $('#price').css('color', 'red');
                                $('#max_price_from_another_merchants').attr('hidden', false);
                                $('#max_price_from_another_merchants').text(data.max_price);
                            } else {
                                $('#price').css('color', 'black');
                                $('#max_price_from_another_merchants').attr('hidden', true);
                                $('#max_price_from_another_merchants').text('');
                            }
                            if (data.has_greater_part_price) {
                                $('#partprice').css('color', 'red');
                                $('#max_part_price_from_another_merchants').attr('hidden', false);
                                $('#max_part_price_from_another_merchants').text(data.max_part_price);
                            } else {
                                $('#partprice').css('color', 'black');
                                $('#max_part_price_from_another_merchants').attr('hidden', true);
                                $('#max_part_price_from_another_merchants').text('');
                            }
                            $('#result').addClass(
                                'is-valid  was-validated form-control:valid');
                        } else {
                            $('#result').addClass(
                                'is-invalid  was-validated form-control:invalid');
                        }
                    }
                });
            } else {
                $('#name').val('');
                $('#quantity').val('0');
                $('#quantityparts').val('0');
                $('#price').val('0');
                $('#partprice').val('0');
                $('#expiry_date').val('');
                $('#description').val('');

                $('#price').css('color', 'black');
                $('#max_price_from_another_merchants').attr('hidden', true);
                $('#max_price_from_another_merchants').text('');

                $('#partprice').css('color', 'black');
                $('#max_part_price_from_another_merchants').attr('hidden', true);
                $('#max_part_price_from_another_merchants').text('');
            }
        }
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
