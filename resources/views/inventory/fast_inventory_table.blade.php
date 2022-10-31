<style>
    .overlay {
        margin: auto;
        position: absolute;
        top: 50px;
        left: 50px;
        bottom: 50px;
        right: 50px;
        z-index: 100000;
    }

    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }

    .add_button {
        background-color: #1b82ec;
        border-color: #1b82ec;
        color: white;
    }

    .add_button:hover {
        background-color: #0080ff;
        border-color: #0080ff;
        border: solid 1px white;
    }

    .edti_button {
        background-color: #f5b225;
        border-color: #f5b225;
        color: whitesmoke;
    }

    .edti_button:hover {
        background-color: #fcad02;
        border-color: #fcad02;
        border: solid 1px white;
    }
</style>

<div id="load" style="position: relative;">
    <div class="row mt-5">
        <div class="col-md-3">
            <input type="text" name="search_input" id="search_input" class="form-control"
                value="{{ request()->search_input }}"
                placeholder="{{ __('inventory/inventory.labels.input_value_and_press_enter') }}">
        </div>
        <div class="col-md-8"></div>
        <div class="col-md-1">
            <select name="length_select" id="length_select" class="form-control">
                <option {{ isset(request()->length_select) ? (request()->length_select == 25 ? 'selected' : '') : '' }}
                    value="25">25</option>
                <option {{ isset(request()->length_select) ? (request()->length_select == 50 ? 'selected' : '') : '' }}
                    value="50">50</option>
                <option {{ isset(request()->length_select) ? (request()->length_select == 100 ? 'selected' : '') : '' }}
                    value="100">100</option>
            </select>
        </div>
    </div>
    <table class="table table-bordered table-responsive table-hover mt-2" style="width: 100%;">
        <thead>
            <tr>
                <th style="text-align: center; width: 10%">{{ __('inventory/inventory.labels.table_header_id') }}</th>
                <th style="text-align: center; width: 15%">{{ __('inventory/inventory.labels.code') }}</th>
                <th style="text-align: center; width: 20%">{{ __('inventory/inventory.labels.name') }}</th>
                <th style="text-align: center; width: 10%">{{ __('inventory/inventory.labels.quantity') }}</th>
                <th style="text-align: center; width: 10%">{{ __('inventory/inventory.labels.price') }}</th>
                <th style="text-align: center; width: 10%">{{ __('inventory/inventory.labels.quantity_p') }}</th>
                <th style="text-align: center; width: 10%">{{ __('inventory/inventory.labels.price_p') }}</th>
                <th style="text-align: center; width: 10%">{{ __('inventory/inventory.labels.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $item)
                <div hidden>
                    {{ $inentory_amount = $item->amountsForUser()->where('amount_type', '0')->first() }}
                </div>
                <tr>
                    <td style="text-align: center">
                        {{ (request()->page > 1 ? (request()->page - 1) * (isset(request()->length_select) ? request()->length_select : 25) : 0) + $key + 1 }}
                    </td>
                    <td style="text-align: center">{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td><input type="number" class="form-control  w-100"
                            value="{{ $inentory_amount != null ? $inentory_amount->amount : 0 }}"></td>
                    <td><input type="number" class="form-control  w-100"
                            value="{{ $inentory_amount != null ? $inentory_amount->price : 0 }}"></td>
                    <td><input type="number" class="form-control  w-100"
                            value="{{ $inentory_amount != null ? $inentory_amount->amount_part : 0 }}"></td>
                    <td><input type="number" class="form-control  w-100"
                            value="{{ $inentory_amount != null ? $inentory_amount->price_part : 0 }}"></td>
                    @if ($inentory_amount != null)
                        <td><a href="{{ route('edit-item-in-fast-initilize-store', $inentory_amount->id) }}"
                                class="btn  btn-sm mt-2 btn_edit edti_button">{{ __('inventory/inventory.labels.edit') }}</a>
                        </td>
                    @else
                        <td><a href="{{ route('save-item-in-fast-initilize-store') }}"
                                class="btn btn-sm mt-2 btn_add add_button">{{ __('inventory/inventory.labels.add') }}</a>
                        </td>
                    @endif
                </tr>
            @endforeach

        </tbody>
    </table>
    <div class="row mt-2">
        <div class="col-md-12 d-flex justify-content-end">
            @if ($data->currentPage() != $data->lastPage())
                <small>Showed: {{ $data->perPage() * $data->currentPage() }} of Total: {{ $data->total() }}</small>
            @else
                <small>Showed: {{ $data->total() }} of Total: {{ $data->total() }}</small>
            @endif
        </div>
    </div>
</div>
{{ $data->links() }}

<div class="pagination">
</div>

<script>
    $('.pagination a').click(function(e) {
        e.preventDefault();
        $('#load').addClass('disabledDiv');
        $('#load').append(
            '<img class="overlay" src="{{ asset('assets/images/loading.gif') }}" />'
        );
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            data: {
                'merchant_id': $('#merchant_email').val(),
                'length_select': $('#length_select').val(),
                'search_input': $('#search_input').val()
            },
            success: function(result) {
                $('#table_data').html(result);
                window.history.pushState("", "", url);
            }
        });
    })

    $('#length_select').change(function(e) {
        e.preventDefault();
        $('#load').addClass('disabledDiv');
        $('#load').append(
            '<img class="overlay" src="{{ asset('assets/images/loading.gif') }}" />'
        );
        var url = "{{ route('fast-initilize-store') }}";
        $.ajax({
            url: url,
            data: {
                'merchant_id': $('#merchant_email').val(),
                'length_select': $('#length_select').val(),
                'search_input': $('#search_input').val()
            },
            success: function(result) {
                $('#table_data').html(result);
                window.history.pushState("", "", url);
            }
        });
    });

    $('#search_input').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $('#load').addClass('disabledDiv');
            $('#load').append(
                '<img class="overlay" src="{{ asset('assets/images/loading.gif') }}" />'
            );
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                data: {
                    'merchant_id': $('#merchant_email').val(),
                    'length_select': $('#length_select').val(),
                    'search_input': $('#search_input').val()
                },
                success: function(result) {
                    $('#table_data').html(result);
                    window.history.pushState("", "", url);
                }
            });
        }
    });
</script>
