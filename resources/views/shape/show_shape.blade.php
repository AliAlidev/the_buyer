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
                            <h4>{{ __('shape/show_shape.show_shape_title') }}</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <div id="alertdanger" class="alert alert-danger" hidden>
                                </div>
                                <div id="alertsuccess" class="alert alert-success" hidden>
                                </div>

                                <form id="main_form" action="" style="margin: 5%">
                                    <div class="row d-flex justify-content-center mt-4">
                                        <div class="col-md-2">
                                            <label for="type">{{ __('shape/show_shape.merchant_type') }}</label>
                                            <select name="type" id="type" class="form-select">
                                                @if ($shape->merchant_type == 1)
                                                    <option value="2">
                                                        {{ __('shape/show_shape.merchant_type_pharmacy') }}
                                                    </option>
                                                @elseif($shape->merchant_type == 2)
                                                    <option value="1">
                                                        {{ __('shape/show_shape.merchant_type_market') }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-10">
                                            <label for="">{{ __('shape/show_shape.shape_name_ar') }}</label>
                                            <input type="text" id="shape_name_ar" name="shape_name_ar"
                                                value="{{ $shape->ar_shape_name }}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-10">
                                            <label for="">{{ __('shape/show_shape.shape_name_en') }}</label>
                                            <input type="text" id="shape_name_en" name="shape_name_en"
                                                value="{{ $shape->en_shape_name }}" class="form-control" readonly>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12 text-center">
                                            <br />
                                            <button class="btn btn-primary" onclick="location.href='{{ route('list-shapes') }}'"
                                                type="button">{{ __('shape/show_shape.back_btn') }}</button>
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
