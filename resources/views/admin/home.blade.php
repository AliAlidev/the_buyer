@extends('layouts.main')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                {{-- <div class="page-title">
                    <h4 class="mb-0 font-size-18">Sidebar with User</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">The Buyer</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Layouts</a></li>
                        <li class="breadcrumb-item active">Sidebar with User</li>
                    </ol>
                </div> --}}

                <div class="state-information d-none d-sm-block">
                    <div class="state-graph">
                        <div id="header-chart-1"></div>
                        <div class="info">Balance $ 2,317</div>
                    </div>
                    <div class="state-graph">
                        <div id="header-chart-2"></div>
                        <div class="info">Item Sold 1230</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Start page content-wrapper -->
    <div class="page-content-wrapper">
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-info mini-stat position-relative" style="height: 80%">
                    <div class="card-body">
                        <div class="mini-stat-desc">
                            <h5 class="text-uppercase verti-label font-size-16 text-white-50">{{ __('home/home.orders') }}
                            </h5>
                            <div class="text-white">
                                <h5 class="text-uppercase font-size-16 text-white-50">{{ __('home/home.orders_count') }}
                                </h5>
                                <h3 class="mb-3 text-white">{{ $orders }}</h3>
                            </div>
                            <div class="text-white">
                                <h5 class="text-uppercase font-size-16 text-white-50">{{ __('home/home.orders_amount') }}
                                </h5>
                                <h3 class="mb-3 text-white">{{ $total_paid }}</h3>
                            </div>
                            <div class="mini-stat-icon">
                                <i class="mdi mdi-cube-outline display-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Col -->

            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary mini-stat position-relative" style="height: 80%">
                    <div class="card-body">
                        <div class="mini-stat-desc">
                            <h5 class="text-uppercase font-size-16 verti-label text-white-50">
                                {{ __('home/home.sell_orders') }}
                            </h5>
                            <div class="text-white">
                                <h5 class="text-uppercase font-size-16 text-white-50">
                                    {{ __('home/home.sell_orders_count') }}</h5>
                                <h3 class="mb-3 text-white">{{ $sell_orders }}</h3>
                            </div>
                            <div class="text-white">
                                <h5 class="text-uppercase font-size-16 text-white-50">
                                    {{ __('home/home.sell_orders_amount') }}</h5>
                                <h3 class="mb-3 text-white">{{ $sell_orders_amount }}</h3>
                            </div>
                            <div class="mini-stat-icon">
                                <i class="mdi mdi-buffer display-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Col -->

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning mini-stat position-relative" style="height: 80%">
                    <div class="card-body">
                        <div class="mini-stat-desc">
                            <h5 class="text-uppercase verti-label font-size-16  text-white-50">
                                {{ __('home/home.buy_orders') }}
                            </h5>
                            <div class="text-white">
                                <h5 class="text-uppercase font-size-16  text-white-50">
                                    {{ __('home/home.buy_orders_count') }}</h5>
                                <h3 class="mb-3 text-white">{{ $buy_orders }}</h3>
                            </div>
                            <div class="text-white">
                                <h5 class="text-uppercase font-size-16  text-white-50">
                                    {{ __('home/home.buy_orders_amount') }}</h5>
                                <h3 class="mb-3 text-white">{{ $buy_orders_amount }}</h3>
                            </div>
                            <div class="mini-stat-icon">
                                <i class="mdi mdi-tag-text-outline display-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Col -->

            {{-- <div class="col-xl-3 col-md-6">
                <div class="card bg-success mini-stat position-relative">
                    <div class="card-body">
                        <div class="mini-stat-desc">
                            <h5 class="text-uppercase font-size-16  verti-label text-white-50">Pr. Sold
                            </h5>
                            <div class="text-white">
                                <h5 class="text-uppercase font-size-16 text-white-50">Product Sold
                                </h5>
                                <h3 class="mb-3 text-white">1890</h3>
                                <div class="">
                                    <span class="badge bg-light text-info"> +89% </span> <span class="ms-2">From previous
                                        period</span>
                                </div>
                            </div>
                            <div class="mini-stat-icon">
                                <i class="mdi mdi-briefcase-check display-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <!-- End Col -->
        </div>
        <!-- End Row -->

        <div class="row">
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 border-end">
                                <h4 class="card-title mb-4">{{ __('home/home.sale_report') }}</h4>
                                <div id="morris-area-example" class="dashboard-charts morris-charts">
                                </div>
                            </div>
                            <!-- End Col -->

                            <div class="col-xl-12">
                                <h4 class="card-title">{{ __('home/home.monthly_sales_report') }}</h4>
                                <div class="p-3">
                                    <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                                        @foreach ($monthly_invoices_tabs as $key => $item)
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-{{ $key }}-tab"
                                                    data-bs-toggle="pill" href="#pills-{{ $key }}" role="tab"
                                                    aria-controls="pills-{{ $key }}"
                                                    aria-selected="true">{{ $month_values[number_format($item->months) - 1] }}</a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content">
                                        @foreach ($monthly_invoices_tabs as $key => $item)
                                            <div class="tab-pane" id="pills-{{ $key }}" role="tabpanel"
                                                aria-labelledby="pills-{{ $key }}-tab">
                                                <div class="p-3">
                                                    <h2>{{ number_format($item->total_paid, 2) }}
                                                        {{ __('home/home.sp') }}</h2>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <!-- End Col -->
                        </div>
                        <!-- end row -->
                    </div>
                </div>
            </div>
            <!-- end col -->

            <div class="col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">{{ __('home/home.sales_analytics') }}</h4>
                        <div id="morris-donut-example" class="dashboard-charts morris-charts"></div>
                    </div>
                </div>
                <!-- End Card -->
            </div>
            <!-- End Col -->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Inbox</h4>
                        <div data-simplebar style="max-height: 334px;">
                            <div class="inbox-wid">
                                <a href="#" class="text-dark">
                                    <div class="inbox-item">
                                        <div class="inbox-item-img float-start me-3"><img
                                                src="assets/images/users/avatar-1.jpg" class="avatar-md rounded-circle"
                                                alt=""></div>
                                        <h6 class="inbox-item-author mb-1 text-dark">Irene</h6>
                                        <p class="inbox-item-text text-muted mb-0">Hey! there I'm
                                            available...</p>
                                        <p class="inbox-item-date text-muted">13:40 PM</p>
                                    </div>
                                </a>
                                <a href="#" class="text-dark">
                                    <div class="inbox-item">
                                        <div class="inbox-item-img float-start me-3"><img
                                                src="assets/images/users/avatar-2.jpg" class="avatar-md rounded-circle"
                                                alt=""></div>
                                        <h6 class="inbox-item-author mb-1 text-dark">Jennifer</h6>
                                        <p class="inbox-item-text text-muted mb-0">I've finished it!
                                            See
                                            you
                                            so...</p>
                                        <p class="inbox-item-date text-muted">13:34 PM</p>
                                    </div>
                                </a>
                                <a href="#" class="text-dark">
                                    <div class="inbox-item">
                                        <div class="inbox-item-img float-start me-3"><img
                                                src="assets/images/users/avatar-3.jpg" class="avatar-md rounded-circle"
                                                alt=""></div>
                                        <h6 class="inbox-item-author mb-1 text-dark">Richard</h6>
                                        <p class="inbox-item-text text-muted mb-0">This theme is
                                            awesome!
                                        </p>
                                        <p class="inbox-item-date text-muted">13:17 PM</p>
                                    </div>
                                </a>
                                <a href="#" class="text-dark">
                                    <div class="inbox-item">
                                        <div class="inbox-item-img float-start me-3"><img
                                                src="assets/images/users/avatar-4.jpg" class="avatar-md rounded-circle"
                                                alt=""></div>
                                        <h6 class="inbox-item-author mb-1 text-dark">Martin</h6>
                                        <p class="inbox-item-text text-muted mb-0">Nice to meet you</p>
                                        <p class="inbox-item-date text-muted">12:20 PM</p>
                                    </div>
                                </a>
                                <a href="#" class="text-dark">
                                    <div class="inbox-item">
                                        <div class="inbox-item-img float-start me-3"><img
                                                src="assets/images/users/avatar-5.jpg" class="avatar-md rounded-circle"
                                                alt=""></div>
                                        <h6 class="inbox-item-author mb-1 text-dark">Sean</h6>
                                        <p class="inbox-item-text text-muted mb-0">Hey! there I'm
                                            available...</p>
                                        <p class="inbox-item-date text-muted">11:47 AM</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Col -->

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">{{ __('home/home.top_product_sales') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    @foreach ($top_products_sales as $item)
                                        <tr>
                                            <td>
                                                <h5 class="font-size-16">{{ $item->name }}</h5>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="peity-pie" data-peity='{ "fill": ["#1b82ec", "#f2f2f2"]}'
                                                        data-width="54" data-height="54">{{ $item->count }}/
                                                        {{ $total_sales_count }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <h5 class="font-size-16">
                                                    {{ number_format(($item->count / $total_sales_count) * 100, 2) }}%</h5>
                                                <p class="text-muted mb-0">{{ __('home/home.sales') }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Col -->
        </div>
        <!-- end row -->

        <div class="row">

            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">{{ __('home/home.latest_order') }}</h4>
                        <div class="table-responsive order-table">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">(#) {{ __('home/home.id') }}</th>
                                        <th scope="col">{{ __('home/home.name') }}</th>
                                        <th scope="col">{{ __('home/home.date_time') }}</th>
                                        <th scope="col">{{ __('home/home.amount') }}</th>
                                        <th scope="col" colspan="2">{{ __('home/home.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($git_last_orders as $item)
                                        <tr>
                                            <th scope="row">#{{ $item->order_number }}</th>
                                            <td>
                                                <div>
                                                    <img src="assets/images/users/avatar-4.jpg" alt=""
                                                        class="avatar-md rounded-circle me-2">
                                                    {{ $item->customer != null ? $item->customer->name : '' }}
                                                </div>
                                            </td>
                                            <td>{{ date_format($item->created_at, 'Y-m-d') }}
                                                <p class="font-size-13 text-muted mb-0">
                                                    {{ date_format($item->created_at, 'H:m:s A') }}</p>
                                            </td>
                                            <td>{{ $item->paid_amount }} {{ __('home/home.sp') }}</td>
                                            <td>
                                                @if ($item->payment_type == '1')
                                                    <span class="badge bg-success rounded-pill" style="padding-right: 5%; padding-left: 15%"><i
                                                            class="mdi mdi-checkbox-blank-circle text-success"></i>
                                                        {{ __('home/home.paid') }}</span>
                                                @elseif ($item->payment_type == '3')
                                                    <span class="badge bg-warning rounded-pill" style="padding-right: 5%; padding-left: 15%"><i
                                                            class="mdi mdi-checkbox-blank-circle text-warning"></i>
                                                        {{ __('home/home.indebted') }}</span>
                                                @else
                                                    <span class="badge bg-primary rounded-pill" style="padding-right: 5%; padding-left: 15%"><i
                                                            class="mdi mdi-checkbox-blank-circle text-primary"></i>
                                                        {{ __('home/home.free') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <a href="{{ route('view.invoice', $item->order_number) }}" target="_blank" class="btn btn-info btn-sm">{{ __('home/home.show') }}</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- end page-content-wrapper-->
@endsection

@push('scripts')
    {{-- area chart --}}
    <script>
        $(document).ready(function() {
            var data = JSON.parse("{{ $monthly_invoices }}".replace(/&quot;/g, '"'));
            console.log(data);
            // createAreaChart(element, pointSize, lineWidth, data, xkey, ykeys, labels, resize, gridLineColor, hideHover, lineColors, fillOpacity, behaveLikeLine)
            createAreaChart(
                "morris-area-example",
                0,
                0,
                data,
                "months",
                ["total_paid"],
                ["{{ __('home/home.sales') }}"],
                ["#f5b225"]
            );
        });
        var months = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"];

        function createAreaChart(e, t, a, i, r, o, n, c) {
            Morris.Area({
                element: e,
                pointSize: 0,
                lineWidth: 0,
                data: i,
                xkey: r,
                ykeys: o,
                labels: n,
                xLabelFormat: function(x) { // <--- x.getMonth() returns valid index
                    var month = months[x.getMonth()];
                    return month;
                },
                dateFormat: function(x) {
                    var month = months[new Date(x).getMonth()];
                    return month;
                },
                resize: !0,
                gridLineColor: "#eeee",
                hideHover: "auto",
                lineColors: c,
                fillOpacity: 0.7,
                behaveLikeLine: !0,
            });
        }
    </script>

    {{-- donat chart --}}
    <script>
        function createDonutChart(e, t, a) {
            Morris.Donut({
                element: e,
                data: t,
                resize: !0,
                colors: a
            });
        }
        $(document).ready(function() {
            createDonutChart(
                "morris-donut-example",
                [{
                        label: "{{ __('home/home.cash_sales') }}",
                        value: "{{ $cash_sell_orders }}"
                    },
                    {
                        label: "{{ __('home/home.dept_sales') }}",
                        value: "{{ $debt_sell_orders }}"
                    },
                    {
                        label: "{{ __('home/home.free_sales') }}",
                        value: "{{ $free_sell_orders }}"
                    }
                ],
                ["#f5b225", "#f0f1f4", "#1b82ec"]
            );
        });
    </script>
@endpush
