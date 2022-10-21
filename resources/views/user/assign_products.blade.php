@extends('layouts.main')

@push('styles')
    <style>
        label {
            font-size: 16px;
            font-weight: 900;
        }

        .dt-buttons {
            margin-right: 15%;
        }

        .button {
            --bs-blue: #1b82ec;
            --bs-indigo: #4a5fc6;
            --bs-purple: #6f42c1;
            --bs-red: #f16c69;
            --bs-orange: #f1734f;
            --bs-yellow: #f5b225;
            --bs-green: #35a989;
            --bs-teal: #2a3142;
            --bs-cyan: #29bbe3;
            --bs-white: #fff;
            --bs-gray: #74788d;
            --bs-gray-dark: #343a40;
            --bs-primary: #1b82ec;
            --bs-secondary: #495057;
            --bs-success: #35a989;
            --bs-info: #29bbe3;
            --bs-warning: #f5b225;
            --bs-danger: #f16c69;
            --bs-pink: #e83e8c;
            --bs-light: #eff2f7;
            --bs-dark: #343a40;
            --bs-font-sans-serif: "Roboto", sans-serif;
            --bs-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            -webkit-box-direction: normal;
            word-wrap: break-word;
            --bs-gutter-x: 1.875rem;
            --bs-gutter-y: 0;
            box-sizing: border-box;
            margin: 0;
            font-family: inherit;
            text-transform: none;
            outline: none !important;
            -webkit-appearance: button;
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            text-align: center;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 0.84375rem;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
            color: #fff;
            background-color: #495057;
            border-color: #495057;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1), 0 2px 5px rgba(0, 0, 0, 0.15);
            cursor: pointer;
        }
    </style>

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
                            <h4>{{ __('user/assign_products.title') }}</h4>
                        </div>
                        <div class="card-body" style="margin: 5%">
                            @if ($errors->any())
                                <div class="alert alert-danger" id="danger_div">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div id="alertdanger" class="alert alert-danger" hidden>
                            </div>
                            <div id="alertsuccess" class="alert alert-success" hidden>
                            </div>

                            <div class="row mb-5">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="merchant_type">{{ __('user/assign_products.product_type') }}</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="merchant_type">{{ __('user/assign_products.merchant_email') }}</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="assign_type" id="assign_type" class="form-select">
                                            <option value=""></option>
                                            <option value="1">{{ __('user/assign_products.product_type_all') }}
                                            </option>
                                            <option value="2">{{ __('user/assign_products.product_type_pharmacy') }}
                                            </option>
                                            <option value="3">{{ __('user/assign_products.product_type_market') }}
                                            </option>
                                            <option value="4">{{ __('user/assign_products.product_type_custome') }}
                                            </option>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="merchant_email" id="merchant_email" class="form-select">
                                            <option value=""></option>
                                            @foreach ($merchants as $merchant)
                                                <option value="{{ $merchant->id }}">{{ $merchant->email }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div id="table_buttons">
                                            <button id="assign_data" class="button"><span class="fa fa-plus-circle"
                                                    aria-hidden="true"></span> <span
                                                    class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true" hidden></span> Assign</button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="custome_data_section" hidden>
                                <h4 class="mt-5 mb-2">{{ __('user/assign_products.filter_products') }}</h4>

                                <div class="row mb-5">
                                    <div class="col-md-3">
                                        <label for="merchant_type">{{ __('product/list_products.merchant_type') }}</label>
                                        <select name="merchant_type" id="merchant_type" class="form-select">
                                            <option value=""></option>
                                            <option value="1">{{ __('product/list_products.merchant_type_pharmacy') }}
                                            </option>
                                            <option value="2">{{ __('product/list_products.merchant_type_market') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="shape_id">{{ __('product/list_products.shape') }}</label>
                                        <select name="shape_id" id="shape_id" class="form-select">
                                            <option value=""></option>
                                            @foreach ($shapes as $shape)
                                                <option value="{{ $shape->id }}">{{ $shape->ar_shape_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="company_id">{{ __('product/list_products.company') }}</label>
                                        <select name="company_id" id="company_id" class="form-select">
                                            <option value=""></option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->ar_comp_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <table id="main_table" class="table table-bordered data-table" style="width: 150%">
                                    <thead style="background-color: #1b82ec; color: white">
                                        <tr>
                                            <th style="text-align: center;">
                                                {{ __('product/list_products.table_header_id') }}
                                            </th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_code') }}
                                            </th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_name') }}
                                            </th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_shape') }}
                                            </th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_comp') }}
                                            </th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_has_parts') }}</th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_parts_number') }}</th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_description') }}</th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_merchant_type') }}</th>
                                            <th style="text-align: center">
                                                {{ __('product/list_products.table_header_status') }}</th>
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
    <script>
        var table;
        $('#assign_type').change(function() {
            if ($(this).val() == 4) {
                if (table != null)
                    table.destroy();
                $('#custome_data_section').removeAttr('hidden');
                var langOptions = getCurrentLanguage();
                if (langOptions == 'ar')
                    langOptions = {
                        "searchPlaceholder": "اكتب النص ومن ثم اضغط انتر",
                        "loadingRecords": "الرجاء الانتظار - جار التحميل...",
                        "sProcessing": "جارٍ التحميل...",
                        "sLengthMenu": "أظهر _MENU_ مدخلات",
                        "sZeroRecords": "لم يعثر على أية سجلات",
                        "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                        "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                        "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                        "sInfoPostFix": "",
                        "sSearch": "ابحث:",
                        "sUrl": "",
                        "oPaginate": {
                            "sFirst": "الأول",
                            "sPrevious": "السابق",
                            "sNext": "التالي",
                            "sLast": "الأخير"
                        }
                    };
                table = $('#main_table').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollY: 500,
                    scrollX: true,
                    "pageLength": 50,
                    "deferRender": true,
                    "paging": true,
                    "pagingType": "full_numbers",
                    "autoWidth": false,
                    ajax: {
                        "url": "{{ route('products-assign') }}",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d) {
                            d._token = "{{ csrf_token() }}";
                            d.comp_id = $('#company_id').val();
                            d.shape_id = $('#shape_id').val();
                            d.merchant_type = $('#merchant_type').val();
                            d.merchant_id = $('#merchant_email').val();
                        }
                    },
                    "buttons": [
                        'selectAll',
                        'selectNone',
                        'excel',
                        {
                            text: '<span class="fa fa-plus-circle" aria-hidden="true"></span> <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" hidden></span>Assign',
                            attr: {
                                id: 'assign_data'
                            }
                        }
                    ],
                    "dom": 'Blftipr',
                    columnDefs: [{
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    }],
                    select: {
                        style: 'multi'
                    },
                    columns: [{
                            data: 'check',
                            name: 'check',
                            orderable: false,
                            searchable: false,
                            "width": "5%"
                        },
                        {
                            data: 'code',
                            name: 'code',
                            orderable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false
                        },
                        {
                            data: 'ar_shape_name',
                            name: 'ar_shape_name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'ar_comp_name',
                            name: 'ar_comp_name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'has_parts',
                            name: 'has_parts',
                            orderable: false,
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
                            orderable: false
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false
                        },
                        {
                            data: 'merchant_type',
                            name: 'merchant_type',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                if (data == 1)
                                    return 'Active';
                                else if (data == 2)
                                    return 'In Active';
                                else if (data == 3)
                                    return 'Under Inspection';
                            }
                        }
                    ],
                    "lengthMenu": [
                        [50, 100, 500, 1000, 2000, 5000, 10000],
                        [50, 100, 500, 1000, 2000, 5000, 10000]
                    ],
                    "language": langOptions,
                    initComplete: (settings, json) => {
                        $('#table_buttons').empty();
                        $('#assign_data').appendTo('#table_buttons');
                        $('#assign_data').bind('click', function() {
                            assignFunction();
                        });
                    }

                });
                $("div.dataTables_filter input").unbind();
                $("div.dataTables_filter input").keyup(function(
                    e) {
                    if (e.keyCode == 13) {
                        table.search(this.value).draw();
                    }
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

                $('#merchant_email').change(function() {
                    table.draw();
                });

            } else {
                $('#custome_data_section').attr('hidden', true);
            }
        });

        $('#assign_data').click(function() {
            assignFunction();
        });

        function assignFunction() {
            var data = $('#main_table').DataTable().rows({
                selected: true
            }).data();
            var products = [];
            data.each(element => {
                products.push(element.id);
            });
            var formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('data', JSON.stringify(products));
            formData.append('merchant_id', $('#merchant_email').val());
            formData.append('assign_type', $('#assign_type').val());
            $.ajax({
                processData: false,
                contentType: false,
                url: "{{ route('assing_user_products') }}",
                type: "post",
                dataType: "json",
                data: formData,
                beforeSend: function() { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#assign_data').attr('disabled', 'disabled');
                    $('.spinner-border').removeAttr('hidden');
                },
                complete: function(result) {
                    $("#assign_data").removeAttr('disabled');
                    $('.spinner-border').attr('hidden', 'hidden');
                    result = result.responseJSON;
                    if (result.success) {
                        $('#alertdanger').attr('hidden', true);
                        $('#alertsuccess').attr('hidden', false);
                        $('#alertsuccess').empty();
                        $('#alertsuccess').append(result.message);
                        table.draw();
                    } else {
                        $('#alertsuccess').attr('hidden', true);
                        $('#alertdanger').attr('hidden', false);
                        $('#alertdanger').empty();
                        $('#alertdanger').append("<ul>");
                        $.each(result.data, function(index, value) {
                            $('#alertdanger').append("<li>" + value + "</li>");
                        });
                        $('#alertdanger').append("</ul>");
                    }
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            if (sessionStorage.getItem("success") == "true") {
                $('#success_div').removeAttr('hidden');
                setInterval(() => {
                    $('#success_div').attr('hidden', true);
                    $('#danger_div').attr('hidden', true);
                    $('#alertdanger').attr('hidden', true);
                    sessionStorage.setItem("success", false);
                }, 5000);
            } else {
                $('#success_div').attr('hidden', true);
                $('#danger_div').attr('hidden', true);
            }

            $('#merchant_email').select2();

        });
    </script>
    <script>
        function getCurrentLanguage() {
            var sessionLang = "{{ strtolower(session()->get('locale')) }}";
            if (sessionLang == '') {
                sessionLang = "{{ strtolower(Auth::user()->language) }}";
            }
            return sessionLang;
        }
    </script>
@endpush
