@extends('layouts.general')

@section('path_content')
    @if(sizeof($pathMenu) != 0)
        @foreach($pathMenu as $path)
            <li class="breadcrumb-item active">
                <a href="{{$path['lien']}}" class="kt-subheader__breadcrumbs-link">{{$path['titre']}}</a>
            </li>
        @endforeach
    @endif
@stop

@section('content')
    <div class="col-lg-12">
        <div class="card" id="ticketsList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"><i class="{{$icone}} m-2"></i>{{$titre}}</h5>
                    <div class="flex-shrink-0">
                        @if(in_array('add_user', session('InfosAction')))
                            <a class="btn btn-primary btn-label right rounded-pill" href="{{route('users.create')}}">
                                <i class="ri-add-line label-icon align-middle fs-16 ms-2 rounded-pill"></i>Ajouter
                            </a>
                        @endif
                        @if(in_array('exporter_user', session('InfosAction')))
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary">Exporter</button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuReference" data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuReference">
                                    <li><a class="dropdown-item exporterXls" target="_blank" href="#">Excel</a></li>
                                    <li><a class="dropdown-item exporterPdf" target="_blank" href="#">PDF</a></li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                {!! Form::open(['id' => 'formSearch', 'method' => 'GET']) !!}
                    <div class="row gy-4">
                        <div class="col-xxl-3 col-md-4">
                            <div>
                                <label for="placeholderInput" class="form-label">Rechercher</label>
                                {!! Form::text('query', '', ['id' => 'SearchUSer', 'class' => 'form-control search', 'onkeyup' => 'funcRecher()', 'autocomplete' => 'off', 'placeholder' => 'Rechercher...']) !!}
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                @if(session()->has('success') || session()->has('error'))
                    <div class="col-md-12 mt-2">
                        <div class="alert {{ session()->has('success') ? 'alert-success' : ''}} {{ session()->has('error') ? 'alert-danger' : ''}} alert-border-left alert-dismissible fade show" role="alert">
                            <i title="{{session()->has('errorMsg') ? session()->get('errorMsg') : '' }}" class="{{session()->has('success') ? 'ri-notification-off-line' : 'ri-error-warning-line'}} me-3 align-middle"></i>
                            <strong>Infos</strong> - {{ session()->has('success') ? session()->get('success') : session()->get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card-body">
                <div id="dataRefresh" class="table-responsive table-card mb-4 m-2 giwuRefresh">
                    @include('users.index-search')
                </div>
            </div>
        </div><!--end card-->
    </div><!--end col-->

    <!-- delete -->
    <div>
        <div class="modal fade bs-example-modal-center" id="kt_delete_4" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">

            </div>
        </div>
    </div>
    <!-- delete -->
@endsection

@section('JS_content')
    <script src="{{url('assets/js/jquery.min.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(".exporterXls").attr('href', '{{url("users/exporterExcel")}}');
            $(".exporterPdf").attr('href', '{{url("users/exporterPdf")}}');

            $(document).on("click", ".pagination a", function(event){
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetch_page(page);
            });

            function fetch_page(page){
                var dr = $("#formSearch").serialize();
                $.ajax({
                    url: "users?page=" + page,
                    data: dr,
                    type: 'GET',
                    success: function(data) {
                        $('#dataRefresh').html(data);
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                    }
                });
            }
        });

        function funcRecher(){
            var filtreData = $("#formSearch").serialize();
            $(".exporterXls").attr('href', '{{url("users/exporterExcel")}}?' + filtreData);
            $(".exporterPdf").attr('href', '{{url("users/exporterPdf")}}?' + filtreData);
            $("div#dataRefresh").html('<h3 class="col-xs-12 text-center kt-subheader__title" style="padding-top: 3em;">' +
                                        '<span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"></span></span></h3>');
            return $.ajax({
                url: '{{ url("/users/") }}',
                data: filtreData,
                type: 'GET',
                success: function(e) {
                    $('#dataRefresh').html(e);
                },
                error: function() {
                    $('#dataRefresh').html('<div class="alert alert-danger" role="alert">Erreur dans la recherche!</div>');
                },
            });
        };

        $(document).on('click', '.btn-delete', function () {
            var id = $(this).data("id");
            $.ajax({
                url: '{{ url("users/AffichePopDelete/") }}/' + id,
                type: 'GET',
                dataType: 'html',
                success: function(code_html) {
                    $("#kt_delete_4 .modal-dialog").html(code_html);
                    $("#kt_delete_4").modal('show');
                }
            });
        });
    </script>
@endsection
