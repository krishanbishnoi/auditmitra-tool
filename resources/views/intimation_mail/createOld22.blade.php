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
        <strong>Intimation Mail</strong>
    </div>

    <div class="card-body card-block">
        {!! Form::open(

    array(

        'route' => 'intimation_mail.store',

        'class' => 'kt-form',

        'role' => 'form',

        'data-toggle' => "validator"
    )

) !!}

        <!--begin::Form-->


        <div id="kt_repeater_1">
            <div class="form-group  row" id="kt_repeater_1">
                <div class="col-lg-12">
                    <div data-repeater-item class="form-group align-items-center">


                        <div class="row">
                            <!-- <div class="col-md-4 form-group">
                                <label>Agency Name*</label>
                                <select id="agency" name="name" class="form-control" required>
                                    <option value="" disabled selected>Select Agency</option>
                                    <option value="Qdegrees 1">Qdegrees 1</option>
                                    <option value="Qdegrees 2">Qdegrees 2</option>
                                    <option value="Qdegrees 3">Qdegrees 3</option>
                                </select>
                            </div> -->

                            <div class="col-md-4 form-group">
                                <label for="month">Process Review Month*</label>
                                <select id="month" name="process_review_month" class="form-control" required>
                                    <option value="" disabled selected>Select Month</option>
                                    @foreach($months as $month)
                                        <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Audit Date*</label>
                                <input type="text" id="date" name="audit_date" class="form-control datepicker"
                                    placeholder="Choose Audit Dates" autocomplete="off" required>
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Select Auditor*</label>
                                <select class="form-control auditor" name="auditor" id="collection_manager" required>
                                    <option value=''>Select Auditor</option>
                                    @foreach($qa_list as $data)
                                        <option value="{{$data->name}}">{{$data->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">


                            <div class="col-md-4 form-group">
                                <label>Agency</label>
                                <select class="form-control agencies" name="agency" id="agencies" required>
                                    <option value=''>Choose Agency</option>
                                    @foreach($agency as $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option> <!-- Use agency ID -->
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Agency Emails</label>
                                {!! Form::select('agency_email[]', [], null, ['id' => 'agency_email_dropdown', 'class' => 'form-control select2', 'multiple' => 'multiple', 'required' => 'required']) !!}
                            </div>



                            <div class="col-md-4 form-group">
                                <label>Add Additional Agency Email</label>
                                {!! Form::text('additional_email', '', ['class' => 'form-control js-example-basic-single']) !!}
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-4 form-group">
                                <label>Products</label>
                                <select class="form-control product" name="product_id" id="product" required>
                                    <option value=''>Choose Product</option>
                                    @foreach($products as $data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sub Product (Attributes) Dropdown -->
                            <div class="col-md-4 form-group">
                                <label>Product Attributes</label>
                                <select class="form-control" name="product_attribute_id" id="product_attributes"
                                    disabled>
                                    <option value=''>Select an attribute</option>
                                </select>
                            </div>


                            <div class="col-md-4 form-group">
                                <label>Level 3</label>
                                {!! Form::select('level_3[]', $formattedUsers, null, ['id' => 'collection_manager-select[]', 'class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Level 4</label>
                                {!! Form::select('level_4[]', $formattedUsers, null, ['id' => 'level_4[]', 'class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Level 5</label>
                                {!! Form::select('level_5[]', $Level_5, null, ['id' => 'level_5[]', 'class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                            </div>


                            <div class="col-md-4 form-group">
                                <label>Description</label>
                                <textarea class="form-control" id="" name="description" rows="3"></textarea>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

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

    </form>



    <!--end::Form-->

</div>



<!--end::Portlet-->







</div>

</div>







@endsection



@section('js')



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Updated jQuery version -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script> -->

<script>
    var $jq = jQuery.noConflict(); // Avoid conflict with other libraries

    $jq(document).ready(function () {
        // Initialize Select2 for multi-select fields with search
        $jq('.select2').select2({
            placeholder: 'Select options', // Placeholder text
            allowClear: true                // Allows clearing selection
        });
    });



    jQuery(document).ready(function () {

        jQuery('.datepicker').datepicker({

            format: "yyyy-mm-dd",

            minDate: new Date()

        });





    });



    jQuery('body').on('focus', ".datepicker", function () {

        jQuery(this).datepicker({

            format: "yyyy-mm-dd",

            minDate: new Date()

        });

    })







    $('.branchName').on('click change', function (e) {

        if (e.type === 'change' || this.id !== 'branchName') {

            var sel = this.value;

            $("#branchName option[value='" + sel + "']").attr("selected", "selected");

            //alert(this.value);

        }
    });
</script>

<!-- V Show Agency email on behalf of Agency -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const agenciesSelect = document.getElementById('agencies');
        const emailInput = document.getElementById('email');

        agenciesSelect.addEventListener('change', function () {
            const selectedOption = agenciesSelect.options[agenciesSelect.selectedIndex];
            const email = selectedOption.getAttribute('data-email');
            emailInput.value = email || ''; // Set the email input value or clear it if no email
        });
    });
</script>

<!-- V Show Product Attributes as sub product on behalf of Product -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#product').change(function () {
            var productId = $(this).val();
            var $attributesSelect = $('#product_attributes');

            if (productId) {
                $.ajax({
                    url: "{{url('product-attributes')}}/" + productId,
                    method: 'GET',
                    success: function (data) {
                        $attributesSelect.empty().append(
                            '<option value="">Select an attribute</option>');
                        if (data.length > 0) {
                            $attributesSelect.prop('disabled', false);
                            $.each(data, function (index, attribute) {
                                $attributesSelect.append('<option value="' + attribute
                                    .id + '">' + attribute.product_attribute_name +
                                    '</option>');
                            });
                        } else {
                            $attributesSelect.prop('disabled', true);
                        }
                    }
                });
            } else {
                $attributesSelect.empty().append('<option value="">Select an attribute</option>').prop(
                    'disabled', true);
            }
        });
    });

    $(document).ready(function () {
        $('#agencies').on('change', function () {
            var agencyId = $(this).val();

            if (agencyId) {
                $.ajax({

                    url: "{{url('get-agency-emails')}}",
                    type: 'POST',
                    data: {
                        agency_id: agencyId, // Send agency ID in POST data
                        _token: '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    dataType: 'json',
                    success: function (data) {
                        $('#agency_email_dropdown').empty(); // Clear previous options
                        $('#agency_email_dropdown').append(
                            '<option value="">Choose Email</option>');
                        $.each(data, function (key, email) {
                            $('#agency_email_dropdown').append('<option value="' +
                                email + '">' + email + '</option>');
                        });
                    }
                });
            } else {
                $('#agency_email_dropdown').empty().append('<option value="">Choose Email</option>');
            }
        });

    });



</script>

@include('shared.form_js')

@endsection