@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')
    @include('components.frontend.restaurantstop', ['selected' => 'publication'])

    <div class="container">
        <div class="allPublications">
            @if (count($posts) == 0)
                <div class="center">
                    <span class="unselectable" style="color: rgb(228, 228, 228)">Este restaurante ainda não tem publicações!</span>
                </div>
            @endif
            @foreach ($posts as $key => $post)
                <div class="post">
                    <div class="center">
                        <h2>{{ $post['title'] }} <i class="fa-solid {{ $key == 0 ? 'fa-eye-slash' : 'fa-eye' }} seeMore"
                                id="show{{ $post['id'] }}" onclick="seeMore({{ $post['id'] }})"
                                style="font-size: 24px;"></i></h2>
                    </div>
                    <div class="{{ $key == 0 ? '' : 'visually-hidden' }}" id="allPost{{ $post['id'] }}">
                        <hr>
                        <div class="post-body" id="body{{ $post['id'] }}">
                            {!! html_entity_decode($post['body']) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function seeMore(id) {
            if ($("#allPost" + id).hasClass('visually-hidden')) {
                $("#allPost" + id).removeClass('visually-hidden');
                $("#show" + id).removeClass('fa-eye');
                $("#show" + id).addClass('fa-eye-slash');
                return;
            }
            $("#allPost" + id).addClass('visually-hidden');
            $("#show" + id).removeClass('fa-eye-slash');
            $("#show" + id).addClass('fa-eye');
        }
    </script>

@stop
