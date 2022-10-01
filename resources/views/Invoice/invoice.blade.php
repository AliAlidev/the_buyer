<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>The Buyer Invoice</title>
    <style>
        body {
            font-family: "Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace !important;
            letter-spacing: -0.3px;
        }

        .invoice-wrapper {
            width: 700px;
            margin: auto;
        }

        .nav-sidebar .nav-header:not(:first-of-type) {
            padding: 1.7rem 0rem .5rem;
        }

        .logo {
            font-size: 50px;
        }

        .sidebar-collapse .brand-link .brand-image {
            margin-top: -33px;
        }

        .content-wrapper {
            margin: auto !important;
        }

        .billing-company-image {
            width: 50px;
        }

        .billing_name {
            text-transform: uppercase;
        }

        .billing_address {
            text-transform: capitalize;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 10px;
        }

        td {
            padding: 10px;
            vertical-align: top;
        }

        .row {
            display: block;
            clear: both;
        }

        .text-right {
            text-align: right;
        }

        .table-hover thead tr {
            background: #eee;
        }

        .table-hover tbody tr:nth-child(even) {
            background: #fbf9f9;
        }

        address {
            font-style: normal;
        }
    </style>
</head>

<body>
    <div class="row invoice-wrapper">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <td>
                                <h4>
                                    <span class="">The Buyer</span>
                                </h4>
                            </td>
                            <td class="text-right">
                                <strong>Date: {{ now()->format('D M Y') }}</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <br><br>
            <div class="row invoice-info">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <td>
                                <div class="">
                                    From
                                    <address>
                                        <strong>{{ strtoupper($from) }}</strong><br>
                                    </address>
                                </div>
                            </td>
                            <td>
                                <div class="">
                                    To
                                    <address>
                                        <strong class="billing_name">{{ strtoupper($customer) }}</strong><br>
                                    </address>
                                </div>
                            </td>
                            <td>
                                <div class="text-right">
                                    Order Number <b> {{ $invoice->order_number }}</b><br>
                                    Paid for <b>{{ $invoice_type }}</b>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Parts Quantity</th>
                                <th>Price</th>
                                <th>Part Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->invoiceItems as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->data->name }}</td>
                                    <td style="text-align: center">{{ $item->quantity }}</td>
                                    <td style="text-align: center">{{ $item->quantity_parts }}</td>
                                    <td style="text-align: center">{{ $item->price }}</td>
                                    <td style="text-align: center">{{ $item->price_part }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5" class="text-right">Total</td>
                                <td class="text-right"><strong> {{ $invoice->total_amount }} SP</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">Discount Value</td>
                                <td class="text-right">
                                    <strong>{{ $invoice->discount_type == 2 ? $invoice->discount . ' %' : ($invoice->discount_type == 1 ? $invoice->discount . ' SP' : '0 SP') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">Total Pay</td>
                                <td class="text-right"><strong> {{ $invoice->paid_amount }} SP</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <br><br><br>
            <div>
                <small><small>NOTE: This is system generate invoice no need of signature</small></small>
            </div>
        </div>
    </div>
</body>

</html>
