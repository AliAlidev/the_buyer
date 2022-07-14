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
                            <h4>Add _ {{ $element->name }} _ Amounts</h4>
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
                                        <input type="text" name="dataId" value="{{ $element->id }}" hidden>
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8 mt-3">
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
                                                <div class="col-md-4">
                                                    <label class="mt-3" for="">Start Date</label>
                                                    <input id="start_date" class="form-control" type="date"
                                                        value="{{ old('start_date') }}" name="start_date">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="mt-3" for="">Expiry Date</label>
                                                    <input id="expiry_date" class="form-control" type="date"
                                                        value="{{ old('expiry_date') }}" name="expiry_date">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 text-center">
                                            <br />
                                            <button class="btn btn-primary" type="button" id="submitForm">Add</button>
                                            <button class="btn btn-primary"
                                                onclick="window.location.href='{{ route('list-items') }}'" type="button"
                                                id="submitForm">Back</button>
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
        $('#submitForm').click(function(e) {
            var data = $('form').serialize();
            $.post("{{ route('create-inventory-item-amount') }}", data).done(function(value) {
                if (value.success) {
                    $('#alertdanger').attr('hidden', true);
                    $('#alertsuccess').attr('hidden', false);
                    $('#alertsuccess').empty();
                    $('#alertsuccess').append(value.message);
                    $("#form1")[0].reset();
                }
            }).fail(function(xhr, status, error) {
                $('#alertsuccess').attr('hidden', true);
                $('#alertdanger').attr('hidden', false);
                $('#alertdanger').empty();
                $('#alertdanger').append("<ul>");
                if (xhr.responseJSON.errors == null) {
                    $('#alertdanger').append("<li>" + xhr.responseJSON.message + "</li>");
                } else {
                    $.each(xhr.responseJSON.errors, function(index, value) {
                        $('#alertdanger').append("<li>" + value + "</li>");
                    });
                }
                $('#alertdanger').append("</ul>");
            });
        });

        $('#form1').submit(function() {
            e.preventDefault();
        });
    </script>
@endpush
