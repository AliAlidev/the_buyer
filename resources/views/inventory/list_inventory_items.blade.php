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
                        <div class="card-body">
                            <div id="alertdiv" class="alert"></div>
                            <table class="table table-bordered data-table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>quantity</th>
                                        <th>quantity Parts</th>
                                        <th>price</th>
                                        <th>start_date</th>
                                        <th>expiry_date</th>
                                        <th>description</th>
                                        <th width="100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                        data: 'quantity_parts',
                        name: 'quantity_parts',
                    },
                    {
                        data: 'price',
                        name: 'price',
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
                var td = $($(this).closest("tr")).find('td:eq(5)');
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
                var td = $($(this).closest("tr")).find('td:eq(5)');
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
            $('.alert').empty();
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
                                $('.alert').append(
                                    "<div class= 'alert alert-success'>" +
                                    result
                                    .message +
                                    "</div>");
                                $('.data-table').DataTable().clear().draw();
                            } else {
                                $('.alert').show();
                                $('.alert').append(
                                    "<div class= 'alert alert-danger'>" +
                                    result
                                    .message +
                                    "</div>");
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
