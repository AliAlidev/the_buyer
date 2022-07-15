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
                                                <div class="col-md-12">
                                                    <input class="form-control" id="result" type="text" name="code"
                                                        value="{{ old('code') }}" placeholder="SCAN CODE" readonly>
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
                                                <div class="col-md-2">
                                                    <label class="mt-3" for="">Quantity</label>
                                                    <input id="quantity" class="form-control" type="number" value="0"
                                                        name="quantity" placeholder="" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3" for="">Price</label>
                                                    <input id="price" class="form-control" type="number" value="0"
                                                        name="price" placeholder="" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3" for="">Quantity Parts</label>
                                                    <input id="quantityparts" class="form-control" type="number"
                                                        value="0" name="quantityparts" placeholder="" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3" for="">Part Price</label>
                                                    <input id="partprice" class="form-control" type="number" value="0"
                                                        name="partprice" placeholder="" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mt-3" for="">Expiry Date</label>
                                                    <input id="expiry_date" class="form-control" type="date"
                                                        value="{{ old('expiry_date') }}" name="expiry_date" readonly>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-2">
                                                    <label class="mt-3" for="">Quantity</label>
                                                    <input id="quantity" class="form-control" type="number"
                                                        value="0" name="quantity" placeholder="" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3" for="">Price</label>
                                                    <input id="price" class="form-control" type="number"
                                                        value="0" name="price" placeholder="" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3" for="">Quantity Parts</label>
                                                    <input id="quantityparts" class="form-control" type="number"
                                                        value="0" name="quantityparts" placeholder="" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3" for="">Part Price</label>
                                                    <input id="partprice" class="form-control" type="number"
                                                        value="0" name="partprice" placeholder="" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mt-3" for="">Expiry Date</label>
                                                    <input id="expiry_date" class="form-control" type="date"
                                                        value="{{ old('expiry_date') }}" name="expiry_date">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-4">
                                                    <button id="add_table_item" class="btn btn-primary"
                                                        type="button">Add Item</button>
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
                                                                <th>Code</th>
                                                                <th>Name</th>
                                                                <th>Quantity</th>
                                                                <th>Price</th>
                                                                <th>Quantity P</th>
                                                                <th>Price P</th>
                                                                <th>Total</th>
                                                                <th>Action</th>
                                                            </div>
                                                        </thead>
                                                        <tbody id="table_items">
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-2" style="font-weight: bolder; font-size: 170%">
                                                        Total
                                                    </div>
                                                    <div class="col-md-4" style="font-weight: bolder; font-size: 170%">
                                                        <div id="total_price">0</div>
                                                    </div>
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
    <script>
        $('#sell_button').click(function(e) {
            e.preventDefault();
            var arrItems = [];
            $("#table_data > tbody  > tr").each(function() {
                var self = $(this);
                arrItems.push({
                    code: self.find("td:eq(0)").text().trim(),
                    name: self.find("td:eq(1)").text().trim(),
                    quantity: self.find("td:eq(2)").text().trim(),
                    price: self.find("td:eq(3)").text().trim(),
                    quantityP: self.find("td:eq(4)").text().trim(),
                    priceP: self.find("td:eq(5)").text().trim(),
                });
            });
            $.ajax({
                url: "{{ route('store-buy-invoice') }}",
                type: "post",
                dataType: 'JSON',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "items": JSON.stringify(arrItems)
                },
                success: function(data) {
                    console.log(data);
                }
            });
        });
    </script>

    <script>
        $('#add_table_item').click(function(e) {
            e.preventDefault();
            var code = $('#result').val();
            var name = $('#name').val();
            var quantity = $('#quantity').val();
            var price = $('#price').val();
            var quantityP = $('#quantityparts').val();
            var priceP = $('#partprice').val();
            var total = price * quantity + quantityP * priceP;
            var deleteRow =
                "<button class='btn btn-danger btn-sm delete_table_row' type='button'> X </button>";

            var tableitem = "<tr><td>" + code + "</td><td>" + name + "</td><td>" + quantity +
                "</td><td>" + price + "</td><td>" + quantityP + "</td><td>" + priceP +
                "</td><td>" + total + "</td><td> " + deleteRow + " </td></tr>";
            $('#table_items').append(tableitem);

            $('#code').val('');
            $('#quantity').val('0');
            $('#quantityparts').val('0');
            $('#price').val('0');
            $('#partprice').val('0');
            $('#expiry_date').val('');

            calcTotalPrice();
        });

        function calcTotalPrice() {
            var total = 0;
            $("#table_data tr").each(function() {
                var self = $(this);
                var quantity = self.find("td:eq(2)").text().trim();
                var price = self.find("td:eq(3)").text().trim();
                var quantityP = self.find("td:eq(4)").text().trim();
                var priceP = self.find("td:eq(5)").text().trim();
                total += quantity * price + quantityP * priceP;
            });
            $('#total_price').text(total + ' ' + 'sp');
        }
    </script>

    <script>
        $('body').on('click', '.delete_table_row', function(event) {
            event.preventDefault();
            $($(this).closest("tr")).remove();
            calcTotalPrice();
        });
    </script>

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
            }
        });
    </script>

    <script>
        $('#name').keyup(function(e) {
            e.preventDefault();
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
                    url: "{{ route('buy-get-data-by-name') }}",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        name: elementName
                    },
                    complete: function(data) {
                        $('#price').css('color', 'balck');
                        $('#expiry_date').css('color', 'black');
                        $('#alertdanger').attr('hidden', true);
                        $('#alertsuccess').attr('hidden', true);
                        $('#name').removeClass(
                            'is-invalid  was-validated form-control:invalid');
                        $('#name').removeClass(
                            'is-valid  was-validated form-control:valid');
                        data = data.responseJSON;
                        if (data.success) {
                            $('#quantity').val(data.amounts.amounts);
                            $('#quantityparts').val(data.amounts.part_amounts);
                            $('#price').val(data.prices.price);
                            $('#partprice').val(data.prices.partprice);
                            $('#expiry_date').val(data.expiry_date);
                            if (data.hasMultipleExpiryDate) {
                                $('#expiry_date').css('color', 'red');
                            }
                            if (data.has_greater_price) {
                                $('#price').css('color', 'red');
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
                    } else {
                        $('#name').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#quantity').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#price').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
                        $('#expiry_date').addClass(
                            'form-control is-invalid  was-validated form-control:invalid');
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

    <script></script>
@endpush
