@extends('layouts.master')

@section('sh-title')
Audit Alert Box
@endsection

@section('sh-detail')
Create New
@endsection

@section('content')
<div class="card" id="kt_repeater_1">
    <div class="card-header">
        <strong>Edit Intimation Mail</strong>
    </div>

     <div class="card-body card-block">
        {!! Form::model($data, [

            'route' => ['intimation_mail.update', Crypt::encrypt($data->id)],
            'method' => 'PUT',
            'class' => 'kt-form',
            'role' => 'form',
            'data-toggle' => 'validator'
        ]) !!}

        <!--begin::Form -->

        <div id="kt_repeater_1">
            <div class="form-group row" id="kt_repeater_1">
                <div  class="col-lg-12">
                    <div data-repeater-item class="form-group align-items-center">
                        <div class="row">
                            <div class="col-md-4 form-group">
                              <label for="name">@lang('Agency Name')*</label>
                                <select id="name" name="name" class="form-control" required>
                                  <option value="" disabled {{ old('name', $data->name) === '' ? 'selected' : '' }}>
                                      @lang('Select Name')
                                  </option>
                                  <option value="Qdegrees 1" {{ old('name', $data->name) === 'Qdegrees 1' ? 'selected' : '' }}>
                                      Qdegrees 1
                                  </option>
                                  <option value="Qdegrees 2" {{ old('name', $data->name) === 'Qdegrees 2' ? 'selected' : '' }}>
                                      Qdegrees 2
                                  </option>
                                  <option value="Qdegrees 3" {{ old('name', $data->name) === 'Qdegrees 3' ? 'selected' : '' }}>
                                      Qdegrees 3
                                  </option>
                                </select>
                              @error('name')
                                  <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                          </div>


                            <div class="col-md-4 form-group">
                                <label for="month">@lang('Process Review Month')*</label>
                                <select id="month" name="process_review_month" class="form-control" required>
                                    <option value="" disabled {{ old('process_review_month', $data->process_review_month ?? '') === '' ? 'selected' : '' }}>
                                        @lang('Select Month')
                                    </option>
                                    @foreach($months as $month)
                                        <option value="{{ $month['value'] }}" {{ old('process_review_month', $data->process_review_month) === $month['value'] ? 'selected' : '' }}>
                                            {{ $month['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('process_review_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Audit Date*</label>
                                <input type="text" id="date" name="audit_date" class="form-control datepicker" placeholder="Choose Audit Dates" autocomplete="off" value="{{ old('audit_date', $data->audit_date) }}" required>
                            </div>

                        </div>

                        <div class="row">

                           <div class="col-md-4 form-group">
                                <label>Select Auditor*</label>
                                <select class="form-control auditor" name="auditor" id="collection_manager" required>
                                    <option value=''>Select Auditor</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->name }}" {{ $user->name == $data->auditor ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Agency</label>
                                <select class="form-control agencies" name="agency" id="agencies" required>
                                    <option value=''>Choose Agency</option>
                                    @foreach($agency as $agency)
                                        <option value="{{ $agency->name }}" data-email="{{ $agency->email }}" {{ $agency->name == $data->agency ? 'selected' : '' }}>{{ $agency->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Agency Email</label>
                                <input type="text" class="form-control" name="email" id="email" value="{{ old('email', $data->email) }}" readonly>
                            </div>

                            
                        </div>

                        <div class="row">

                            <div class="col-md-4 form-group">
                                <label>Collection Manager*</label>
                                <select class="form-control collection_manager" name="collection_manager" id="collection_manager" required>
                                    <option value=''>Select Collection Manager</option>
                                    @foreach($collection_manager as $manager)
                                        <option value="{{ $manager->name }}" {{ $manager->name == $data->collection_manager ? 'selected' : '' }}>{{ $manager->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Products</label>
                                <select class="form-control product" name="product_id" id="product" required>
                                    <option value=''>Choose Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $product->id == $data->product_id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sub Product (Attributes) Dropdown -->
                            <div class="col-md-4 form-group">
                                <label>Product Attributes</label>
                                <select class="form-control" name="product_attribute_id" id="product_attributes">
                                    <option value=''>Select an attribute</option>
                                    @foreach($attributes as $attribute)
                                        <option value="{{ $attribute->id }}" {{ $attribute->id == $data->product_attribute_id ? 'selected' : '' }}>{{ $attribute->product_attribute_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                          <div class="col-md-4 form-group">
                              <label>Description</label>
                              <textarea class="form-control" name="description" rows="3">{{ old('description', $data->description) }}</textarea>
                          </div>
                         </div> 
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-dot-circle-o"></i> Update
            </button>
            <button href="{{ route('intimation_mail.index') }}" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Cancel
            </button>
        </div>

        {!! Form::close() !!}
        <!--end::Form-->

    </div>
</div>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
  jQuery(document).ready(function () {
    jQuery('.datepicker').datepicker({
      format: "yyyy-mm-dd",
      minDate: new Date()
    });
  })

  jQuery('body').on('focus', ".datepicker", function () {
    jQuery(this).datepicker({
      format: "yyyy-mm-dd",
      minDate: new Date()
    });
  });
</script>

<!-- V Show Agency email on behalf of Agency -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const agenciesSelect = document.getElementById('agencies');
        const emailInput = document.getElementById('email');
        
        agenciesSelect.addEventListener('change', function() {
            const selectedOption = agenciesSelect.options[agenciesSelect.selectedIndex];
            const email = selectedOption.getAttribute('data-email');
            emailInput.value = email || ''; // Set the email input value or clear it if no email
        });
    });
</script>

<!-- V Show Product Attributes as sub product on behalf of Product -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#product').change(function() {
            var productId = $(this).val();
            var $attributesSelect = $('#product_attributes');

            if (productId) {
                $.ajax({
                    url: '/product-attributes/' + productId,
                    method: 'GET',
                    success: function(data) {
                        $attributesSelect.empty().append('<option value="">Select an attribute</option>');
                        if (data.length > 0) {
                            $attributesSelect.prop('disabled', false);
                            $.each(data, function(index, attribute) {
                                $attributesSelect.append('<option value="' + attribute.id + '">' + attribute.product_attribute_name + '</option>');
                            });
                        } else {
                            $attributesSelect.prop('disabled', true);
                        }
                    }
                });
            } else {
                $attributesSelect.empty().append('<option value="">Select an attribute</option>').prop('disabled', true);
            }
        });
    });
</script>

@include('shared.form_js')
@endsection