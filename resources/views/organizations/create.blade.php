@extends('layouts.app')
@section('title', 'New Organization')
@section('content')
    @include('organizations._form', ['organization' => null])
@endsection