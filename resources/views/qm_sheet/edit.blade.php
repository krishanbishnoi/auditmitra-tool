@extends('layouts.master')

@section('sh-title')
QM - Sheet
@endsection

@section('sh-detail')
Edit
@endsection

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <strong>Edit Sheet</strong>
      </div>
      <div class="card-body card-block">

        {!! Form::model($data, [
            'method' => 'PATCH',
            'url' => 'qm_sheet/' . Crypt::encrypt($data->id),
            'class' => 'kt-form',
            'data-toggle' => "validator"
        ]) !!}
          @role('Super Admin')
        <div class="row form-group">
          <div class="col col-md-3">
            <label for="client-select" class="form-control-label">Client*</label>
          </div>
          <div class="col-12 col-md-9">
            {!! Form::select('client_id', $clients, $data->client_id ?? null, [
                'id' => 'client-select',
                'class' => 'form-control',
                'placeholder' => 'Choose Client'
            ]) !!}
          </div>
        </div>
        @endrole

        <div class="row form-group">
          <div class="col col-md-3">
            <label for="lob-select" class="form-control-label">Lob Name*</label>
          </div>
          <div class="col-12 col-md-9">
            <select name="lob" class="form-control" id="lob-select">
              <option value="">Choose Lob Name</option>
              <option value="collection" {{ ($data->lob == 'collection') ? 'selected' : '' }}>Collection</option>
              <option value="commercial_vehicle" {{ ($data->lob == 'commercial_vehicle') ? 'selected' : '' }}>Commercial Vehicle</option>
              <option value="rural" {{ ($data->lob == 'rural') ? 'selected' : '' }}>Rural</option>
              <option value="alliance" {{ ($data->lob == 'alliance') ? 'selected' : '' }}>Alliance</option>
              <option value="credit_card" {{ ($data->lob == 'credit_card') ? 'selected' : '' }}>Credit Card</option>
            </select>
          </div>
        </div>

        <div class="row form-group">
          <div class="col col-md-3">
            <label for="sheet-name" class="form-control-label">Sheet Name*</label>
          </div>
          <div class="col-12 col-md-9">
            <input type="text" id="sheet-name" name="name" class="form-control" required value="{{ $data->name }}">
          </div>
        </div>

        <div class="row form-group">
          <div class="col col-md-3">
            <label for="sheet-type" class="form-control-label">Sheet Type*</label>
          </div>
          <div class="col-12 col-md-9">
            <select name="type" class="form-control" id="sheet-type">
              <option value="">Choose Sheet Type</option>
              <option value="branch" {{ ($data->type == 'branch') ? 'selected' : '' }}>Branch</option>
              <option value="agency" {{ ($data->type == 'agency') ? 'selected' : '' }}>{{$agencyLabel}}</option>
              <option value="branch_repo" {{ ($data->type == 'branch_repo') ? 'selected' : '' }}>Branch Repo</option>
              <option value="agency_repo" {{ ($data->type == 'agency_repo') ? 'selected' : '' }}>{{$agencyLabel}} Repo</option>
              <option value="repo_yard" {{ ($data->type == 'repo_yard') ? 'selected' : '' }}>Repo and Yard</option>
            </select>
          </div>
        </div>

        <div class="row form-group">
          <div class="col col-md-3">
            <label for="details" class="form-control-label">Details</label>
          </div>
          <div class="col-12 col-md-9">
            <textarea class="form-control" name="details" id="details">{{ $data->details }}</textarea>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa fa-dot-circle-o"></i> Submit
          </button>
          <button type="reset" class="btn btn-danger btn-sm">
            <i class="fa fa-ban"></i> Reset
          </button>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
@include('shared.form_js')
@endsection
