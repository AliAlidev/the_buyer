@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="page-title">
                        <h4 class="mb-0 font-size-18">Starter Page</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Agroxa</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                            <li class="breadcrumb-item active">Starter Page</li>
                        </ol>
                    </div>

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

        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Demo purpose only -->
                            <div style="min-height: 300px;">
                                <p>Main</p>
                                <input id="open_camera" type="button" class="btn btn-primary" value="Open Cam">
                                <input id="close_camera" type="button" class="btn btn-primary" value="Close Cam">
                                <form method="POST" action="{{ route('webcam.capture') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div id="my_camera"></div>
                                            <br />
                                            <input type=button value="Take Snapshot" onClick="take_snapshot()">
                                            <input type="hidden" name="image" class="image-tag">
                                        </div>
                                        <div class="col-md-6">
                                            <div id="results">Your captured image will appear here...</div>
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <br />
                                            <button class="btn btn-success">Submit</button>
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
    <script language="JavaScript">
        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
            });
        }
    </script>

    <script>
        $('#open_camera').click(function() {
            function onScanSuccess(decodedText, decodedResult) {
                console.log(`Code scanned = ${decodedText}`, decodedResult);
            }
            var html5QrcodeScanner = new Html5QrcodeScanner(
                "my_camera", {
                    fps: 10,
                    qrbox: 250
                });
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>

    <script>
        $('#close_camera').click(function() {
            Webcam.reset();
        });
    </script>
@endpush
