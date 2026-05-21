@extends('layouts.app')

@section('content')
    @livewire('rfq-form', ['rfqId' => $rfq->id])
@endsection