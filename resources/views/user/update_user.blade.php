@extends('layouts.main')

@push('styles')
    <style>
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            /* add padding to account for vertical scrollbar */
            padding-right: 20px;
        }

        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 34px;
            user-select: none;
            -webkit-user-select: none;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
            position: absolute;
            top: 1px;
            right: 1px;
        }

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
                            <h4>{{ __('user/update_user.title') }}</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <div id="alertdanger" class="alert alert-danger" hidden>
                                </div>
                                <div id="alertsuccess" class="alert alert-success" hidden>
                                </div>

                                <form id="main_form" action="" style="margin: 5%">
                                    <div class="row mt-4">
                                        {{-- name --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.name') }}</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                value="{{ old('name') == null ? $user->name : old('name') }}">
                                        </div>
                                        {{-- email --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.email') }}</label>
                                            <input type="text" id="email" name="email" class="form-control"
                                                value="{{ old('email') == null ? $user->email : old('email') }}">
                                        </div>
                                        {{-- phone --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.phone') }}</label>
                                            <input type="text" id="phone" name="phone" class="form-control"
                                                value="{{ old('phone') == null ? $user->phone : old('phone') }}">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        {{-- password --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.password') }}</label>
                                            <input type="password" id="password" name="password" class="form-control">
                                        </div>
                                        {{-- password confirm --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.password_confirm') }}</label>
                                            <input type="password" id="password_confirm" name="password_confirm"
                                                class="form-control">
                                        </div>
                                        {{-- tel_phone --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.tel_phone') }}</label>
                                            <input type="text" id="tel_phone" name="tel_phone" class="form-control"
                                                value="{{ old('tel_phone') == null ? $user->tel_phone : old('tel_phone') }}">
                                        </div>

                                    </div>
                                    <div class="row mt-4">
                                        {{-- role --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.role') }}</label>
                                            <select name="role" id="role" class="form-select">
                                                <option {{ $user->role == 1 ? 'selected' : '' }} value="1">
                                                    {{ __('user/update_user.role_merchant') }}</option>
                                                <option {{ $user->role == 2 ? 'selected' : '' }} value="2">
                                                    {{ __('user/update_user.role_employee') }}</option>
                                            </select>
                                        </div>
                                        {{-- merchant name --}}
                                        @if ($user->role == 2)
                                            <div class="col-md-4" id="merchant_id_div">
                                                <label for="">{{ __('user/update_user.merchant_id') }}</label>
                                                <select name="merchant_id" id="merchant_id" class="form-select">
                                                    <option value=""></option>
                                                    @foreach ($merchants as $merchant)
                                                        <option {{ $user->merchant_id == $merchant->id ? 'selected' : '' }}
                                                            value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @else
                                            <div class="col-md-4" id="merchant_id_div" hidden>
                                                <label for="">{{ __('user/update_user.merchant_id') }}</label>
                                                <select name="merchant_id" id="merchant_id" class="form-select">
                                                    <option value=""></option>
                                                    @foreach ($merchants as $merchant)
                                                        <option {{ $user->merchant_id == $merchant->id ? 'selected' : '' }}
                                                            value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        {{-- provinces --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.province') }}</label>
                                            <select name="province" id="province" class="form-select">
                                                <option value=""></option>
                                                @foreach ($provinces as $province)
                                                    @if (strtolower(session()->get('locale')) == 'ar')
                                                        <option {{ $user->province == $province->id ? 'selected' : '' }}
                                                            value="{{ $province->id }}">{{ $province->ar_name }}
                                                        </option>
                                                    @else
                                                        <option {{ $user->province == $province->id ? 'selected' : '' }}
                                                            value="{{ $province->id }}">{{ $province->en_name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row mt-4">
                                        {{-- cities --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/update_user.city') }}</label>
                                            <select name="city" id="city" class="form-select">
                                                @foreach ($cities as $city)
                                                    @if (strtolower(session()->get('locale')) == 'ar')
                                                        <option {{ $user->city == $city->id ? 'selected' : '' }}
                                                            value="{{ $city->id }}">{{ $city->ar_name }}
                                                        </option>
                                                    @elseif (strtolower(session()->get('locale')) == 'en')
                                                        <option {{ $user->city == $city->id ? 'selected' : '' }}
                                                            value="{{ $city->id }}">{{ $city->en_name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- merchant type --}}
                                        <div class="col-md-4">
                                            <label for="merchant_type">{{ __('user/update_user.merchant_type') }}</label>
                                            <select name="merchant_type" id="merchant_type" class="form-select">
                                                <option {{ $user->merchant_type == 1 ? 'selected' : '' }} value="1">
                                                    {{ __('user/update_user.merchant_type_market') }}
                                                </option>
                                                <option {{ $user->merchant_type == 2 ? 'selected' : '' }} value="2">
                                                    {{ __('user/update_user.merchant_type_pharmacy') }}
                                                </option>
                                            </select>
                                        </div>
                                        {{-- language --}}
                                        <div class="col-md-4">
                                            <label for="language">{{ __('user/update_user.language') }}</label>
                                            <select name="language" id="language" class="form-select">
                                                <option {{ $user->language == 'ar' ? 'selected' : '' }} value="ar">
                                                    {{ __('user/update_user.language_ar') }}
                                                </option>
                                                <option {{ $user->language == 'en' ? 'selected' : '' }} value="en">
                                                    {{ __('user/update_user.language_en') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    {{-- address --}}
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <label for="">{{ __('user/update_user.address') }}</label>
                                            <textarea name="address" id="address" cols="30" rows="5" class="form-control">{{ $user->address }}</textarea>
                                        </div>
                                    </div>
                                    {{-- notes --}}
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <label for="">{{ __('user/update_user.notes') }}</label>
                                            <textarea name="notes" id="notes" cols="30" rows="5" class="form-control">{{ $user->notes }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12 text-center">
                                            <br />
                                            <button class="btn btn-primary"
                                                type="submit">{{ __('user/update_user.update_btn') }}</button>
                                        </div>
                                    </div>
                                </form>

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
        $('#role').change(function() {
            if ($(this).val() == '2') {
                $('#merchant_id_div').attr('hidden', false);
                $('#merchant_id').select2();
            } else
                $('#merchant_id_div').attr('hidden', true);
        });

        function getCurrentLanguage() {
            var sessionLang = "{{ strtolower(session()->get('locale')) }}";
            if (sessionLang == '') {
                sessionLang = "{{ strtolower(Auth::user()->language) }}";
            }
            return sessionLang;
        }

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
    </script>
    <script>
        $('#main_form').submit(function(e) {
            e.preventDefault();
            var currForm = $(this);
            var formData = new FormData($(this)['0']);
            formData.append("_token", "{{ csrf_token() }}");

            var url = "{{ route('update-user', '#id') }}";
            url = url.replace('#id', "{{ $user->id }}");
            $.ajax({
                url: url,
                type: "post",
                cache: false,
                dataType: "json",
                processData: false,
                contentType: false,
                data: formData,
                success: function(value) {
                    if (value.success) {
                        $('#alertdanger').attr('hidden', true);
                        $('#alertsuccess').attr('hidden', false);
                        $('#alertsuccess').empty();
                        $('#alertsuccess').append(value.message);
                        $(currForm)[0].reset();
                        $('#effict_materials_table tr').remove();
                        $('.result').removeClass(
                            'is-valid  was-validated form-control:valid');
                        $('.result').removeClass(
                            'is-invalid  was-validated form-control:invalid');
                        $('.name').removeClass(
                            'is-valid  was-validated form-control:valid');
                        $('.name').removeClass(
                            'is-invalid  was-validated form-control:invalid');

                        $('.numofpartsdiv').attr('hidden', 'hidden');

                        sessionStorage.success = true;
                        window.location.href = "{{ route('list-users') }}";
                    } else {
                        $('#alertsuccess').attr('hidden', true);
                        $('#alertdanger').attr('hidden', false);
                        $('#alertdanger').empty();
                        $('#alertdanger').append(value.data);
                    }
                },
                'error': function(xhr, status, error) {
                    $('#alertsuccess').attr('hidden', true);
                    $('#alertdanger').attr('hidden', false);
                    $('#alertdanger').empty();
                    $('#alertdanger').append("<ul>");
                    $.each(xhr.responseJSON.data, function(index, value) {
                        $('#alertdanger').append("<li>" + value + "</li>");
                    });
                    $('#alertdanger').append("</ul>");
                }
            });

        });
    </script>
@endpush