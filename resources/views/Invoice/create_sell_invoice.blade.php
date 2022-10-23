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
                            <h4>{{ __('invoice/invoice.create.labels.create_sell_invoice') }}</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <form id="main_form" method="POST" action="{{ route('store-sell-invoice') }}" style="margin: 5%">
                                    @csrf

                                    <div class="row">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input id="start_cam" type="button"
                                                    value="{{ __('invoice/invoice.create.labels.start_cam') }}"
                                                    data-id="1" onclick="startBarcodePicker()"
                                                    class="btn btn-primary">
                                            </div>
                                            <div class="col-md-6"></div>
                                            <div class="col-md-4">
                                                <label
                                                    for="customer_name">{{ __('invoice/invoice.create.labels.customer_name') }}</label>
                                                <input type="text" id="customer_name" name="customer_name"
                                                    class="form-control">
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
                                        <input type="text" name="" value="0" id="data_id" hidden>
                                        <div class="row">
                                            <label for="result">{{ __('invoice/invoice.create.labels.code') }}</label>
                                            <div class="col-md-10">
                                                <input class="form-control" id="result" type="text"
                                                    value="{{ old('code') }}"
                                                    placeholder="{{ __('invoice/invoice.create.labels.scan_code') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <input id="getdata" onclick="getItemDetailsByCode()" type="button"
                                                    class="btn btn-primary"
                                                    value="{{ __('invoice/invoice.create.labels.check') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mt-3">
                                                <label for="name">
                                                    {{ __('invoice/invoice.create.labels.element_name') }}</label>
                                                <input id="name" value="{{ old('name') }}" type="text"
                                                    class="form-control"
                                                    placeholder="{{ __('invoice/invoice.create.labels.enter_element_name') }}">
                                            </div>
                                        </div>
                                        <label for="square-switch1"
                                            class="mt-3">{{ __('invoice/invoice.create.labels.current_amounts') }}</label>
                                        <div class="square-switch">
                                            <input type="checkbox" id="square-switch1" switch="none">
                                            <label class="form-label" for="square-switch1" data-on-label="On"
                                                data-off-label="Off"></label>
                                        </div>

                                        <div id="description" hidden>
                                            {{-- fixed values --}}
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label class="mt-3"
                                                        for="">{{ __('invoice/invoice.create.labels.quantity') }}</label>
                                                    <input id="quantity" class="form-control" type="number"
                                                        value="0" placeholder="" readonly
                                                        style="text-align: center">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3"
                                                        for="">{{ __('invoice/invoice.create.labels.price') }}</label>
                                                    <input id="price" class="form-control" type="number"
                                                        value="0" placeholder="" readonly
                                                        style="text-align: center">
                                                    <div style="text-align: center">
                                                        <small id="max_price_from_another_merchants" hidden></small>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3"
                                                        for="">{{ __('invoice/invoice.create.labels.quantity_p') }}</label>
                                                    <input id="quantityparts" class="form-control" type="number"
                                                        value="0" placeholder="" readonly
                                                        style="text-align: center">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="mt-3"
                                                        for="">{{ __('invoice/invoice.create.labels.price_p') }}</label>
                                                    <input id="partprice" class="form-control" type="number"
                                                        value="0" placeholder="" readonly
                                                        style="text-align: center">
                                                    <div style="text-align: center">
                                                        <small id="max_part_price_from_another_merchants"
                                                            hidden></small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('invoice/invoice.create.labels.expiry_date') }}</label>
                                                    <input id="expiry_date" class="form-control" type="date"
                                                        value="{{ old('expiry_date') }}" readonly
                                                        style="text-align: center">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- selected values --}}
                                        <div class="row mt-4">
                                            <div class="col-md-3">
                                                <label
                                                    class="form-label">{{ __('invoice/invoice.create.labels.amounts') }}</label>
                                                <div
                                                    class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                                                    <input id="selected_quantity" data-toggle="touchspin"
                                                        type="text" value="0" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label
                                                    class="form-label">{{ __('invoice/invoice.create.labels.quantity_parts') }}</label>
                                                <div
                                                    class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                                                    <input id="selected_quantityparts" data-toggle="touchspin"
                                                        type="text" value="0" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-4">
                                                <button id="add_table_item" class="btn btn-primary"
                                                    type="button">{{ __('invoice/invoice.create.labels.add_item') }}</button>
                                            </div>
                                        </div>

                                        <div class="row mt-5">
                                            <h4>{{ __('invoice/invoice.create.labels.items') }}</h4>
                                            <div class="panel-body table-responsive">
                                                <table id="table_data" class="table table-striped" cellspacing="0">
                                                    <thead>
                                                        <div class="tr">
                                                            <th hidden>id</th>
                                                            <th>{{ __('invoice/invoice.create.labels.code') }}</th>
                                                            <th>{{ __('invoice/invoice.create.labels.name') }}</th>
                                                            <th>{{ __('invoice/invoice.create.labels.quantity') }}</th>
                                                            <th>{{ __('invoice/invoice.create.labels.price') }}</th>
                                                            <th>{{ __('invoice/invoice.create.labels.quantity_p') }}
                                                            </th>
                                                            <th>{{ __('invoice/invoice.create.labels.price_p') }}</th>
                                                            <th>{{ __('invoice/invoice.create.labels.total') }}</th>
                                                            <th>{{ __('invoice/invoice.create.labels.action') }}</th>
                                                        </div>
                                                    </thead>
                                                    <tbody id="table_items">
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-3 mt-4" style="font-weight: 800; font-size: 200%">
                                                    {{ __('invoice/invoice.create.labels.total') }}:
                                                </div>

                                                <div class="col-md-3 mt-4"
                                                    style="font-weight: bolder; font-size: 200%">
                                                    <div id="total_price" style="font-weight: 500;">0
                                                    </div>
                                                </div>
                                            </div>

                                            <label for="invoice_options_checkbox"
                                                class="mt-5">{{ __('invoice/invoice.create.labels.invoice_options') }}</label>
                                            <div class="square-switch">
                                                <input type="checkbox" id="invoice_options_checkbox" switch="none">
                                                <label class="form-label" for="invoice_options_checkbox"
                                                    data-on-label="On" data-off-label="Off"></label>
                                            </div>
                                            <div id="invoice_options" hidden>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="" class="mt-3"
                                                            style="font-size: 110%; font-weight: 600">{{ __('invoice/invoice.create.labels.payment_type') }}</label>
                                                        <select name="payment_type" id="payment_type"
                                                            class="form-control">
                                                            <option value="1">
                                                                {{ __('invoice/invoice.create.labels.payment_type_cash') }}
                                                            </option>
                                                            <option value="2">
                                                                {{ __('invoice/invoice.create.labels.payment_type_dept') }}
                                                            </option>
                                                            <option value="3">
                                                                {{ __('invoice/invoice.create.labels.payment_type_free') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="" class="mt-3"
                                                            style="font-size: 110%; font-weight: 600">{{ __('invoice/invoice.create.labels.discount_type') }}</label>
                                                        <select name="discount_type" id="discount_type"
                                                            class="form-control">
                                                            <option value="0"></option>
                                                            <option value="2">
                                                                {{ __('invoice/invoice.create.labels.discount_type_perc') }}
                                                            </option>
                                                            <option value="1">
                                                                {{ __('invoice/invoice.create.labels.discount_type_val') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div id="paid_amount_div" class="col-md-3">
                                                        <label class="mt-3" for=""
                                                            style="font-size: 110%; font-weight: 600">{{ __('invoice/invoice.create.labels.value') }}</label>
                                                        <input id="discount" class="form-control" type="number"
                                                            value="0" name="discount" style="text-align: center"
                                                            min="0">
                                                    </div>
                                                </div>

                                                <div class="row mt-5">
                                                    <div class="col-md-12">
                                                        <label
                                                            for="notes">{{ __('invoice/invoice.create.labels.notes') }}</label>
                                                        <textarea id="notes" class="form-control" name="notes" id="" cols="30" rows="2"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 text-center pb-5">
                                                <br />
                                                <button class="btn btn-primary btn-lg"
                                                    type="submit">{{ __('invoice/invoice.create.labels.sell') }}</button>
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
    <script type="text/javascript" src="instascan.min.js"></script>
    <script src="{{ asset('assets/js/custome_validation.js') }}"></script>

    {{-- sell button --}}
    <script>
        $('#main_form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var arrItems = [];
            $("#table_data > tbody  > tr").each(function() {
                var self = $(this);
                arrItems.push({
                    data_id: self.find("td:eq(0)").text().trim(),
                    amount: self.find("td:eq(3)").text().trim(),
                    part_amount: self.find("td:eq(5)").text().trim(),
                    price: self.find("td:eq(4)").text().trim(),
                    part_price: self.find("td:eq(6)").text().trim()
                });
            });
            formData.append('data', JSON.stringify(arrItems));
            formData.append('discount', $('#discount').val());
            formData.append('invoice_type', 1);
            formData.append('notes', $('#notes').val());
            $.ajax({
                processData: false,
                contentType: false,
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                dataType:"json",
                success: function(data) {
                    showInvoiceMessage(data, 'main_form');
                    if (data.success) {
                        $("#table_data tbody").empty();
                        $("#total_price").text('0');
                        $('#notes').val('');
                        $('#main_form')[0].reset();
                        $('input').each(function(element) {
                            clearValidation($(this));
                        });
                    }
                },
                error: function(reject, status) {
                    reject = reject.responseJSON;
                    showValidationMessage(reject, $(this).attr('id'));
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
            var amount = $('#selected_quantity').val();
            var part_amount = $('#selected_quantityparts').val();

            if (dataId == 0) {
                showShortMessage('danger', ['{{ __("invoice/invoice.create.labels.you_should_select_product") }}'],
                    'table_data');
                return false;
            }

            if (already_found(dataId)) {
                showShortMessage('danger', [
                    '{{ __("invoice/invoice.create.labels.you_aleready_add_this_product_to_invoice") }}'
                ], 'table_data');
                return false;
            }

            if (amount == 0 && part_amount == 0) {
                showShortMessage('danger', [
                    '{{ __("invoice/invoice.create.labels.you_should_select_amount_or_part_amount") }}'
                ], 'table_data');
                return false;
            }

            var price = $('#price').val();
            var part_price = $('#partprice').val();
            var total = price * amount + part_amount * part_price;
            var deleteRow =
                "<button class='btn btn-danger btn-sm delete_table_row' type='button'> X </button>";

            var tableitem = "<tr><td hidden>" + dataId + "</td><td>" + code + "</td><td>" + name + "</td><td>" +
                amount + "</td><td>" + price + "</td><td>" + part_amount + "</td><td>" + part_price +
                "</td><td>" + total + "</td><td> " + deleteRow + " </td></tr>";
            $('#table_items').append(tableitem);

            $('#data_id').val('0');
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
            $('#total_price').text(total + ' ' + '{{ __('invoice/invoice.create.labels.currency') }}');
        }

        function already_found(item) {
            var result = false;
            $("#table_data  > tbody  > tr").each(function() {
                var self = $(this);
                var data_id = self.find("td:eq(0)").text().trim();
                if (item == data_id)
                    result = true;
            });
            return result;
        }
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
                $('#data_id').val('0');
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
                        data = data.responseJSON.data;
                        if (data) {
                            console.log(data);
                            var itemQuantityInCurrInvoice = getItemAmountsInInvoice(data.data.id);
                            var itemPartQuantityInCurrInvoice = getItemPartAmountsInInvoice(data
                                .data.id);
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
                            $('#partprice').val(data.prices.part_price);
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
                                $('#max_price_from_another_merchants').text(data.max_price.price);
                            } else {
                                $('#price').css('color', 'black');
                                $('#max_price_from_another_merchants').attr('hidden', true);
                                $('#max_price_from_another_merchants').text('');
                            }
                            if (data.has_greater_part_price) {
                                $('#partprice').css('color', 'red');
                                $('#max_part_price_from_another_merchants').attr('hidden', false);
                                $('#max_part_price_from_another_merchants').text(data
                                    .max_price.part_price);
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
                $('#data_id').val('0');
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
                        data = data.responseJSON.data;
                        if (data) {
                            var itemQuantityInCurrInvoice = getItemAmountsInInvoice(data.data.id);
                            var itemPartQuantityInCurrInvoice = getItemPartAmountsInInvoice(data.data.id);
                            $('#selected_quantity').val('0');
                            $('#selected_quantity').attr('max', data.amounts.amounts -
                                itemQuantityInCurrInvoice);
                            $('#selected_quantityparts').val('0');
                            $('#selected_quantityparts').attr('max', data.amounts.part_amounts -
                                itemPartQuantityInCurrInvoice);
                            $('#quantity').val(data.amounts.amounts - itemQuantityInCurrInvoice);
                            $('#quantityparts').val(data.amounts.part_amounts -
                                itemPartQuantityInCurrInvoice);

                            $('#name').val(data.data.name);
                            $('#quantity').val(data.amounts.amounts);
                            $('#quantityparts').val(data.amounts.part_amounts);
                            $('#price').val(data.prices.price);
                            $('#partprice').val(data.prices.part_price);
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
                                $('#max_price_from_another_merchants').text(data.max_price.price);
                            } else {
                                $('#price').css('color', 'black');
                                $('#max_price_from_another_merchants').attr('hidden', true);
                                $('#max_price_from_another_merchants').text('');
                            }
                            if (data.has_greater_part_price) {
                                $('#partprice').css('color', 'red');
                                $('#max_part_price_from_another_merchants').attr('hidden', false);
                                $('#max_part_price_from_another_merchants').text(data.max_price.part_price);
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

    {{-- barcode reader --}}
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

            getItemDetailsByCode();
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

    <script>
        $('#square-switch1').change(function() {
            if ($(this).is(':checked'))

                $('#description').attr('hidden', false);
            else
                $('#description').attr('hidden', true);
        })

        $('#invoice_options_checkbox').change(function() {
            if ($(this).is(':checked'))

                $('#invoice_options').attr('hidden', false);
            else
                $('#invoice_options').attr('hidden', true);
        })
    </script>
@endpush
