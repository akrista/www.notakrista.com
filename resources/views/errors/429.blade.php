@extends('errors::illustrated-layout')

@section('code', '429')
@section('title', __('errors.429.title'))

@section('image')
    <img
        src="{{ asset('/svg/403.svg') }}"
        alt="{{ __('errors.429.title') }}"
        class="max-h-full max-w-full object-contain p-6 opacity-90 sm:p-8 dark:opacity-80"
        loading="lazy"
    >
@endsection

@section('message', __('errors.429.message'))
