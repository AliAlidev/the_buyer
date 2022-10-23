@extends('layouts.main')

@push('styles')
    <style>
        label {
            font-size: 16px;
            font-weight: 900;
        }

        .dt-buttons {
            margin-left: 15%;
            margin-right: 15%;
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
                            <h4>{{ __('product/list_products.list_items') }}</h4>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger" id="danger_div">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="alert alert-success" id="success_div" hidden>
                                {{ session()->get('success') }}
                            </div>
                            <div class="row mb-5">
                                @if (Auth::user()->isAdmin())
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
                                @endif
                                <div class="col-md-3">
                                    <label for="shape_id">{{ __('product/list_products.shape') }}</label>
                                    <select name="shape_id" id="shape_id" class="form-select">
                                        {{-- <option value=""></option>
                                        @foreach ($shapes as $shape)
                                            <option value="{{ $shape->id }}">{{ $shape->ar_shape_name }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="company_id">{{ __('product/list_products.company') }}</label>
                                    <select name="company_id" id="company_id" class="form-select">
                                        {{-- <option value=""></option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->ar_comp_name }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered data-table" style="width: 150%">
                                <thead style="background-color: #1b82ec; color: white">
                                    <tr>
                                        <th style="text-align: center;">{{ __('product/list_products.table_header_id') }}
                                        </th>
                                        <th style="text-align: center">{{ __('product/list_products.table_header_code') }}
                                        </th>
                                        <th style="text-align: center">{{ __('product/list_products.table_header_name') }}
                                        </th>
                                        <th style="text-align: center">{{ __('product/list_products.table_header_shape') }}
                                        </th>
                                        <th style="text-align: center">{{ __('product/list_products.table_header_comp') }}
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
                                        <th style="text-align: center">
                                            {{ __('product/list_products.table_header_action') }}</th>
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
    <script>
        $(document).ready(function() {
            if (sessionStorage.getItem("success") == "true") {
                $('#success_div').removeAttr('hidden');
                setInterval(() => {
                    $('#success_div').attr('hidden', true);
                    $('#danger_div').attr('hidden', true);
                    sessionStorage.setItem("success", false);
                }, 5000);
            } else {
                $('#success_div').attr('hidden', true);
                $('#danger_div').attr('hidden', true);
            }
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
    <script type="text/javascript">
        $(document).ready(function() {
            if ('{{ Auth::user()->isAdmin() }}' == false) {
                // fill companies
                $('#company_id').empty();
                url = "{{ route('get-companies', '#id') }}";
                url = url.replace('#id', "{{ Auth::user()->merchant_type }}");
                $.ajax({
                    url: url,
                    dataType: 'json',
                    complete: function(data) {
                        data = data.responseJSON;
                        var empty = '<option value=""></option>';
                        $('#company_id').append(empty);
                        data.forEach((item) => {
                            var option = '<option value=' + item.comp_id + '>' + item
                                .ar_comp_name + '</option>';
                            $('#company_id').append(option);
                        });
                    }
                });

                // fill shapes
                $('#shape_id').empty();
                url = "{{ route('get-shapes', '#id') }}";
                url = url.replace('#id', "{{ Auth::user()->merchant_type }}");
                $.ajax({
                    url: url,
                    dataType: 'json',
                    complete: function(data) {
                        data = data.responseJSON;
                        var empty = '<option value=""></option>';
                        $('#shape_id').append(empty);
                        data.forEach((item) => {
                            var option = '<option value=' + item.shape_id + '>' + item
                                .ar_shape_name + '</option>';
                            $('#shape_id').append(option);
                        });
                    }
                });
            }
        });

        var table;
        $(function() {
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
            table = $('.data-table').DataTable({
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
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                "lengthMenu": [
                    [50, 100, 500, 1000, 2000, 5000, 10000],
                    [50, 100, 500, 1000, 2000, 5000, 10000]
                ],
                "language": langOptions,
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

        $('#company_id, #shape_id, #merchant_type').change(function() {
            table.draw();
        });

        $('#merchant_type').change(function() {
            if ($(this).val() != 0) {
                // fill companies
                $('#company_id').empty();
                url = "{{ route('get-companies', '#id') }}";
                url = url.replace('#id', $(this).val());
                $.ajax({
                    url: url,
                    dataType: 'json',
                    complete: function(data) {
                        data = data.responseJSON;
                        var empty = '<option value=""></option>';
                        $('#company_id').append(empty);
                        data.forEach((item) => {
                            var option = '<option value=' + item.comp_id + '>' + item
                                .ar_comp_name + '</option>';
                            $('#company_id').append(option);
                        });
                    }
                });

                // fill shapes
                $('#shape_id').empty();
                url = "{{ route('get-shapes', '#id') }}";
                url = url.replace('#id', $(this).val());
                $.ajax({
                    url: url,
                    dataType: 'json',
                    complete: function(data) {
                        data = data.responseJSON;
                        var empty = '<option value=""></option>';
                        $('#shape_id').append(empty);
                        data.forEach((item) => {
                            var option = '<option value=' + item.shape_id + '>' + item
                                .ar_shape_name + '</option>';
                            $('#shape_id').append(option);
                        });
                    }
                });
            }
        })

        $('body').on('click', '.delete', function() {
            var title = '';
            var text = '';
            var buttons = '';
            if (getCurrentLanguage() == 'ar') {
                title = "هل أنت متأكد من عملية الحذف؟";
                text = "هذا المنتج وجميع المرفقات الخاصة به سيتم حذفها ولايمكن التراجع عن هذه العملية!";
                buttons = ["إلغاء", "تأكيد"];
            } else {
                title = "Are you sure?";
                text = "This record and it`s details will be permanantly deleted!";
                buttons = ["Cancel", "Yes!"];
            }

            var id = $(this).attr('id');
            swal({
                title: title,
                text: text,
                icon: 'warning',
                buttons: buttons,
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
                        url: "{{ route('delete-product') }}",
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                $('.alert-success').empty();
                                $('.alert-success').append(result.message);
                                $('.alert-success').removeAttr('hidden');
                                $('.data-table').DataTable().clear().draw();
                                setInterval(() => {
                                    $('.alert-success').attr('hidden', 'hidden');
                                }, 5000);
                            } else {
                                $('.alert-danger').empty();
                                $('.alert-danger').append(result.message);
                                $('.alert-danger').removeAttr('hidden');
                                setInterval(() => {
                                    $('.alert-danger').attr('hidden', 'hidden');
                                }, 5000);
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
