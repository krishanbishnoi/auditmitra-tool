@extends('layouts.master')

@section('title', '| Users')

@section('sh-detail')
    Create New
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <strong>Create User Hierarchy</strong>
            <a class="btn btn-primary btn-sm float-right" style="margin-right: 5px" href="{{route('userhierarchy.index')}}">User Heirarchy List</a>

        </div>
        <div class="card-body card-block">
            {!! Form::open(
                     array(
                       'route' => 'userhierarchy.store',
                       'class' => 'form-horizontal',
                       'role'=>'form',
                       'data-toggle'=>"validator")
                     ) !!}

            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class="form-control-label">Select Collection Manager</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('cm_id', $col_manager_roles, '', ['class' => 'form-control', 'placeholder' =>'Select Collection Manager']) !!}
                </div>
            </div>

             
            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class="form-control-label">Select Quality Auditor</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('qa_id', $quality_auditor_roles ?? [], '', ['class' => 'form-control', 'placeholder' => 'Select Quality Auditor']) !!}  
                </div>
            </div>

            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class=" form-control-label">Select Quality Control</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('qc_id', $quality_control_roles, '', ['class' => 'form-control', 'placeholder' =>'Select Quality Control']) !!}
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class=" form-control-label">Select Area Collection Manager</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('acm_id', $area_col_manager_roles, '', ['class' => 'form-control','placeholder' =>'Select Area Collection Manager']) !!}
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class=" form-control-label">Select Regional Collection Manager</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('rcm_id', $reg_col_manager_roles, '', ['class' => 'form-control', 'placeholder' =>'Select Regional Collection Manager']) !!}
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class=" form-control-label">Select Zonal Collection Manager</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('zcm_id', $zonal_col_manager_roles, '', ['class' => 'form-control', 'placeholder' =>'Select Zonal Collection Manager']) !!} 
                </div>
            </div>

            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class=" form-control-label">Select National Collection Manager</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('ncm_id', $national_col_manager_roles, '', ['class' => 'form-control', 'placeholder' =>'Select National Collection Manager']) !!} 
                </div>
            </div>
            
            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class=" form-control-label">Select Group Product Head</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('gph_id', $group_product_head_roles, '', ['class' => 'form-control', 'placeholder' =>'Select Group Product Head']) !!}
                </div>
            </div>

            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="multiple-select" class=" form-control-label">Select Head of the Collections</label>
                </div>
                <div class="col col-md-9">
                    {!! Form::select('hc_id', $head_collection_roles, '', ['class' => 'form-control', 'placeholder' =>'Select Head of the Collections']) !!}
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
            
        </form>
        </div>

    </div>


@endsection
@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script>

        jQuery(function () {
            jQuery(".sizes").select2();
        });
        jQuery('#check-input2').on('click',function(){
            jQuery('#passwordDiv').show();
        })
        jQuery('#check-input').on('click',function(){
            jQuery('#passwordDiv').hide();
        })
    </script>
@endsection