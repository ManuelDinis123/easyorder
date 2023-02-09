@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')
    @include("components.frontend.restaurantstop", ["selected" => "main"])

    <h1>main page</h1>
@stop
