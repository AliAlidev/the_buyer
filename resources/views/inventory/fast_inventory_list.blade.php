@extends('layouts.main')

@push('styles')
    <style>
        .dt-buttons {
            margin-left: 15%;
            margin-right: 15%;
        }

        .overlay {
            margin: auto;
            position: absolute;
            top: 50px;
            left: 50px;
            bottom: 50px;
            right: 50px;
            z-index: 100000;
        }

        .disabledDiv {
            pointer-events: none;
            opacity: 0.4;
        }

        .add_button {
            background-color: #1b82ec;
            border-color: #1b82ec;
            color: white;
        }

        .add_button:hover {
            background-color: #0080ff;
            border-color: #0080ff;
            border: solid 1px white;
        }

        .edti_button {
            background-color: #f5b225;
            border-color: #f5b225;
            color: whitesmoke;
        }

        .edti_button:hover {
            background-color: #fcad02;
            border-color: #fcad02;
            border: solid 1px white;
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
                            <h4>{{ __('inventory/inventory.labels.product_list') }}</h4>
                        </div>
                        <div class="card-body">
                            {{-- <div class="alert alert-danger" hidden></div>
                            <div class="alert alert-success" hidden></div> --}}
                            <div id="data_table" style="margin: 5%">
                                @if (Auth::user()->isAdmin())
                                    <div class="row mt-4 mb-4">
                                        <div class="col-md-3">
                                            <label
                                                for="merchant_email">{{ __('inventory/inventory.labels.merchant_name') }}</label>
                                            <select name="merchant_email" id="merchant_email" class="form-select">
                                                <option value=""></option>
                                                @foreach ($merchants as $merchant)
                                                    <option value="{{ $merchant->id }}">{{ $merchant->email }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div id="table_data">
                                    <table class="table table-bordered data-table mt-5" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; width: 10%">
                                                    {{ __('inventory/inventory.labels.table_header_id') }}</th>
                                                <th style="text-align: center; width: 15%">
                                                    {{ __('inventory/inventory.labels.code') }}</th>
                                                <th style="text-align: center; width: 20%">
                                                    {{ __('inventory/inventory.labels.name') }}</th>
                                                <th style="text-align: center; width: 10%">
                                                    {{ __('inventory/inventory.labels.quantity') }}</th>
                                                <th style="text-align: center; width: 10%">
                                                    {{ __('inventory/inventory.labels.price') }}</th>
                                                <th style="text-align: center; width: 10%">
                                                    {{ __('inventory/inventory.labels.quantity_p') }}</th>
                                                <th style="text-align: center; width: 10%">
                                                    {{ __('inventory/inventory.labels.price_p') }}</th>
                                                <th style="text-align: center; width: 10%">
                                                    {{ __('inventory/inventory.labels.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="8" style="text-align: center"><small>Please select
                                                        merchant</small></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

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
        function getCurrentLanguage() {
            var sessionLang = "{{ strtolower(session()->get('locale')) }}";
            if (sessionLang == '') {
                sessionLang = "{{ strtolower(Auth::user()->language) }}";
            }
            return sessionLang;
        }
    </script>

    <script src="{{ asset('assets/js/custome_validation.js') }}"></script>

    <script type="text/javascript">
        // $(function() {
        //     var langOptions = getCurrentLanguage();
        //     if (langOptions == 'ar')
        //         langOptions = {
        //             "searchPlaceholder": "اكتب النص ومن ثم اضغط انتر",
        //             "loadingRecords": "الرجاء الانتظار - جار التحميل...",
        //             "sProcessing": "جارٍ التحميل...",
        //             "sLengthMenu": "أظهر _MENU_ مدخلات",
        //             "sZeroRecords": "لم يعثر على أية سجلات",
        //             "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
        //             "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
        //             "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
        //             "sInfoPostFix": "",
        //             "sSearch": "ابحث:",
        //             "sUrl": "",
        //             "oPaginate": {
        //                 "sFirst": "الأول",
        //                 "sPrevious": "السابق",
        //                 "sNext": "التالي",
        //                 "sLast": "الأخير"
        //             }
        //         };

        //     var table = $('.data-table').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         scrollY: 500,
        //         scrollX: true,
        //         "pageLength": 50,
        //         "deferRender": true,
        //         "paging": true,
        //         "pagingType": "full_numbers",
        //         "autoWidth": false,
        //         ajax: {
        //             url: "{{ route('fast-initilize-store') }}",
        //             data: function(d) {
        //                 d.merchant_id = $('#merchant_email').val();
        //             }
        //         },
        //         columns: [{
        //                 data: 'DT_RowIndex',
        //                 name: 'DT_RowIndex',
        //                 orderable: false,
        //                 searchable: false
        //             },
        //             {
        //                 data: 'code',
        //                 name: 'code',
        //             },
        //             {
        //                 data: 'name',
        //                 name: 'name',
        //             },
        //             {
        //                 data: 'amount',
        //                 name: 'amount',
        //             },
        //             {
        //                 data: 'price',
        //                 name: 'price',
        //             },
        //             {
        //                 data: 'partamount',
        //                 name: 'partamount',
        //             },
        //             {
        //                 data: 'part_price',
        //                 name: 'part_price',
        //             },
        //             {
        //                 data: 'action',
        //                 name: 'action',
        //             },
        //         ],
        //         "lengthMenu": [
        //             [50, 100, 500, 1000, 2000, 5000, 10000],
        //             [50, 100, 500, 1000, 2000, 5000, 10000]
        //         ],
        //         "language": langOptions,
        //         "dom": 'lBfrtipr'
        //     });
        //     $("div.dataTables_filter input").unbind();
        //     $("div.dataTables_filter input").keyup(function(
        //         e) {
        //         if (e.keyCode == 13) {
        //             table.search(this.value).draw();
        //         }
        //     });

        // });

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

            var formData = new FormData();
            formData.append('code', code);
            formData.append('name', name.text());
            formData.append('quantity', quantity.val());
            formData.append('price', price.val());
            formData.append('quantityP', quantityP.val());
            formData.append('priceP', priceP.val());

            var btn = $(this).closest("tr").find('td:eq(7)').find('a');

            if ('{{ Auth::user()->isAdmin() }}' == true) {
                formData.append('merchant_id', $('#merchant_email').val());
            }

            // // ajax
            $.ajax({
                type: "post",
                cache: false,
                dataType: "json",
                processData: false,
                contentType: false,
                url: "{{ route('save-item-in-fast-initilize-store') }}",
                data: formData,
                success: function(result) {
                    // $('.alert-danger').attr('hidden', true);
                    // $('.alert-success').attr('hidden', true);

                    showMessage(result, 'data_table');

                    if (result.success) {
                        $(btn).text("{{ __('inventory/inventory.labels.edit') }}");
                        var inventoryAmount = $(btn).attr('inventoryAmount');
                        var eidtUrl = "{{ route('edit-item-in-fast-initilize-store', '#id') }}";
                        eidtUrl = eidtUrl.replace('#id', result.data.amount_id);
                        $(btn).attr('href', eidtUrl);
                        $(btn).addClass('edti_button');
                    }
                },
                error: function(erorr) {
                    console.log(erorr);
                }
            });
        });

        $('body').on('click', '.btn_edit', function(e) {
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

            var formData = new FormData();
            formData.append('code', code);
            formData.append('name', name.text());
            formData.append('quantity', quantity.val());
            formData.append('price', price.val());
            formData.append('quantityP', quantityP.val());
            formData.append('priceP', priceP.val());

            if ('{{ Auth::user()->isAdmin() }}' == true) {
                formData.append('merchant_id', $('#merchant_email').val());
            }
            // // ajax
            $.ajax({
                type: "post",
                cache: false,
                dataType: "json",
                processData: false,
                contentType: false,
                url: $(this).attr('href'),
                data: formData,
                success: function(result) {
                    showMessage(result, 'data_table');

                    if (result.success) {
                        // $('#load').addClass('disabledDiv');
                        // $('#load').append(
                        //     '<img class="overlay" src="{{ asset('assets/images/loading.gif') }}" />'
                        // );
                        var url = window.location.href;
                        $.ajax({
                            url: url,
                            data: {
                                'merchant_id': $('#merchant_email').val()
                            },
                            success: function(result) {
                                // $('#table_data').html(result);
                                window.history.pushState("", "", url);
                            }
                        });
                    }
                },
                error: function(erorr) {}
            });
        });

        $(document).ready(function() {

            if ('{{ Auth::user()->isMerchant() }}' == true) {
                $('#load').addClass('disabledDiv');
                $('#load').append(
                    '<img class="overlay" src="{{ asset('assets/images/loading.gif') }}" />'
                );
                var url = "{{ route('fast-initilize-store') }}";
                $.ajax({
                    url: url,
                    success: function(result) {
                        $('#table_data').empty();
                        $('#table_data').html(result);
                        window.history.pushState("", "", url);
                    }
                });
            }

            $('#merchant_email').change(function() {
                $('#load').addClass('disabledDiv');
                $('#load').append(
                    '<img class="overlay" src="{{ asset('assets/images/loading.gif') }}" />'
                );
                var url = "{{ route('fast-initilize-store') }}";
                $.ajax({
                    url: url,
                    data: {
                        'merchant_id': $('#merchant_email').val()
                    },
                    success: function(result) {
                        $('#table_data').empty();
                        $('#table_data').html(result);
                        window.history.pushState("", "", url);
                    }
                });
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
