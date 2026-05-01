@extends('errors::illustrated-layout')

@section('code', '404')
@section('title', __('errors.404.title'))

@section('image')
    <img
        src="{{ asset('/svg/404.svg') }}"
        alt="{{ __('errors.404.title') }}"
        class="max-h-full max-w-full object-contain p-6 opacity-90 sm:p-8 dark:opacity-80"
        loading="lazy"
    >
@endsection

@section('message', __('errors.404.message'))
