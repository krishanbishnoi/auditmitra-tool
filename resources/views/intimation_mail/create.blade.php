@extends('layouts.master')
@section('sh-title')
    Audit Alert Box
@endsection



@section('sh-detail')
    Create New
@endsection
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<?php
$allocatedmodule = App\Helpers\Helper::allocatedmodulelist();
?>
@section('content')
    <div class="card" id="kt_repeater_1">
        <div class="card-header">
            <strong>Intimation Mail</strong>
        </div>

        <div class="card-body card-block">
            {!! Form::open([
                'route' => 'intimation_mail.store',
            
                'class' => 'kt-form',
            
                'role' => 'form',
            
                'data-toggle' => 'validator',
            ]) !!}

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
                                        @foreach ($cycle as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Audit Date*</label>
                                    <input type="text" id="audit_date" name="audit_date" class="form-control"
                                        placeholder="Choose Audit Date" autocomplete="off" required>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label>Select Auditor*</label>
                                    <select class="form-control auditor" name="auditor" id="collection_manager" required>
                                        <option value=''>Select Auditor</option>
                                        @foreach ($qa_list as $data)
                                            <option value="{{ $data->name }}">{{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">


                                <div class="col-md-4 form-group">
                                    <label>Agency</label>
                                    <select class="form-control agencies" name="agency" id="agencies" required>
                                        <option value=''>Choose Agency</option>
                                        @foreach ($agency as $data)
                                            <option value="{{ $data->id }}">
                                                {{ $data->agency_id }}--{{ $data->name }}--{{ $data->location }}</option>
                                            <!-- Use agency ID -->
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Agency Emails</label>
                                    {!! Form::select('agency_email[]', [], null, [
                                        'id' => 'agency_email_dropdown',
                                        'class' => 'form-control select2',
                                        'multiple' => 'multiple',
                                        'required' => 'required',
                                    ]) !!}
                                </div>



                                <div class="col-md-4 form-group">
                                    <label>Auditor Email</label>
                                    {!! Form::text('additional_email', '', ['class' => 'form-control js-example-basic-single']) !!}
                                </div>
                                @if(auth()->user()->client_id == 285)
                                <div class="col-md-4 form-group">
                                    <label>Auditor Name</label>
                                    {!! Form::text('auditor_name', old('auditor_name'), [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter Auditor Name',
                                    ]) !!}
                                </div>
                                @endif
                            </div>



                            <div class="row">
                                @if (auth()->user()->client_id == 15 || auth()->user()->client_id == 249)
                                    <div class="col-md-4 form-group">
                                        <label>Agency SPOC Name</label>
                                        {!! Form::text('agency_spoc_name', old('agency_spoc_name'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Agency SPOC Name',
                                        ]) !!}
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Agency SPOC Number</label>
                                        {!! Form::text('agency_spoc_number', old('agency_spoc_number'), [
                                            'class' => 'form-control',
                                            'pattern' => '[0-9]{10}',
                                            'placeholder' => 'Enter Agency SPOC Number',
                                        ]) !!}
                                    </div>
                                @endif
                                @if (auth()->user()->client_id == 249)
                                    <div class="col-md-4 form-group">
                                        <label>Client SPOC Name</label>
                                        {!! Form::text('client_spoc_name', old('client_spoc_name'), [
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter Client SPOC Name',
                                        ]) !!}
                                    </div>
                                @endif


                            </div>

                            <!-- <div class="row">

                                                <div class="col-md-4 form-group">
                                                    <label>Products</label>
                                                    <select class="form-control product" name="product_id" id="product" required>
                                                        <option value=''>Choose Product</option>
                                                        @foreach ($products as $data)
    <option value="{{ $data->id }}">{{ $data->name }}</option>
    @endforeach
                                                    </select>
                                                </div> -->

                            <!-- Sub Product (Attributes) Dropdown -->
                            <!-- <div class="col-md-4 form-group">
                                                    <label>Product Attributes</label>
                                                    <select class="form-control" name="product_attribute_id" id="product_attributes"
                                                        disabled>
                                                        <option value=''>Select an attribute</option>
                                                    </select>
                                                </div>
                                            </div>
                                          -->
                            <div id="intimationUsersContainer">
                                <div class="row intimationUser">
                                    <div class="col-md-3 form-group">
                                        <label>Level 3</label>
                                        {{-- {!! Form::select('levels[0][level_3]', $formattedUsers, null, ['class' => 'form-control select2']) !!} --}}
                                        {!! Form::select('levels[0][level_3]', ['' => 'Select Level 3'] + $formattedUsers, null, [
                                            'class' => 'form-control select2',
                                        ]) !!}
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Level 4</label>
                                        {{-- {!! Form::select('levels[0][level_4]', $formattedUsers, null, ['class' => 'form-control select2']) !!} --}}
                                        {!! Form::select('levels[0][level_4]', ['' => 'Select Level 4'] + $formattedUsers, null, [
                                            'class' => 'form-control select2',
                                        ]) !!}
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label>Level 5</label>
                                        {{-- {!! Form::select('levels[0][level_5]', $Level_5, null, ['class' => 'form-control select2']) !!} --}}
                                        {!! Form::select('levels[0][level_5]', ['' => 'Select Level 5'] + $formattedUsers, null, [
                                            'class' => 'form-control select2',
                                        ]) !!}
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <button type="button" id="addMoreUsers" class="btn btn-primary"
                                            style="margin-top: 11%;">Add More</button>

                                    </div>
                                </div>
                            </div>



                            @if (auth()->user()->client_id == 13)
                                <div class="col-md-4 form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" id="" name="description" rows="3"></textarea>
                                </div>
                            @endif
                            @if (in_array(21, $allocatedmodule))
                                <div class="col-md-4 form-group">
                                    <label for="mode">Mode*</label>
                                    <select name="mode" id="mode" class="form-control" required>
                                        <option value="" disabled selected>Select Mode</option>
                                        <option value="virtual">Virtual</option>
                                        <option value="physical">Physical</option>
                                    </select>
                                </div>
                        </div>
                        @endif

                        @if (auth()->user()->client_id == 285)
                            <div class="row m-3">
                                {{-- <h5>Executive Details</h5> --}}

                                <div id="executiveContainer">

                                    <div class="row executiveRow">

                                        <div class="col-md-5 form-group">
                                            <label>Executive Name</label>
                                            <input type="text" name="executives[0][name]" class="form-control"
                                                placeholder="Enter Executive Name">
                                        </div>

                                        <div class="col-md-5 form-group">
                                            <label>ICE (Unique PFL Identity)</label>
                                            <input type="text" name="executives[0][ice]" class="form-control"
                                                placeholder="Enter ICE">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <button type="button" id="addExecutive" class="btn btn-primary"
                                                style="margin-top:32px;">
                                                Add More
                                            </button>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        @endif
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

        $jq(document).ready(function() {
            // Initialize Select2 for multi-select fields with search
            $jq('.select2').select2({
                placeholder: 'Select options', // Placeholder text
                allowClear: true // Allows clearing selection
            });
        });
        $('#mode').select2({
            placeholder: 'Select Mode',
            allowClear: true
        });




        jQuery(document).ready(function() {

            jQuery('.datepicker').datepicker({

                format: "yyyy-mm-dd",

                minDate: new Date()

            });





        });



        jQuery('body').on('focus', ".datepicker", function() {

            jQuery(this).datepicker({

                format: "yyyy-mm-dd",

                minDate: new Date()

            });

        })







        $('.branchName').on('click change', function(e) {

            if (e.type === 'change' || this.id !== 'branchName') {

                var sel = this.value;

                $("#branchName option[value='" + sel + "']").attr("selected", "selected");

                //alert(this.value);

            }
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
                        url: "{{ url('product-attributes') }}/" + productId,
                        method: 'GET',
                        success: function(data) {
                            $attributesSelect.empty().append(
                                '<option value="">Select an attribute</option>');
                            if (data.length > 0) {
                                $attributesSelect.prop('disabled', false);
                                $.each(data, function(index, attribute) {
                                    $attributesSelect.append('<option value="' +
                                        attribute
                                        .id + '">' + attribute
                                        .product_attribute_name +
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

        $(document).ready(function() {
            $('#agencies').on('change', function() {
                var agencyId = $(this).val();

                if (agencyId) {
                    $.ajax({

                        url: "{{ url('get-agency-emails') }}",
                        type: 'POST',
                        data: {
                            agency_id: agencyId, // Send agency ID in POST data
                            _token: '{{ csrf_token() }}' // Include CSRF token for security
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#agency_email_dropdown').empty(); // Clear previous options
                            $('#agency_email_dropdown').append(
                                '<option value="">Choose Email</option>');
                            $.each(data, function(key, email) {
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

        $(document).ready(function() {
            var userCount = 1;

            // Get PHP variables as JSON for use in JavaScript
            var formattedUsers = @json($formattedUsers);
            var level5Users = @json($Level_5);

            // Function to generate options for a select input
            function generateOptions(options) {
                var htmlOptions = '<option value="">Select User</option>'; // Default empty option
                for (var key in options) {
                    htmlOptions += `<option value="${key}">${options[key]}</option>`;
                }
                return htmlOptions;
            }

            // Add more fields on button click
            $('#addMoreUsers').click(function() {
                var newUserFields = `
            <div class="row intimationUser">
                <div class="col-md-3 form-group">
                    <label>Level 3</label>
                    <select name="levels[` + userCount + `][level_3]" class="form-control select2">
                        ${generateOptions(formattedUsers)}
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Level 4</label>
                    <select name="levels[` + userCount + `][level_4]" class="form-control select2">
                        ${generateOptions(formattedUsers)}
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Level 5</label>
                    <select name="levels[` + userCount + `][level_5]" class="form-control select2">
                        ${generateOptions(formattedUsers)}
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <button type="button" class="btn btn-danger removeUser" style="margin-top: 11%;">Remove</button>
                </div>
            </div>
        `;
                $('#intimationUsersContainer').append(newUserFields);
                userCount++;
            });

            // Remove user set on button click
            $(document).on('click', '.removeUser', function() {
                $(this).closest('.intimationUser').remove();
            });

            var executiveCount = 1;

            jQuery(document).on('click', '#addExecutive', function() {

                var html = `
        <div class="row executiveRow">

            <div class="col-md-5 form-group">
                <input type="text"
                    name="executives[` + executiveCount + `][name]"
                    class="form-control"
                    placeholder="Enter Executive Name">
            </div>

            <div class="col-md-5 form-group">
                <input type="text"
                    name="executives[` + executiveCount + `][ice]"
                    class="form-control"
                    placeholder="Enter ICE">
            </div>

            <div class="col-md-2 form-group">
                <button type="button"
                    class="btn btn-danger removeExecutive">
                    Remove
                </button>
            </div>

        </div>
    `;

                jQuery('#executiveContainer').append(html);

                executiveCount++;
            });

            jQuery(document).on('click', '.removeExecutive', function() {

                jQuery(this).closest('.executiveRow').remove();

            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#audit_date", {
                dateFormat: "Y-m-d",
                disableMobile: true,
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const dateObj = dayElem.dateObj;
                    // getDay(): 0 = Sunday, 1 = Monday, ..., 6 = Saturday
                    if (dateObj.getDay() === 0) {
                        dayElem.classList.add('flatpickr-sunday');
                        dayElem.setAttribute('title', 'Sunday');
                    }
                }
            });

            $('.select2').select2({
                placeholder: 'Select',
                allowClear: true
            });

            // Add more users logic...
            // (Keep your existing user add/remove code here)
        });
    </script>



    @include('shared.form_js')
@endsection
