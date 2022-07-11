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
                            <h4>List Items</h4>
                        </div>
                        <div class="card-body">
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
                            <table class="table table-bordered data-table" style="width: 150%">
                                <thead>
                                    <tr>
                                        <th style="text-align: center">No</th>
                                        <th style="text-align: center">Code</th>
                                        <th style="text-align: center">Name</th>
                                        <th style="text-align: center">Quantity</th>
                                        <th style="text-align: center">Price</th>
                                        <th style="text-align: center">Quantity Parts</th>
                                        <th style="text-align: center">Part Price</th>
                                        <th style="text-align: center">Start_date</th>
                                        <th style="text-align: center">Expiry_date</th>
                                        <th style="text-align: center">Description</th>
                                        <th style="text-align: center">Action</th>
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
                ajax: "{{ route('list-items') }}",

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
                        data: 'quantity',
                        name: 'quantity',
                    },
                    {
                        data: 'price',
                        name: 'price',
                    },
                    {
                        data: 'quantity_parts',
                        name: 'quantity_parts',
                    },
                    {
                        data: 'part_price',
                        name: 'part_price',
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date',
                    },
                    {
                        data: 'description',
                        name: 'description',
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                ]
            });

        });

        $('body').on('click', '.show_max_price', function() {
            var currBtn = $(this);
            /// get max price
            if (currBtn.text() == "Max Price") {
                var td = $($(this).closest("tr")).find('td:eq(4)');
                var elementId = $(this).attr('id');
                var id = elementId.split("_")[0];
                var url = "{{ route('get-max-price-for-element', '#id') }}";
                url = url.replace('#id', id);
                $.ajax({
                    url: url,
                    success: function(newValue) {
                        td.html(newValue);
                        td.css('color', 'black');
                        currBtn.text("Current Price");
                    }
                });
            } else {
                /////// get current price
                var td = $($(this).closest("tr")).find('td:eq(4)');
                var elementId = $(this).attr('id');
                var id = elementId.split("_")[0];
                var merchantId = elementId.split("_")[1];
                var url = "{{ route('get-current-price-for-element', ['#id', '#merchantId']) }}";
                url = url.replace('#id', id);
                url = url.replace('#merchantId', merchantId);
                $.ajax({
                    url: url,
                    success: function(currValue) {
                        td.html(currValue);
                        td.css('color', 'red');
                        currBtn.text("Max Price");
                    }
                });

            }
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
