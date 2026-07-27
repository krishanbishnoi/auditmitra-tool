@extends('layouts.master')

@section('content')
    <div class="container">

        <h3>Select Mapping</h3>

        <div class="row">

            <!-- Location -->
            <div class="col-md-3">

                <label>Location</label>

                <select id="location" class="form-control">

                    <option value="">Select Location</option>

                    @foreach ($locations as $location)
                        <option value="{{ $location }}">
                            {{ $location }}
                        </option>
                    @endforeach

                </select>

            </div>


            <!-- Campus Type -->
            <div class="col-md-3">

                <label>Campus Type</label>

                <select id="campus_type" class="form-control">

                    <option value="">Select Campus Type</option>

                    @foreach ($campusTypes as $type)
                        <option value="{{ $type }}">
                            {{ $type }}
                        </option>
                    @endforeach

                </select>

            </div>


            <!-- Brand -->
            <div class="col-md-3" id="brandDiv" style="display:none;">

                <label>Brand</label>

                <select id="brand" class="form-control">

                    <option value="">Select Brand</option>

                    @foreach ($brands as $brand)
                        <option value="{{ $brand }}">

                            {{ $brand }}

                        </option>
                    @endforeach

                </select>

            </div>


            <!-- Pillar -->
            <div class="col-md-3">

                <label>Pillar</label>

                <select id="pillar" class="form-control">

                    <option value="">Select Pillar</option>

                </select>

            </div>


            <!-- Touch Point -->
            <div class="col-md-3 mt-3">

                <label>Touch Point</label>

                <select id="touch_points" class="form-control">

                    <option value="">Select Touch Point</option>

                </select>

            </div>

        </div>

        <br>

        <button id="nextBtn" class="btn btn-primary">

            Next

        </button>

        <hr>

        <!-- Final Data -->
        <div id="parameterList"></div>

    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {

            // Show Hide Brand Dropdown
            $('#campus_type').change(function() {

                let campusType = $(this).val();

                if (campusType == 'Brand') {
                    $('#brandDiv').show();
                } else {
                    $('#brandDiv').hide();

                    $('#brand').val('');
                }

                $('#pillar').html(
                    '<option value="">Select Pillar</option>'
                );

                $('#touch_points').html(
                    '<option value="">Select Touch Point</option>'
                );

            });



            // Get Pillars
            $('#location, #campus_type, #brand').change(function() {

                let location = $('#location').val();

                let campus_type = $('#campus_type').val();

                let brand = $('#brand').val();

                // If Brand campus type selected
                // but brand not selected yet
                if (campus_type == 'Brand' && brand == '') {
                    return;
                }

                if (location != '' && campus_type != '') {
                    $.ajax({

                        url: '/get-pillars',

                        type: 'POST',

                        data: {

                            _token: '{{ csrf_token() }}',

                            location: location,

                            campus_type: campus_type,

                            brand: brand

                        },

                        success: function(response) {

                            $('#pillar').html(
                                '<option value="">Select Pillar</option>'
                            );

                            $('#touch_points').html(
                                '<option value="">Select Touch Point</option>'
                            );

                            response.forEach(function(item) {

                                $('#pillar').append(

                                    `<option value="${item}">
                                ${item}
                            </option>`

                                );

                            });

                        }

                    });
                }

            });



            // Get Touch Points
            $('#pillar').change(function() {

                $.ajax({

                    url: '/get-touch-points',

                    type: 'POST',

                    data: {

                        _token: '{{ csrf_token() }}',

                        location: $('#location').val(),

                        campus_type: $('#campus_type').val(),

                        brand: $('#brand').val(),

                        pillar: $('#pillar').val()

                    },

                    success: function(response) {

                        $('#touch_points').html(
                            '<option value="">Select Touch Point</option>'
                        );

                        response.forEach(function(item) {

                            $('#touch_points').append(

                                `<option value="${item}">
                            ${item}
                        </option>`

                            );

                        });

                    }

                });

            });




            // Final Parameters
            $('#nextBtn').click(function() {

                $.ajax({

                    url: '/get-final-parameters',

                    type: 'POST',

                    data: {

                        _token: '{{ csrf_token() }}',

                        location: $('#location').val(),

                        campus_type: $('#campus_type').val(),

                        brand: $('#brand').val(),

                        pillar: $('#pillar').val(),

                        touch_points: $('#touch_points').val()

                    },

                    success: function(response) {

                        let html = '';

                        // No Data
                        if (response.status == false) {
                            html += `

                        <div class="alert alert-danger">

                            No Parameters Found

                        </div>

                    `;
                        } else {
                            // Mapping Details
                            html += `

                        <h4>Mapping Details</h4>

                        <table class="table table-bordered">

                            <tr>
                                <th>Mapping ID</th>
                                <td>${response.mapping_details.mapping_id}</td>
                            </tr>

                            <tr>
                                <th>Location</th>
                                <td>${response.mapping_details.location}</td>
                            </tr>

                            <tr>
                                <th>Campus Type</th>
                                <td>${response.mapping_details.campus_type}</td>
                            </tr>

                            <tr>
                                <th>Brand</th>
                                <td>${response.mapping_details.brand ?? '-'}</td>
                            </tr>

                            <tr>
                                <th>Pillar</th>
                                <td>${response.mapping_details.pillar}</td>
                            </tr>

                            <tr>
                                <th>Touch Point</th>
                                <td>${response.mapping_details.touch_points}</td>
                            </tr>

                        </table>

                        <hr>

                        <h4>Parameters</h4>

                    `;


                            // Parameters Loop
                            response.parameters.forEach(function(item) {

                                html += `

                            <div class="card p-3 mb-4">

                                <h5 class="mb-3">

                                    ${item.parameter}
                                    (${item.id})

                                </h5>

                                <table class="table table-bordered table-striped">

                        `;


                                // Show All Parameter Fields
                                Object.keys(item).forEach(function(key) {

                                    if (key != 'qm_sheet_sub_parameter') {
                                        html += `

                                    <tr>

                                        <th>${key}</th>

                                        <td>${item[key]}</td>

                                    </tr>

                                `;
                                    }

                                });

                                html += `</table>`;


                                // Sub Parameters
                                if (item.qm_sheet_sub_parameter &&
                                    item.qm_sheet_sub_parameter.length > 0) {
                                    html += `

                                <h6>Sub Parameters</h6>

                            `;

                                    item.qm_sheet_sub_parameter.forEach(function(sub) {

                                        html += `

                                    <div class="card p-2 mb-3">

                                        <table class="table table-bordered">

                                `;

                                        Object.keys(sub).forEach(function(
                                            subKey) {

                                            html += `

                                        <tr>

                                            <th>${subKey}</th>

                                            <td>${sub[subKey]}</td>

                                        </tr>

                                    `;

                                        });

                                        html += `

                                        </table>

                                    </div>

                                `;

                                    });

                                } else {
                                    html += `

                                <p>No Sub Parameters Found</p>

                            `;
                                }

                                html += `</div>`;

                            });

                        }

                        $('#parameterList').html(html);

                    }

                });

            });

        });
    </script>
@endsection
