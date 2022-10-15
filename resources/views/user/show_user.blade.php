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
                            <h4>{{ __('user/create_user.title') }}</h4>
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
                                            <label for="">{{ __('user/create_user.name') }}</label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                value="{{ $user->name }}" readonly>
                                        </div>
                                        {{-- email --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/create_user.email') }}</label>
                                            <input type="text" id="email" name="email" class="form-control"
                                                value="{{ $user->email }}" readonly>
                                        </div>
                                        {{-- phone --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/create_user.phone') }}</label>
                                            <input type="text" id="phone" name="phone" class="form-control"
                                                value="{{ $user->phone }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        {{-- tel_phone --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/create_user.tel_phone') }}</label>
                                            <input type="text" id="tel_phone" name="tel_phone" class="form-control"
                                                value="{{ $user->tel_phone }}" readonly>
                                        </div>
                                        {{-- role --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/create_user.role') }}</label>
                                            <select name="role" id="role" class="form-select">
                                                @if ($user->role == 1)
                                                    <option value="1">
                                                        {{ __('user/create_user.role_merchant') }}</option>
                                                @elseif ($user->role == 2)
                                                    <option value="2">
                                                        {{ __('user/create_user.role_employee') }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        {{-- merchant name --}}
                                        @if ($user->role == 2)
                                            <div class="col-md-4" id="merchant_id_div">
                                                <label for="">{{ __('user/create_user.merchant_id') }}</label>
                                                <select name="merchant_id" id="merchant_id" class="form-select">
                                                    @foreach ($merchants as $merchant)
                                                        @if ($user->merchant_id == $merchant->merchant_id)
                                                            <option value="{{ $merchant->id }}">{{ $merchant->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="row mt-4">
                                        {{-- provinces --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/create_user.province') }}</label>
                                            <select name="province" id="province" class="form-select">
                                                @foreach ($provinces as $province)
                                                    @if (strtolower(session()->get('locale')) == 'ar')
                                                        @if ($user->province == $province->id)
                                                            <option value="{{ $province->id }}">{{ $province->ar_name }}
                                                            </option>
                                                        @endif
                                                    @else
                                                        @if ($user->province == $province->id)
                                                            <option value="{{ $province->id }}">{{ $province->en_name }}
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- cities --}}
                                        <div class="col-md-4">
                                            <label for="">{{ __('user/create_user.city') }}</label>
                                            <select name="city" id="city" class="form-select">
                                                @foreach ($cities as $city)
                                                    @if (strtolower(session()->get('locale')) == 'ar')
                                                        @if ($user->city == $city->id)
                                                            <option value="{{ $city->id }}">{{ $city->ar_name }}
                                                            </option>
                                                        @endif
                                                    @else
                                                        @if ($user->city == $city->id)
                                                            <option value="{{ $city->id }}">{{ $city->en_name }}
                                                            </option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- merchant type --}}
                                        <div class="col-md-4">
                                            <label for="merchant_type">{{ __('user/create_user.merchant_type') }}</label>
                                            <select name="merchant_type" id="merchant_type" class="form-select">
                                                @if ($user->merchant_type == 1)
                                                    <option value="1">
                                                        {{ __('user/create_user.merchant_type_market') }}
                                                    </option>
                                                @elseif ($user->merchant_type == 2)
                                                    <option value="2">
                                                        {{ __('user/create_user.merchant_type_pharmacy') }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        {{-- language --}}
                                        <div class="col-md-4">
                                            <label for="language">{{ __('user/create_user.language') }}</label>
                                            <select name="language" id="language" class="form-select">
                                                @if ($user->language == 'ar')
                                                    <option value="ar">
                                                        {{ __('user/create_user.language_ar') }}
                                                    </option>
                                                @elseif ($user->language == 'en')
                                                    <option value="en">
                                                        {{ __('user/create_user.language_en') }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    {{-- address --}}
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <label for="">{{ __('user/create_user.address') }}</label>
                                            <textarea name="address" id="address" cols="30" rows="5" class="form-control">{{ $user->address }}</textarea>
                                        </div>
                                    </div>
                                    {{-- notes --}}
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <label for="">{{ __('user/create_user.notes') }}</label>
                                            <textarea name="notes" id="notes" cols="30" rows="5" class="form-control">{{ $user->notes }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12 text-center">
                                            <br />
                                            <button class="btn btn-primary" onclick="location.href='{{ route('list-users') }}'"
                                                type="button">{{ __('user/show_user.back_btn') }}</button>
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
