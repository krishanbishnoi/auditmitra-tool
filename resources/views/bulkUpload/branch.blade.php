@extends('layouts.master')



@section('title', '| Bulk Upload')



@section('sh-detail')

    Create New

@endsection



@section('content')

<div class="row">

    <div class="col-md-12">

    <div class="card ">

        <div class="card-header">

            <strong>Bulk Upload</strong> 

            <a class="btn btn-primary btn-sm float-right" href="{{route('downloadBranch')}}">Download Excel Format</a>

            <div style="font-size:13px">Click to download excel sheet and re-upload sheet again with required data</div>

        </div>

        <div class="card-body card-block">

            {!! Form::open(

                     array(

                       'route' => 'bulkUpload.store',

                       'class' => 'form-horizontal',

                       'role'=>'form',

                       'data-toggle'=>"validator",

                       'files' => true)

                     ) !!}

            

            <div class="row">

                <div class="col-sm-3">

                    <div class=" form-group">

                        <label for="branch" class=" form-control-label">Upload Sheet</label>

                    </div>

                </div>

                <div class="col-sm-4">

                    <div class=" form-group">

                        <input type="file" id="branch" name="file" class="form-control-file">

                        @if(is_array(session()->get('error')))

                            @foreach(session()->get('error') as $error)

                                @if(is_array($error))

                                    @foreach($error as $err)

                                    <span class="text-danger">{{$err}}</span>

                                    @endforeach

                                @endif

                            @endforeach

                        @else

                        <span class="text-danger">{{session()->get('error')}}</span>

                        @endif

                                    

                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-md-12">

                    @if($errors->any())

                    <div class="text-danger">

                        {{-- <u>There are {{count($errors->all())}} {{count($errors->all())>1?'errors':'error'}} in your excel</u>  --}}

                        Errors

                    </div>

                    <div>

                        <ol class="ml-5">

                            @foreach ($errors->all() as $error)

                                <li class="text-danger">{{$error}}</li>

                            @endforeach

                        </ol>

                    </div>    

                    @endif

                </div>

            </div>

            

<div class="card-footer">

                <button type="submit" class="btn btn-primary btn-sm">

                    <i class="fa fa-dot-circle-o"></i> Save Sheet

                </button>

                <button type="reset" class="btn btn-danger btn-sm">

                    <i class="fa fa-ban"></i> Reset

                </button>

            </div>

            </form>

        </div>



    </div>

</div>

</div>



@endsection

@section('js')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

    <script>

        jQuery(document).ready(function() {

            jQuery('.datepicker').datepicker({

                format: "mm-yyyy",

                viewMode: "months", 

                minViewMode: "months"

            });

    })

    </script>

@endsection