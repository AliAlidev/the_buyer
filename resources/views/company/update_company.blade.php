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
                            <h4>{{ __('company/update_company.update_company_title') }}</h4>
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
                                            <label for="type">{{ __('company/update_company.merchant_type') }}</label>
                                            <select name="type" id="type" class="form-select">
                                                @if ($company->merchant_type == 1)
                                                    <option value="2">
                                                        {{ __('company/update_company.merchant_type_pharmacy') }}
                                                    </option>
                                                @elseif ($company->merchant_type == 2)
                                                    <option value="1">
                                                        {{ __('company/update_company.merchant_type_market') }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-10">
                                            <label for="">{{ __('company/update_company.company_name_ar') }}</label>
                                            <input type="text" id="company_name_ar" name="company_name_ar"
                                                value="{{ old('company_name_ar') == null ? $company->ar_comp_name : old('company_name_ar') }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-10">
                                            <label for="">{{ __('company/update_company.company_name_en') }}</label>
                                            <input type="text" id="company_name_en" name="company_name_en"
                                                value="{{ old('company_name_en') == null ? $company->en_comp_name : old('company_name_en') }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-10">
                                            <label for="">{{ __('company/update_company.description') }}</label>
                                            <textarea name="description" id="description" cols="30" rows="5" class="form-control">{{ old('description') == null ? $company->description : old('description') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-12 text-center">
                                            <br />
                                            <button class="btn btn-primary"
                                                type="submit">{{ __('company/update_company.update_btn') }}</button>
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
        $('#main_form').submit(function(e) {
            e.preventDefault();
            var currForm = $(this);
            var formData = new FormData($(this)['0']);
            formData.append("_token", "{{ csrf_token() }}");
            var url = "{{ route('update-company', '#id') }}";
            url = url.replace('#id', "{{ $company->id }}");
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
                        window.location.href = "{{ route('list-companies') }}";
                    } else {
                        console.log(value);
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
