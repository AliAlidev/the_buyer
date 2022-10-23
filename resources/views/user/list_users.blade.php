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
                            <h4>{{ __('user/list_users.title') }}</h4>
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
                                <div class="col-md-2">
                                    <label for="merchant_type">{{ __('user/list_users.merchant_type') }}</label>
                                    <select name="merchant_type" id="merchant_type" class="form-select">
                                        <option value=""></option>
                                        <option value="1">{{ __('user/list_users.merchant_type_pharmacy') }}
                                        </option>
                                        <option value="2">{{ __('user/list_users.merchant_type_market') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="province">{{ __('user/list_users.province') }}</label>
                                    <select name="province" id="province" class="form-select">
                                        <option value=""></option>
                                        @foreach ($provinces as $province)
                                            @if (strtolower(session()->get('locale')) == 'ar')
                                                <option value="{{ $province->id }}">{{ $province->ar_name }}
                                                </option>
                                            @else
                                                <option value="{{ $province->id }}">{{ $province->en_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="">{{ __('user/create_user.city') }}</label>
                                    <select name="city" id="city" class="form-select">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="">{{ __('user/list_users.role') }}</label>
                                    <select name="role" id="role" class="form-select">
                                        <option value=""></option>
                                        <option value="1">{{ __('user/list_users.role_merchant') }}</option>
                                        <option value="2">{{ __('user/list_users.role_employee') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="">{{ __('user/list_users.language') }}</label>
                                    <select name="language" id="language" class="form-select">
                                        <option value=""></option>
                                        <option value="ar"> {{ __('user/list_users.language_ar') }}</option>
                                        <option value="en"> {{ __('user/list_users.language_en') }} </option>
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered data-table" style="width: 250%">
                                <thead style="background-color: #1b82ec; color: white">
                                    <tr>
                                        <th style="text-align: center;">{{ __('user/list_users.table_header_id') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.name') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.email') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.phone') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.tel_phone') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.role') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.merchant_id') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.province') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.city') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.merchant_type') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.language') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.address') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.notes') }}</th>
                                        <th style="text-align: center"> {{ __('user/list_users.actions') }}</th>
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
                    "url": "{{ route('list-users') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.merchant_type = $('#merchant_type').val();
                        d.province = $('#province').val();
                        d.city = $('#city').val();
                        d.role = $('#role').val();
                        d.language = $('#language').val();
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
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        width: '10%'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                        width: '10%'
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'tel_phone',
                        name: 'tel_phone',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'merchant_id',
                        name: 'merchant_id',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'province',
                        name: 'province',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'city',
                        name: 'city',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'merchant_type',
                        name: 'merchant_type',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'language',
                        name: 'language',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'address',
                        name: 'address',
                        orderable: false,
                        width: '13%'
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        orderable: false,
                        width: '13%'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '14%'
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

        $('#merchant_type, #city, #role, #language').change(function() {
            table.draw();
        });

        $('#province').change(function() {
            var url = "{{ route('get_cities', '#id') }}";
            url = url.replace('#id', $(this).val());
            $.ajax({
                url: url,
                dataType: 'json',
                complete: function(result) {
                    $('#city').empty();
                    $('#city').append("<option value=''></option>");
                    $.each(result.responseJSON, function(key, value) {
                        if (getCurrentLanguage() == 'ar') {
                            var row = "<option value=" + value.id + ">" + value.ar_name +
                                "</option>";
                        } else {
                            var row = "<option value=" + value.id + ">" + value.en_name +
                                "</option>";
                        }
                        $('#city').append(row);
                    });
                }
            });
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
                        url: "{{ route('delete-user') }}",
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
