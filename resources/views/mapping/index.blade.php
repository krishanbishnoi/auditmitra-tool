@extends('layouts.master')

@section('content')

<select id="location" class="form-control">
    <option value="">Select Location</option>

    @foreach($locations as $location)
        <option value="{{ $location }}">
            {{ $location }}
        </option>
    @endforeach
</select>

<br>

<select id="campus_type" class="form-control">
    <option value="">Select Campus Type</option>

    @foreach($campusTypes as $type)
        <option value="{{ $type }}">
            {{ $type }}
        </option>
    @endforeach
</select>

<br>

<select id="pillar" class="form-control">
    <option value="">Select Pillar</option>
</select>

<br>

<select id="touch_points" class="form-control">
    <option value="">Select Touch Point</option>
</select>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

$(document).ready(function () {

    // Get Pillars
    $('#location, #campus_type').change(function () {

        let location = $('#location').val();
        let campus_type = $('#campus_type').val();

        if(location != '' && campus_type != '') {

            $.ajax({
                url: '/get-pillars',
                type: 'POST',

                data: {
                    _token: '{{ csrf_token() }}',
                    location: location,
                    campus_type: campus_type
                },

                success: function (response) {

                    $('#pillar').html(
                        '<option value="">Select Pillar</option>'
                    );

                    $('#touch_points').html(
                        '<option value="">Select Touch Point</option>'
                    );

                    response.forEach(function(item) {

                        $('#pillar').append(
                            `<option value="${item}">${item}</option>`
                        );

                    });

                }
            });

        }

    });


    // Get Touch Points
    $('#pillar').change(function () {

        $.ajax({
            url: '/get-touch-points',
            type: 'POST',

            data: {
                _token: '{{ csrf_token() }}',
                location: $('#location').val(),
                campus_type: $('#campus_type').val(),
                pillar: $('#pillar').val()
            },

            success: function (response) {

                $('#touch_points').html(
                    '<option value="">Select Touch Point</option>'
                );

                response.forEach(function(item) {

                    $('#touch_points').append(
                        `<option value="${item}">${item}</option>`
                    );

                });

            }
        });

    });

});

</script>

@endsection