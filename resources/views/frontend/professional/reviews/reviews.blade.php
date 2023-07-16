@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'reviews'])

<link rel="stylesheet" href="{{ asset('css/proreviews.css') }}">
@section('content')

    {{-- Breadcrumbs --}}
    @component('components.breadcrumbs', [
        'title' => 'Reviews',
        'separator' => true,
    ])
    @endcomponent

    @component('components.modal_builder', [
        'modal_id' => 'reportModal',
        'hasHeader' => true,
        'rawHeader' =>
            '<h5 class="modal-title" id="reportModalLabel"><i class="fa-regular fa-triangle-exclamation" style="color: #ae1313;"></i> Reportar esta Crítica</h5>',
        'hasBody' => true,
        'inputs' => [
            [
                'label' => 'Razão:',
                'type' => 'text',
                'id' => 'reason',
                'placeholder' => 'Porque esta a reportar esta crítica?',
                'isTextarea' => true,
            ],
            ['label' => '', 'type' => 'hidden', 'id' => 'reviewID'],
        ],
        'hasFooter' => true,
        'buttons' => [
            ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true],
            ['label' => 'Guardar', 'id' => 'save', 'class' => 'btn btn-primary'],
        ],
    ])
    @endcomponent

    <div class="container">
        @foreach ($reviews as $rev)
            <div class="review mt-4">
                <i style="float: right" onclick="report({{ $rev['id'] }})"
                    class="fa-regular fa-triangle-exclamation report-ico"></i>
                <div class="r-header">
                    <img src="{{ asset('img/pfp/' . $rev['pfp']) }}"
                        onerror="this.src = '{{ asset('img/pfp/defaultpfp.png') }}';" class="r-pfp" id="userIco">
                    <span style="font-size: 20px">{{ $rev['first_name'] . ' ' . $rev['last_name'] }}</span>
                </div>
                <div class="stars mt-3">
                    @for ($i = 0; $i < $rev['stars']; $i++)
                        <i class="fa-solid fa-star rw-st"></i>
                    @endfor
                    <span style="float: right">{{ $rev['written_at'] }}</span>
                </div>
                <hr>
                <div class="r-title">
                    <h3>{{ $rev['title'] }}</h3>
                </div>
                <div class="r-b">
                    <p style="width: 66vw">{{ $rev['review'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
    <br><br>

    <script>
        function report(id) {
            $("#reviewID").val(id);
            $("#reason").val("");
            $("#reportModal").modal("toggle");
        }

        $("#save").on('click', () => {
            $.ajax({
                method: "post",
                url: "/professional/criticas/reportar",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "review": $("#reviewID").val(),
                    "description": $("#reason").val()
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $("#reportModal").modal("toggle");
            }).fail((err) => {
                errorToast(res.responseJSON.title, res.responseJSON.message);
            });
        })

        $("#closeMdl").on('click', ()=>{
            $("#reason").val("");
        })
    </script>
@stop
