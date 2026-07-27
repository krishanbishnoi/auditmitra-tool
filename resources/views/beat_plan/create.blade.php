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

        <strong>Beat Plan</strong>

    </div>

    <div class="card-body card-block">



        {!! Form::open(

    array(

        'route' => 'beat_plan.store',

        'class' => 'kt-form',

        'role' => 'form',

        'data-toggle' => "validator"
    )

) !!}

        <!--begin::Form-->



        <div class="row">

            <div class="col-md-3 form-group">

                <label>Name*</label>

                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Beat plan name"
                    required>

            </div>
        </div>

        <div id="kt_repeater_1">

            <div class="form-group  row" id="kt_repeater_1">

                <div data-repeater-list="subs" class="col-lg-12">

                    <div data-repeater-item class="form-group align-items-center">

                        <div class="row">



                            <div class="col-md-3 form-group">

                                <label>From Date*</label>

                                <input type="text" id="date" name="date" class="form-control datepicker"
                                    placeholder="Choose From dates" autocomplete="off" required>

                            </div>



                            <div class="col-md-3 form-group">

                                <label>To Date*</label>

                                <input type="text" id="to_date" name="to_date" class="form-control datepicker"
                                    placeholder="Choose To dates" autocomplete="off" required>

                            </div>


                            <div class="col-md-3 form-group">

                                <label>Agency</label>

                                <select class="form-control agencies" name="agencies" id="agencies" required>

                                    <option value=''>--Choose Agency--</option>

                                    @foreach($agency as $data)

                                        <option value="{{$data->id}}">{{$data->name}}</option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-3 form-group">

                                <label>Products</label>

                                <select class="form-control product" name="product" id="product" required>

                                    <option value=''>--Choose Product--</option>

                                    @foreach($product as $data)

                                        <option value="{{$data->id}}">{{$data->name}}</option>

                                    @endforeach

                                </select>

                            </div>



                            <div class="col-md-3 form-group">

                                <label>Collection Manager*</label>

                                <select class="form-control collection_manager" name="collection_manager"
                                    id="collection_manager" required>

                                    <option value=''>--Choose Collection Manager--</option>
                                    @foreach($users as $data)

                                        <option value="{{$data->id}}">{{$data->name}}</option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-3 form-group">

                                <label>Description</label>

                                <textarea class="form-control" id="" name="description"></textarea>

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







<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

{{--
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script> --}}





<script>
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

@include('shared.form_js')

@endsection