@extends('layouts.main')

@push('styles')
    <style>
        label {
            font-size: 16px;
            font-weight: 900;
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
                            <h4>{{ __('company/list_comapnies.list_companies_title') }}</h4>
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
                                <div class="col-md-3">
                                    <label for="merchant_type">{{ __('company/list_comapnies.merchant_type') }}</label>
                                    <select name="merchant_type" id="merchant_type" class="form-select">
                                        <option value=""></option>
                                        <option value="1">{{ __('company/list_comapnies.merchant_type_pharmacy') }}
                                        </option>
                                        <option value="2">{{ __('company/list_comapnies.merchant_type_market') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered data-table" style="width: 100%">
                                <thead style="background-color: #1b82ec; color: white">
                                    <tr>
                                        <th style="text-align: center;">{{ __('company/list_comapnies.table_header_id') }}
                                        </th>
                                        <th style="text-align: center">
                                            {{ __('company/list_comapnies.list_companies_header_ar_name') }}
                                        </th>
                                        <th style="text-align: center">
                                            {{ __('company/list_comapnies.list_companies_header_en_name') }}
                                        </th>
                                        <th style="text-align: center">
                                            {{ __('company/list_comapnies.merchant_type') }}
                                        </th>
                                        <th style="text-align: center">
                                            {{ __('company/list_comapnies.list_companies_header_en_description') }}
                                        </th>
                                        <th style="text-align: center">
                                            {{ __('company/list_comapnies.list_companies_header_en_action') }}
                                        </th>
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
                    "url": "{{ route('list-companies') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}";
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
                        data: 'ar_comp_name',
                        name: 'ar_comp_name',
                        orderable: false
                    },
                    {
                        data: 'en_comp_name',
                        name: 'en_comp_name',
                        orderable: false
                    },
                    {
                        data: 'merchant_type',
                        name: 'merchant_type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                "lengthMenu": [
                    [50, 100, 500, 1000, 2000],
                    [50, 100, 500, 1000, 2000]
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

        $('#merchant_type').change(function() {
            table.draw();
        });

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
                        url: "{{ route('delete-company') }}",
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
