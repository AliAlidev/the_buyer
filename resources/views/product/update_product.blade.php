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
                            <h4>{{ __('product/update_product.update_element_title') }}</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <div id="alertdanger" class="alert alert-danger" hidden>
                                </div>
                                <div id="alertsuccess" class="alert alert-success" hidden>
                                </div>

                                <div id="market_section" hidden>
                                    <form class="form" method="POST" style="margin: 5%">
                                        @csrf
                                        <div class="row mt-5 pl-5 pr-5 d-flex justify-content-center">
                                            {{-- code --}}
                                            <div class="row">
                                                <label for="result">{{ __('product/update_product.code') }}</label>
                                                <div class="col-md-10">
                                                    <input class="form-control result" type="text" name="code"
                                                        value="{{ old('code') }}"
                                                        placeholder="{{ __('product/update_product.code') }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="button" class="btn btn-primary getdata"
                                                        value="{{ __('product/update_product.check_btn') }}">
                                                </div>
                                            </div>
                                            {{-- name --}}
                                            <div class="row mt-3">
                                                <label for="name">
                                                    {{ __('product/update_product.product_name') }}</label>
                                                <div class="col-md-10">
                                                    <input name="name" value="{{ old('name') }}" type="text"
                                                        class="form-control name"
                                                        placeholder="{{ __('product/update_product.product_name') }}"
                                                        required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="button" class="btn btn-primary getdatabyname"
                                                        value="{{ __('product/update_product.check_btn') }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- shape --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.market_shape') }}</label>
                                                    <select name="shape_id" id="shape" class="form-select shape">
                                                        <option value=""></option>
                                                        @foreach ($shapes_market as $shape)
                                                            <option value="{{ $shape->shape_id }}">
                                                                {{ $shape->ar_shape_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- company --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.company') }}</label>
                                                    <select name="comp_id" id="company" class="form-select company">
                                                        <option value=""></option>
                                                        @foreach ($companies_market as $company)
                                                            <option value="{{ $company->comp_id }}">
                                                                {{ $company->ar_comp_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- has_parts --}}
                                                <div class="col-md-2">
                                                    <label class="form-check-label mt-3"
                                                        for="flexSwitchCheckDefault">{{ __('product/update_product.has_parts') }}</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input hasparts" type="checkbox"
                                                            name="hasparts">
                                                    </div>
                                                </div>
                                                {{-- num_of_parts --}}
                                                <div class="col-md-4 numofpartsdiv" hidden>
                                                    <label class="form-check-label mt-3"
                                                        for="flexSwitchCheckDefault">{{ __('product/update_product.num_of_parts') }}</label>
                                                    <input type="number" class="form-control numofparts"
                                                        value="{{ old('numofparts') != null ? old('numofparts') : 0 }}"
                                                        name="numofparts">
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- minimum_amount --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.minimum_amount') }}</label>
                                                    <input id="minimum_amount" class="form-control" type="text"
                                                        value="{{ old('minimum_amount') }}" name="minimum_amount"
                                                        placeholder="{{ __('product/update_product.minimum_amount') }}">
                                                </div>
                                                {{-- maximum_amount --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.maximum_amount') }}</label>
                                                    <input id="maximum_amount" class="form-control" type="text"
                                                        value="{{ old('maximum_amount') }}" name="maximum_amount"
                                                        placeholder="{{ __('product/update_product.maximum_amount') }}">
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.description') }}</label>
                                                    <textarea id="description" class="form-control" name="description" id="" cols="30" rows="10">{{ old('description') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-12 text-center">
                                                <br />
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('product/update_product.add_btn') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div id="pharmacy_section" hidden>
                                    <form class="form" method="POST" style="margin: 5%">
                                        @csrf
                                        <div class="row mt-5 pl-5 pr-5 d-flex justify-content-center">
                                            {{-- code --}}
                                            <div class="row">
                                                <label for="result">{{ __('product/update_product.code') }}</label>
                                                <div class="col-md-10">
                                                    <input class="form-control result" type="text" name="code"
                                                        value="{{ old('code') == null ? $product->code : old('code') }}"
                                                        placeholder="{{ __('product/update_product.code') }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="button" class="btn btn-primary getdata"
                                                        value="{{ __('product/update_product.check_btn') }}">
                                                </div>
                                            </div>
                                            {{-- name --}}
                                            <div class="row mt-3">
                                                <label for="name">
                                                    {{ __('product/update_product.product_name') }}</label>
                                                <div class="col-md-10">
                                                    <input name="name"
                                                        value="{{ old('name') == null ? $product->name : old('name') }}"
                                                        type="text" class="form-control name"
                                                        placeholder="{{ __('product/update_product.product_name') }}"
                                                        required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="button" class="btn btn-primary getdatabyname"
                                                        value="{{ __('product/update_product.check_btn') }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- dose --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.dose') }}</label>
                                                    <input id="dose" class="form-control" type="text"
                                                        value="{{ old('dose') == null ? $product->dose : old('dose') }}"
                                                        name="dose"
                                                        placeholder="{{ __('product/update_product.dose') }}">
                                                </div>
                                                {{-- tab count --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.tab_counts') }}</label>
                                                    <input id="tab_count" class="form-control" type="text"
                                                        value="{{ old('tab_count') == null ? $product->tab_count : old('tab_count') }}"
                                                        name="tab_count"
                                                        placeholder="{{ __('product/update_product.tab_counts') }}">
                                                </div>
                                                {{-- shape --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.shape') }}</label>
                                                    <select name="shape_id" id="shape" class="form-control shape">
                                                        <option value=""></option>
                                                        @foreach ($shapes_pharmacy as $shape)
                                                            <option
                                                                {{ $product->shape_id == $shape->shape_id ? 'selected' : '' }}
                                                                value="{{ $shape->shape_id }}">
                                                                {{ $shape->ar_shape_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- company --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.company') }}</label>
                                                    <select name="comp_id" id="company" class="form-control company">
                                                        <option value=""></option>
                                                        @foreach ($companies_pharmacy as $company)
                                                            <option
                                                                {{ $product->comp_id == $company->comp_id ? 'selected' : '' }}
                                                                value="{{ $company->comp_id }}">
                                                                {{ $company->ar_comp_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- treatement group --}}
                                                <div class="col-md-4">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.treatement_group') }}</label>
                                                    <select name="treatement_group" id="treatement_group"
                                                        class="form-control treatement_group">
                                                        <option value=""></option>
                                                        @foreach ($treatement_groups as $group)
                                                            <option
                                                                {{ $product->treatement_group == $group->tg_id ? 'selected' : '' }}
                                                                value="{{ $group->tg_id }}">
                                                                {{ $group->ar_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- treatements --}}
                                                <div class="col-md-8">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.treatement') }}</label>
                                                    <input id="treatements" class="form-control" type="text"
                                                        value="{{ old('treatements') == null ? $product->treatements : old('treatements') }}"
                                                        name="treatements"
                                                        placeholder="{{ __('product/update_product.treatement') }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- special_alarms --}}
                                                <div class="col-md-6">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.special_alarms') }}</label>
                                                    <input id="special_alarms" class="form-control" type="text"
                                                        value="{{ old('special_alarms') == null ? $product->special_alarms : old('special_alarms') }}"
                                                        name="special_alarms"
                                                        placeholder="{{ __('product/update_product.special_alarms') }}">
                                                </div>
                                                {{-- interference --}}
                                                <div class="col-md-6">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.interference') }}</label>
                                                    <input id="interference" class="form-control" type="text"
                                                        value="{{ old('interference') == null ? $product->interference : old('interference') }}"
                                                        name="interference"
                                                        placeholder="{{ __('product/update_product.interference') }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- side_effects --}}
                                                <div class="col-md-6">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.side_effects') }}</label>
                                                    <input id="side_effects" class="form-control" type="text"
                                                        value="{{ old('side_effects') == null ? $product->side_effects : old('side_effects') }}"
                                                        name="side_effects"
                                                        placeholder="{{ __('product/update_product.side_effects') }}">
                                                </div>
                                                {{-- has_parts --}}
                                                <div class="col-md-2">
                                                    <label class="form-check-label mt-3"
                                                        for="flexSwitchCheckDefault">{{ __('product/update_product.has_parts') }}</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input hasparts" type="checkbox"
                                                            name="hasparts">
                                                    </div>
                                                </div>
                                                {{-- num_of_parts --}}
                                                <div class="col-md-4 numofpartsdiv" hidden>
                                                    <label class="form-check-label mt-3"
                                                        for="flexSwitchCheckDefault">{{ __('product/update_product.num_of_parts') }}</label>
                                                    <input type="number" class="form-control numofparts"
                                                        value="{{ old('numofparts') == null ? $product->num_of_parts : old('numofparts') }}"
                                                        name="numofparts">
                                                </div>
                                            </div>
                                            {{-- Effect Materiald --}}
                                            <div class="row mt-3">
                                                <div class="col-md-2">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.effect_materials') }}
                                                    </label>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="alert alert-danger" id="alert_div" hidden></div>
                                                    <table>
                                                        <tr>
                                                            <td style="width: 40%">
                                                                <label
                                                                    for="">{{ __('product/update_product.effect_materials_table_material') }}</label>

                                                            </td>
                                                            <td style="width: 30%">
                                                                <label
                                                                    for="">{{ __('product/update_product.effect_materials_table_dose') }}</label>
                                                            </td>
                                                            <td style="width: 30%; padding-right: 2%">
                                                                <label
                                                                    for="">{{ __('product/update_product.effect_materials_table_unit') }}</label>
                                                            </td>
                                                            <td style="width: 10%"></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 40%">
                                                                <select id="eff_materials_material_input"
                                                                    class="form-control eff_materials">
                                                                    <option value=""></option>
                                                                    @foreach ($eff_materials as $mat)
                                                                        <option value="{{ $mat->en_name }}">
                                                                            {{ $mat->en_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td style="width: 30%">
                                                                <input type="text" value=""
                                                                    id="eff_materials_dose_input" class="form-control">
                                                            </td>
                                                            <td style="width: 30%; padding-right: 2%">
                                                                <input type="text" value=""
                                                                    id="eff_materials_dose_unit" class="form-control">
                                                            </td>
                                                            <td>
                                                            <td style="width: 10%">
                                                                <button id="add_new_row" class="btn btn-primary"><i
                                                                        class="ion ion-ios-add-circle"></i>
                                                                </button>
                                                            </td>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                <div class="col-md-12 mt-4">
                                                    <label
                                                        for="">{{ __('product/update_product.list_effect_materials') }}</label>
                                                    <table id="effict_materials_table"
                                                        class="table table-striped display-all">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('product/update_product.effect_materials_table_id') }}
                                                                </th>
                                                                <th>{{ __('product/update_product.effect_materials_table_material') }}
                                                                </th>
                                                                <th>{{ __('product/update_product.effect_materials_table_dose') }}
                                                                </th>
                                                                <th>{{ __('product/update_product.effect_materials_table_unit') }}
                                                                </th>
                                                                <th>{{ __('product/update_product.effect_materials_table_action') }}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="effict_materials_table_body">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">
                                                {{-- minimum_amount --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.minimum_amount') }}</label>
                                                    <input id="minimum_amount" class="form-control" type="text"
                                                        value="{{ old('minimum_amount') == null ? $product->minimum_amount : old('minimum_amount') }}"
                                                        name="minimum_amount"
                                                        placeholder="{{ __('product/update_product.minimum_amount') }}">
                                                </div>
                                                {{-- maximum_amount --}}
                                                <div class="col-md-3">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.maximum_amount') }}</label>
                                                    <input id="maximum_amount" class="form-control" type="text"
                                                        value="{{ old('maximum_amount') == null ? $product->maximum_amount : old('maximum_amount') }}"
                                                        name="maximum_amount"
                                                        placeholder="{{ __('product/update_product.maximum_amount') }}">
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="mt-3"
                                                        for="">{{ __('product/update_product.description') }}</label>
                                                    <textarea id="description" class="form-control" name="description" id="" cols="30" rows="10">{{ old('description') == null ? $product->description : old('description') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-12 text-center">
                                                <br />
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('product/update_product.update_btn') }}</button>
                                            </div>
                                        </div>
                                    </form>
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
        $(document).ready(function() {
            var merchantType = "{{ $product->merchant_type }}";
            if (merchantType == 2) {
                $('#pharmacy_section').attr('hidden', 'hidden');
                $('#market_section').removeAttr('hidden');
                $('.shape').select2();
                $('.company').select2();
            } else if (merchantType == 1) {
                $('#pharmacy_section').removeAttr('hidden');
                $('#market_section').attr('hidden', 'hidden');
                $('.shape').select2();
                $('.company').select2();
                $('.treatement_group').select2();
                $('.eff_materials').select2();
            }

            // if has parts
            var hasParts = "{{ $product->has_parts }}";
            if (hasParts) {
                $('.numofpartsdiv').removeAttr('hidden');
                $('.hasparts').prop('checked', true);
            } else {
                $('.numofpartsdiv').attr('hidden', 'hidden');
            }

            // effict materials
            var effMaterials = "{{ $productEffMat }}";
            var textArea = document.createElement('textarea');
            textArea.innerHTML = effMaterials;
            var effMaterials = JSON.parse(textArea.value);
            if (effMaterials) {
                effMaterials.forEach(element => {
                    if (element.dose == null)
                        element.dose = '';
                    if (element.unit == null)
                        element.unit = '';

                    var col1 = '<td>#</td>';
                    var col2 = '<td>' + element.en_name + '</td>';
                    var col3 = '<td>' + element.dose + '</td>';
                    var col4 = '<td>' + element.unit + '</td>';
                    var col5 = '<td style="width: 10%""> <button class="btn btn-danger delete_material">' +
                        '<i class="ion ion-md-remove-circle"></i></button></td>';
                    var row = "<tr>" + col1 + col2 + +col3 + col3 + col4 + col5 + "<tr>";

                    $('#effict_materials_table_body').append(row);

                    $('.delete_material').bind('click', function(e) {
                        e.preventDefault();
                        $(this).parents("tr").remove();
                    });
                });
            }
        });


        $('#add_new_row').click(function(e) {
            e.preventDefault();

            if ($('#eff_materials_material_input').val() == '') {
                $('#alert_div').empty();
                $('#alert_div').append("You should select material first!");
                $('#alert_div').removeAttr('hidden');
                setTimeout(function() {
                    $('#alert_div').attr('hidden', 'hidden');
                }, 5000);

            } else {
                var col1 = '<td>#</td>';
                var col2 = '<td>' + $("#eff_materials_material_input").val() + '</td>';
                var col3 = '<td>' + $("#eff_materials_dose_input").val() + '</td>';
                var col4 = '<td>' + $("#eff_materials_dose_unit").val() + '</td>';
                var col5 = '<td style="width: 10%""> <button class="btn btn-danger delete_material">' +
                    '<i class="ion ion-md-remove-circle"></i></button></td>';
                var row = "<tr>" + col1 + col2 + +col3 + col3 + col4 + col5 + "<tr>";

                $('#effict_materials_table_body').append(row);

                $('#eff_materials_material_input').val('').trigger('change');
                $('#eff_materials_dose_input').val('');
                $('#eff_materials_dose_unit').val('');

                $('.delete_material').bind('click', function(e) {
                    e.preventDefault();
                    $(this).parents("tr").remove();
                });
            }
        });
    </script>

    <script>
        $('.hasparts').change(function() {
            if ($(this).is(':checked')) {
                $('.numofpartsdiv').removeAttr('hidden');
            } else {
                $('.numofpartsdiv').attr('hidden', 'hidden');
            }
        });
    </script>

    <script>
        $('.name').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ route('get-items-name') }}",
                    dataType: "json",
                    data: {
                        searchText: request.term
                    },
                    success: function(data) {
                        response($.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.name
                            };
                        }));
                    }
                });
            }
        });
    </script>

    <script>
        $('.getdatabyname').click(function(e) {
            $('.result').removeClass(
                'is-invalid  was-validated form-control:invalid');
            $('.result').removeClass(
                'is-valid  was-validated form-control:valid');

            // clear old
            // $('.result').val('');
            var currInput = $(this).closest('div').prev().children('input');
            var elementName = currInput.val();
            $.ajax({
                type: 'post',
                dataType: "JSON",
                url: "{{ route('get-data-by-name') }}",
                data: {
                    '_token': '{{ csrf_token() }}',
                    name: elementName
                },
                complete: function(data) {
                    $('#alertdanger').attr('hidden', true);
                    $('#alertsuccess').attr('hidden', true);
                    currInput.removeClass(
                        'is-invalid  was-validated form-control:invalid');
                    currInput.removeClass(
                        'is-valid  was-validated form-control:valid');
                    data = data.responseJSON;
                    if (data.success) {
                        data = data.data;
                        currInput.addClass(
                            'is-invalid  was-validated form-control:invalid');
                    } else {
                        currInput.addClass(
                            'is-valid  was-validated form-control:valid');
                    }
                }
            });
        });
    </script>

    <script>
        $('.getdata').click(function() {
            $('.name').removeClass(
                'is-invalid  was-validated form-control:invalid');
            $('.name').removeClass(
                'is-valid  was-validated form-control:valid');
            // clear old
            // $('.name').val('');

            // get code details
            var currInput = $(this).closest('div').prev().children('input');
            var serialCode = currInput.val();
            $.ajax({
                type: 'post',
                dataType: "JSON",
                url: "{{ route('get-data-by-serial') }}",
                data: {
                    '_token': '{{ csrf_token() }}',
                    code: serialCode
                },
                complete: function(data) {
                    $('#alertdanger').attr('hidden', true);
                    $('#alertsuccess').attr('hidden', true);
                    currInput.removeClass(
                        'is-valid  was-validated form-control:valid');
                    currInput.removeClass(
                        'is-invalid  was-validated form-control:invalid');
                    data = data.responseJSON;
                    console.log(data);
                    if (data.success) {
                        data = data.data;
                        currInput.addClass(
                            'is-invalid  was-validated form-control:invalid');
                    } else {
                        currInput.addClass(
                            'is-valid  was-validated form-control:valid');
                    }
                }
            });
        })
    </script>

    <script>
        $('.form').submit(function(e) {
            e.preventDefault();
            var currForm = $(this);
            var formData = new FormData($(this)['0']);
            formData.append("_token", "{{ csrf_token() }}");
            formData.append('merchant_type', $('#type').val());

            var eff_materials = [];
            $('#effict_materials_table tr:not(:first)').each(function() {
                if ($(this).find("td").eq(2).html() != null) {
                    var tt = {
                        "mat_name": $(this).find("td").eq(1).html(),
                        "dose": $(this).find("td").eq(2).html()
                    };
                    eff_materials.push(tt);
                }
            });

            formData.append('eff_materials_var', JSON.stringify(eff_materials));

            var url = "{{ route('update-product', '#id') }}";
            url = url.replace('#id', "{{ $product->id }}");
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
                        window.location.href = "{{ route('product-list') }}";
                    }
                },
                'error': function(xhr, status, error) {
                    $('#alertsuccess').attr('hidden', true);
                    $('#alertdanger').attr('hidden', false);
                    $('#alertdanger').empty();
                    $('#alertdanger').append("<ul>");
                    $.each(xhr.responseJSON.errors, function(index, value) {
                        $('#alertdanger').append("<li>" + value + "</li>");
                    });
                    $('#alertdanger').append("</ul>");
                }
            });

        });
    </script>
@endpush
