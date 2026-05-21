@extends('layouts.app')

@section('content')
    @livewire('agency-form', ['agencyId' => $agency->id])
@endsection