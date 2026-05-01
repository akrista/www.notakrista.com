@extends('errors::illustrated-layout')

@section('code', '503')
@section('title', __('errors.503.title'))

@section('image')
    <img
        src="{{ asset('/svg/503.svg') }}"
        alt="{{ __('errors.503.title') }}"
        class="max-h-full max-w-full object-contain p-6 opacity-90 sm:p-8 dark:opacity-80"
        loading="lazy"
    >
@endsection

@section('message', __($exception->getMessage() ?: __('errors.503.message')))
