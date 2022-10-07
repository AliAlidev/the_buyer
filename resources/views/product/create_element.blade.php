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
                            <h4>Create Element</h4>
                        </div>
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <div id="alertdanger" class="alert alert-danger" hidden>
                                </div>
                                <div id="alertsuccess" class="alert alert-success" hidden>
                                </div>
                                <form id="form1" method="POST">
                                    @csrf
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-md-2">
                                            <label for="type">Merchant Type</label>
                                            <select name="type" id="type" class="form-control">
                                                <option value="1">Market</option>
                                                <option value="2">Pharmacy</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="market_section" hidden>

                                    </div>
                                    <div id="pharmacy_section" hidden>
                                        <div class="row mt-3 d-flex justify-content-center">
                                            <div class="col-md-8 mt-3">
                                                {{-- code --}}
                                                <div class="row">
                                                    <label for="result">Code</label>
                                                    <div class="col-md-10">
                                                        <input class="form-control" id="result" type="text"
                                                            name="code" value="{{ old('code') }}"
                                                            placeholder="SCAN CODE">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input id="getdata" type="button" class="btn btn-primary"
                                                            value="Check">
                                                    </div>
                                                </div>
                                                {{-- name --}}
                                                <div class="row">
                                                    <div class="col-md-12 mt-3">
                                                        <label for="name"> Element Name</label>
                                                        <input id="name" name="name" value="{{ old('name') }}"
                                                            type="text" class="form-control"
                                                            placeholder="ENTER ELEMENT NAME" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    {{-- dose --}}
                                                    <div class="col-md-3">
                                                        <label class="mt-3" for="">Dose</label>
                                                        <input id="dose" class="form-control" type="text"
                                                            value="{{ old('dose') }}" name="dose" required
                                                            placeholder="DOSE">
                                                    </div>
                                                    {{-- tab count --}}
                                                    <div class="col-md-3">
                                                        <label class="mt-3" for="">Tab Count</label>
                                                        <input id="tab_count" class="form-control" type="text"
                                                            value="{{ old('tab_count') }}" name="tab_count" required
                                                            placeholder="Tab Count">
                                                    </div>
                                                    {{-- shape --}}
                                                    <div class="col-md-3">
                                                        <label class="mt-3" for="">Shape</label>
                                                        <select name="shape" id="shape" class="form-control">
                                                            <option value=""></option>
                                                            @foreach ($shapes as $shape)
                                                                <option value="{{ $shape->shape_id }}">
                                                                    {{ $shape->ar_shape_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{-- company --}}
                                                    <div class="col-md-3">
                                                        <label class="mt-3" for="">Company</label>
                                                        <select name="company" id="company" class="form-control">
                                                            <option value=""></option>
                                                            @foreach ($companies as $company)
                                                                <option value="{{ $company->comp_id }}">
                                                                    {{ $company->ar_comp_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    {{-- treatement group --}}
                                                    <div class="col-md-4">
                                                        <label class="mt-3" for="">Treatement Group</label>
                                                        <select name="treatement_group" id="treatement_group"
                                                            class="form-control">
                                                            <option value=""></option>
                                                            @foreach ($treatement_groups as $group)
                                                                <option value="{{ $group->tg_id }}">
                                                                    {{ $group->ar_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label class="form-check-label mt-3"
                                                            for="flexSwitchCheckDefault">Has
                                                            Parts</label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="hasparts" name="hasparts">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4" id="numofpartsdiv" hidden>
                                                        <label class="form-check-label mt-3"
                                                            for="flexSwitchCheckDefault">Parts
                                                            Number</label>
                                                        <input id="numofparts" type="number" class="form-control"
                                                            value="{{ old('numofparts') != null ? old('numofparts') : 0 }}"
                                                            name="numofparts">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="mt-3" for="">Description</label>
                                                        <textarea id="description" class="form-control" name="description" id="" cols="30" rows="10">{{ old('description') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <br />
                                                <button class="btn btn-primary" type="button"
                                                    id="submitForm">Add</button>
                                            </div>
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
        $('#type').change(function() {
            merchantType($(this).val());
        });

        $(document).ready(function() {
            merchantType(2);
        });

        function merchantType(merchant) {
            if (merchant == 1) {
                $('#pharmacy_section').attr('hidden', 'hidden');
                $('#market_section').removeAttr('hidden');
            } else if (merchant == 2) {
                $('#pharmacy_section').removeAttr('hidden');
                $('#market_section').attr('hidden', 'hidden');
            }
        }
    </script>

    <script>
        $('#hasparts').change(function() {
            if ($(this).is(':checked')) {
                $('#numofpartsdiv').removeAttr('hidden');
            } else {
                $('#numofpartsdiv').attr('hidden', 'hidden');
            }
        });
    </script>

    <script>
        $("#name").autocomplete({
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
        $('#name').keyup(function(e) {
            if (e.keyCode == 13) {
                $('#result').removeClass(
                    'is-invalid  was-validated form-control:invalid');
                $('#result').removeClass(
                    'is-valid  was-validated form-control:valid');

                // clear old
                $('#result').val('');
                $('#quantity').val('0');
                $('#quantityparts').val('0');
                $('#price').val('0');
                $('#partprice').val('0');
                $('#expiry_date').val('');
                $('#description').val('');
                var elementName = $(this).val();
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
                        $('#name').removeClass(
                            'is-invalid  was-validated form-control:invalid');
                        $('#name').removeClass(
                            'is-valid  was-validated form-control:valid');
                        data = data.responseJSON;
                        if (data.success) {
                            data = data.data;
                            $('#name').addClass(
                                'is-invalid  was-validated form-control:invalid');
                        } else {
                            $('#name').addClass(
                                'is-valid  was-validated form-control:valid');
                        }
                    }
                });
            }
        });
    </script>

    <script>
        $('#getdata').click(function() {
            $('#name').removeClass(
                'is-invalid  was-validated form-control:invalid');
            $('#name').removeClass(
                'is-valid  was-validated form-control:valid');
            // clear old
            $('#name').val('');
            $('#quantity').val('0');
            $('#quantityparts').val('0');
            $('#price').val('0');
            $('#partprice').val('0');
            $('#expiry_date').val('');
            $('#description').val('');

            // get code details
            var serialCode = $('#result').val();
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
                    $('#result').removeClass(
                        'is-valid  was-validated form-control:valid');
                    $('#result').removeClass(
                        'is-invalid  was-validated form-control:invalid');
                    data = data.responseJSON;
                    console.log(data);
                    if (data.success) {
                        data = data.data;
                        $('#result').addClass(
                            'is-invalid  was-validated form-control:invalid');
                    } else {
                        $('#result').addClass(
                            'is-valid  was-validated form-control:valid');
                    }
                }
            });
        })
    </script>

    <script>
        $('#submitForm').click(function(e) {
            var data = $('form').serialize();
            $.post("{{ route('create') }}", data).done(function(value) {
                if (value.success) {
                    $('#alertdanger').attr('hidden', true);
                    $('#alertsuccess').attr('hidden', false);
                    $('#alertsuccess').empty();
                    $('#alertsuccess').append(value.message);
                    $("#form1")[0].reset();
                    $('#result').removeClass(
                        'is-valid  was-validated form-control:valid');
                    $('#result').removeClass(
                        'is-invalid  was-validated form-control:invalid');
                    $('#name').removeClass(
                        'is-valid  was-validated form-control:valid');
                    $('#name').removeClass(
                        'is-invalid  was-validated form-control:invalid');

                    $('#numofpartsdiv').attr('hidden', 'hidden');
                }
            }).fail(function(xhr, status, error) {
                $('#alertsuccess').attr('hidden', true);
                $('#alertdanger').attr('hidden', false);
                $('#alertdanger').empty();
                $('#alertdanger').append("<ul>");
                $.each(xhr.responseJSON.errors, function(index, value) {
                    $('#alertdanger').append("<li>" + value + "</li>");
                });
                $('#alertdanger').append("</ul>");
            });
        });

        $('#form1').submit(function() {
            e.preventDefault();
        });
    </script>
@endpush
