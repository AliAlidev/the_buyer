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
                            <div class="row mb-5">
                                <div class="col-md-3">
                                    <label for="merchant_type">Merchant Type</label>
                                    <select name="merchant_type" id="merchant_type" class="form-select">
                                        <option value=""></option>
                                        <option value="1">Pharmacy</option>
                                        <option value="2">Market</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="shape_id">Shape</label>
                                    <select name="shape_id" id="shape_id" class="form-select">
                                        <option value=""></option>
                                        @foreach ($shapes as $shape)
                                            <option value="{{ $shape->id }}">{{ $shape->ar_shape_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="company_id">Company</label>
                                    <select name="company_id" id="company_id" class="form-select">
                                        <option value=""></option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->ar_comp_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered data-table" style="width: 150%">
                                <thead>
                                    <tr>
                                        <th style="text-align: center">No</th>
                                        <th style="text-align: center">Code</th>
                                        <th style="text-align: center">Name</th>
                                        <th style="text-align: center">Shape</th>
                                        <th style="text-align: center">Company</th>
                                        <th style="text-align: center">Has Parts</th>
                                        <th style="text-align: center">Part Number</th>
                                        <th style="text-align: center">Description</th>
                                        <th style="text-align: center">Merchant Type</th>
                                        <th style="text-align: center">Status</th>
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
        var table;
        $(function() {

            table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                scrollY: 500,
                scrollX: true,
                "pageLength": 25,
                "deferRender": true,
                "paging": true,
                "pagingType": "full_numbers",
                ajax: {
                    "url": "{{ route('product-list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.comp_id = $('#company_id').val();
                        d.shape_id = $('#shape_id').val();
                        d.merchant_type = $('#merchant_type').val();
                    }
                },
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
                        data: 'ar_shape_name',
                        name: 'ar_shape_name',
                    },
                    {
                        data: 'ar_comp_name',
                        name: 'ar_comp_name',
                    },
                    {
                        data: 'has_parts',
                        name: 'has_parts',
                        render: function(data) {
                            if (data == 0)
                                return 'No';
                            else
                                return 'Yes';
                        }
                    },
                    {
                        data: 'num_of_parts',
                        name: 'num_of_parts',
                    },
                    {
                        data: 'description',
                        name: 'description',
                    },
                    {
                        data: 'merchant_type',
                        name: 'merchant_type',
                        render: function(data) {
                            if (data == 1)
                                return 'Pharmcay';
                            else
                                return 'Market';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            if (data == 1)
                                return 'Active';
                            else if (data == 2)
                                return 'In Active';
                            else if (data == 3)
                                return 'Under Inspection';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                    },
                ],
                "lengthMenu": [
                    [25, 500, 1000, 2000, 5000, 10000],
                    [25, 500, 1000, 2000, 5000, 10000]
                ],
                "language": {
                    "searchPlaceholder": "Type and press Enter",
                    "loadingRecords": "Please wait - loading..."
                },
                "dom": 'lBfrtipr'

            });
            $("div.dataTables_filter input").unbind();
            $("div.dataTables_filter input").keyup(function(
                e) {
                if (e.keyCode == 13) {
                    table.search(this.value).draw();
                }
            });

        });

        $('#company_id').change(function() {
            table.draw();
        });

        $('#shape_id').change(function() {
            table.draw();
        });

        $('#merchant_type').change(function() {
            table.draw();
        });

        // $('body').on('click', '.show_max_part_price', function() {
        //     var currBtn = $(this);
        //     /// get max price
        //     if (currBtn.text() == "Max Part Price") {
        //         var td = $($(this).closest("tr")).find('td:eq(6)');
        //         var elementId = $(this).attr('id');
        //         var id = elementId.split("_")[0];
        // var url = "@{{ route('get-max-part-price-for-element', '#id') }}";
        //         url = url.replace('#id', id);
        //         $.ajax({
        //             url: url,
        //             success: function(newValue) {
        //                 td.html(newValue);
        //                 td.css('color', 'black');
        //                 currBtn.text("Current Part Price");
        //             }
        //         });
        //     } else {
        //         /////// get current price
        //         var td = $($(this).closest("tr")).find('td:eq(6)');
        //         var elementId = $(this).attr('id');
        //         var id = elementId.split("_")[0];
        //         var merchantId = elementId.split("_")[1];
        //         var url = "@{{ route('get-current-part-price-for-element', ['#id', '#merchantId']) }}";
        //         url = url.replace('#id', id);
        //         url = url.replace('#merchantId', merchantId);
        //         $.ajax({
        //             url: url,
        //             success: function(currValue) {
        //                 td.html(currValue);
        //                 td.css('color', 'red');
        //                 currBtn.text("Max Part Price");
        //             }
        //         });

        //     }
        // });

        // $('body').on('click', '.show_max_price', function() {
        //     var currBtn = $(this);
        //     /// get max price
        //     if (currBtn.text() == "Max Price") {
        //         var td = $($(this).closest("tr")).find('td:eq(4)');
        //         var elementId = $(this).attr('id');
        //         var id = elementId.split("_")[0];
        //         var url = "@{{ route('get-max-price-for-element', '#id') }}";
        //         url = url.replace('#id', id);
        //         $.ajax({
        //             url: url,
        //             success: function(newValue) {
        //                 td.html(newValue);
        //                 td.css('color', 'black');
        //                 currBtn.text("Current Price");
        //             }
        //         });
        //     } else {
        //         /////// get current price
        //         var td = $($(this).closest("tr")).find('td:eq(4)');
        //         var elementId = $(this).attr('id');
        //         var id = elementId.split("_")[0];
        //         var merchantId = elementId.split("_")[1];
        //         var url = "@{{ route('get-current-price-for-element', ['#id', '#merchantId']) }}";
        //         url = url.replace('#id', id);
        //         url = url.replace('#merchantId', merchantId);
        //         $.ajax({
        //             url: url,
        //             success: function(currValue) {
        //                 td.html(currValue);
        //                 td.css('color', 'red');
        //                 currBtn.text("Max Price");
        //             }
        //         });

        //     }
        // });

        // $('body').on('click', '.delete', function() {
        //     var id = $(this).attr('id');
        //     swal({
        //         title: 'Are you sure?',
        //         text: 'This record and it`s details will be permanantly deleted!',
        //         icon: 'warning',
        //         buttons: ["Cancel", "Yes!"],
        //     }).then(function(value) {
        //         if (value) {

        //             $.ajaxSetup({
        //                 headers: {
        //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                 }
        //             });

        //             // ajax
        //             $.ajax({
        //                 type: "POST",
        //                 url: "@{{ route('delete-item') }}",
        //                 data: {
        //                     id: id
        //                 },
        //                 dataType: 'json',
        //                 success: function(result) {
        //                     if (result.success) {
        //                         $('.alert-success').empty();
        //                         $('.alert-success').append(result.message);
        //                         $('.data-table').DataTable().clear().draw();
        //                     } else {
        //                         $('.alert-danger').empty();
        //                         $('.alert-danger').show();
        //                         $('.alert-danger').append(result.message);
        //                     }
        //                 },
        //                 error: function(erorr) {
        //                     console.log(erorr);
        //                 }
        //             });
        //         }
        //     });
        // });
    </script>
@endpush
