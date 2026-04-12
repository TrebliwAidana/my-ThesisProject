@extends('layouts.app')
@section('title', 'New Organization')
@section('content')
    @include('organizations.form', ['organization' => null])
@endsection