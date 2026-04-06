@extends('layouts.app')
@section('title', 'Edit Organization')
@section('content')
    @include('organizations.form', ['organization' => $organization])
@endsection