@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <h2>Welcome, {{ auth()->user()->name }}</h2>

@endsection