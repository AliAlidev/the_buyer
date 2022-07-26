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
                            <h4>Fast Inventory List</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger" hidden></div>
                            <div class="alert alert-success" hidden></div>
                            <table class="table table-bordered data-table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; width: 10%">No</th>
                                        <th style="text-align: center; width: 15%">Code</th>
                                        <th style="text-align: center; width: 20%">Name</th>
                                        <th style="text-align: center; width: 10%">Quantity</th>
                                        <th style="text-align: center; width: 10%">Price</th>
                                        <th style="text-align: center; width: 10%">QuantityP</th>
                                        <th style="text-align: center; width: 10%">PartP</th>
                                        <th style="text-align: center; width: 10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody style="text-align: center">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page content-->

    </div> <!-- container-fluid -->
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                "deferRender": true,
                scrollY: 500,
                scrollX: true,
                ajax: "{{ route('fast-inventory-list') }}",

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                    },
                    {
                        data: 'price',
                        name: 'price',
                    },
                    {
                        data: 'partamount',
                        name: 'partamount',
                    },
                    {
                        data: 'part_price',
                        name: 'part_price',
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                ]
            });

        });

        $('body').on('click', '.btn_add', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var code = $(this).closest("tr").find('td:eq(1)').text();
            var name = $(this).closest("tr").find('td:eq(2)');
            var quantity = $(this).closest("tr").find('td:eq(3)').find('input');
            var price = $(this).closest("tr").find('td:eq(4)').find('input');
            var quantityP = $(this).closest("tr").find('td:eq(5)').find('input');
            var priceP = $(this).closest("tr").find('td:eq(6)').find('input');

            // // ajax
            $.ajax({
                type: "POST",
                url: "{{ route('store-fast-inventory-list') }}",
                data: {
                    code: code,
                    name: name.text(),
                    quantity: quantity.val(),
                    price: price.val(),
                    quantityP: quantityP.val(),
                    priceP: priceP.val()
                },
                dataType: 'json',
                success: function(result) {
                    $('.alert-danger').attr('hidden', true);
                    $('.alert-success').attr('hidden', true);

                    if (result.success) {
                        name.css('color', 'red');
                        quantity.val(0);
                        price.val(0);
                        quantityP.val(0);
                        priceP.val(0);

                        $('.alert-success').empty();
                        $('.alert-success').attr('hidden', false);
                        $('.alert-success').append(result.message);
                        setTimeout(function() {
                            $(".alert-success").attr('hidden', true);
                        }, 3000);
                    } else {
                        $('.alert-danger').empty();
                        $('.alert-danger').attr('hidden', false);
                        $('.alert-danger').append(result.message);
                        setTimeout(function() {
                            $(".alert-danger").attr('hidden', true);
                        }, 3000);
                    }
                },
                error: function(erorr) {
                    console.log(erorr);
                }
            });
        });

        $('body').on('click', '.delete', function() {
            var id = $(this).attr('id');
            swal({
                title: 'Are you sure?',
                text: 'This record and it`s details will be permanantly deleted!',
                icon: 'warning',
                buttons: ["Cancel", "Yes!"],
            }).then(function(value) {
                if (value) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    // ajax
                    $.ajax({
                        type: "POST",
                        url: "{{ route('delete-item') }}",
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                $('.alert-success').empty();
                                $('.alert-success').append(result.message);
                                $('.data-table').DataTable().clear().draw();
                            } else {
                                $('.alert-danger').empty();
                                $('.alert-danger').show();
                                $('.alert-danger').append(result.message);
                            }
                        },
                        error: function(erorr) {
                            console.log(erorr);
                        }
                    });
                }
            });
        });
    </script>
@endpush
