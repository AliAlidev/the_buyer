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
                            <div class="row">
                                <div class="col-md-5">
                                    <h4>List _ {{ $data->name }} _ Amounts</h4>
                                </div>
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-primary"
                                        onclick="window.location.href='{{ route('create-inventory-item-amount-index', $data->id) }}'">
                                        Add
                                    </button>
                                </div>
                            </div>
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
                            <div class="col-md-12">
                                <table class="table table-bordered data-table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center">No</th>
                                            <th style="text-align: center">Code</th>
                                            <th style="text-align: center">Name</th>
                                            <th style="text-align: center">Quantity</th>
                                            <th style="text-align: center">Price</th>
                                            <th style="text-align: center">Quantity Parts</th>
                                            <th style="text-align: center">Part Price</th>
                                            <th style="text-align: center">Start Date</th>
                                            <th style="text-align: center">Expiry Date</th>
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
        </div>
        <!-- end page content-->

    </div> <!-- container-fluid -->
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function() {
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            var dataId = urlParams.get('dataId');
            var merchantId = urlParams.get('merchId');
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                "deferRender": true,
                scrollY: 500,
                scrollX: true,
                "ajax": {
                    url: "{{ route('list-inventory-item-amounts') }}",
                    type: "get",
                    data: {
                        dataId: dataId,
                        merchantId: merchantId,
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
                        data: 'price_part',
                        name: 'price_part',
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
                        data: 'action',
                        name: 'action',
                    },
                ]
            });

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
                        url: "{{ route('delete-item-amount') }}",
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
