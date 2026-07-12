@extends('errors::illustrated-layout')

@section('code', '503')
@section('title', __('errors.503.title'))

@section('image')
    <div style="background-image: url({{ asset('/svg/503.svg') }});"
        class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@section('message', __($exception->getMessage() ?: __('errors.503.title')))
