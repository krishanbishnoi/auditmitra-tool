@extends('layouts.master')

@section('sh-title')
QM - Sheet
@endsection

@section('sh-detail')
Create New
@endsection

@section('content')

<div class="card">
  <div class="card-header">
    <strong>Create Sheet</strong>
  </div>

  <div class="card-body card-block">
    {!! Form::open(['route' => 'legal.qm_sheet.store', 'class' => 'form-horizontal', 'role'=>'form', 'data-toggle'=>"validator"]) !!}

    {{-- Show only for Super Admin --}}
    @role('Super Admin') {{-- Spatie Role Check --}}
    <div class="row form-group">
      <div class="col col-md-3"><label class="form-control-label">Client*</label></div>
      <div class="col-12 col-md-9">
        {!! Form::select('client_id', $clients, null, ['placeholder' => 'Choose Client', 'class' => 'form-control', 'required']) !!}
      </div>
    </div>
    @endrole

    {{-- OR use this if you're not using Spatie --}}
    {{-- @if(Auth::user() && Auth::user()->role === 'Super Admin')
    <div class="row form-group">
      <div class="col col-md-3"><label class="form-control-label">Client*</label></div>
      <div class="col-12 col-md-9">
        {!! Form::select('client_id', $clients, null, ['placeholder' => 'Choose Client', 'class' => 'form-control', 'required']) !!}
      </div>
    </div>
    @endif --}}

    {{-- <div class="row form-group">
      <div class="col col-md-3"><label class="form-control-label">Lob Name*</label></div>
      <div class="col-12 col-md-9">
        {!! Form::select('lob', [
            '' => 'Choose Lob Name',
            'collection' => 'Collection',
            'commercial_vehicle' => 'Commercial Vehicle',
            'rural' => 'Rural',
            'alliance' => 'Alliance',
            'credit_card' => 'Credit Card'
          ], null, ['class' => 'form-control','required' => 'required']) !!}
      </div>
    </div> --}}

    <div class="row form-group">
      <div class="col col-md-3"><label class="form-control-label">Sheet Type*</label></div>
      <div class="col-12 col-md-9">
        {!! Form::select('type', [
            '' => 'Choose Sheet Type',
            'legal' => 'Legal',
          ], null, ['class' => 'form-control' ,'required' => 'required']) !!}
      </div>
    </div>

    <div class="row form-group">
      <div class="col col-md-3"><label class="form-control-label">Sheet Name*</label></div>
      <div class="col-12 col-md-9">
        <input type="text" name="name" class="form-control" required>
      </div>
    </div>

    <div class="row form-group">
      <div class="col col-md-3"><label class="form-control-label">Details</label></div>
      <div class="col-12 col-md-9">
        <textarea class="form-control" name="details"></textarea>
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary btn-sm">
        <i class="fa fa-dot-circle-o"></i> Create
      </button>
      <button type="reset" class="btn btn-danger btn-sm">
        <i class="fa fa-ban"></i> Reset
      </button>
    </div>

    {!! Form::close() !!}
  </div>
</div>

@endsection
