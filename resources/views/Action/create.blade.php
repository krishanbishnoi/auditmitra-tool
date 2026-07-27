@extends('layouts.master')

@section('sh-title')
Audited
@endsection

@section('sh-detail')
Call
@endsection

@section('content')
<div class="row">
	<div class="col-lg-12" style="margin-top:10x">
	</div>
</div>
<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<strong class="card-title">Create Action Plan</strong>
				</div>
				<div class="card-body">
                    <form method="post" action="{{route('action.store')}}" enctype="multipart/form-data">
                        @csrf
                            <div class="row">

                                    <div class="col-md-3 form-group">
                                        <label>Branch*</label>
                                        <select name="branch_id" id="branchName" data-placeholder="Choose a Branch..." class="standardSelect form-control branchName" tabindex="2" required>
                                          <option value="" label="Branch Name"></option>
                                          @foreach($branch as $k=>$item)
                                          <option value="{{$item->id}}">{{$item->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-5 form-group">
                                    <input type="hidden" name="sheet_id" value="{{$did}}"/>
                                        <label>Question*</label>
                                        <textarea name="question"  class="form-control"></textarea>
                                    </div>
                                    <div class="col-md-5 form-group">
                                        <label>Artifact*</label><br/>
                                        <input type="file" name="artifact"/>
                                    </div>
                                    {{-- <div class="col-md-2">
                                        <div data-repeater-delete="" class="btn-sm btn btn-danger btn-pill">
                                            <span>
                                                <i class="la la-trash-o"></i>
                                                <span>Delete</span>
                                            </span>
                                        </div>
                                    </div> --}}
                                </div>
                               <div id="kt_repeater_6"></div>

                        
                        <div class="form-group row">
                            <div class="col-lg-4">
                                <div id="add" class="btn btn-primary btn-sm">
                                    <span>
                                        <i class="fa fa-plus"></i>
                                        <span>Add</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <input type="submit" value="submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('css')
@include('shared.table_css');
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
{{-- @include('shared.form_js'); --}}

<div class="col-md-3 form-group">
                                        <label>Branch*</label>
                                        <select name="branch_id'+i+'" id="branchName" data-placeholder="Choose a Branch..." class="standardSelect form-control branchName" tabindex="2" required>
                                          <option value="" label="Branch Name"></option>
                                          @foreach($branch as $k=>$item)
                                          <option value="{{$item->id}}">{{$item->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
<script>
    var i=1;
    jQuery("#add").on('click',function(e){ 
    var data=''
    data='<div class="col-md-3 form-group">\
            <label>Branch*</label>\
            <select name="branch_id'+i+'" id="branchName" data-placeholder="Choose a Branch..." class="standardSelect form-control branchName" tabindex="2" required>\
                                          <option value="" label="Branch Name"></option>\
                                          @foreach($branch as $k=>$item)\
                                          <option value="{{$item->id}}">{{$item->name}}</option>\
                                          @endforeach\
                                        </select>\
        </div>\
    <div class="col-md-3 form-group">\
            <label>Question*</label>\
            <textarea name="question'+i+'"  class="form-control"></textarea>\
        </div>\
        <div class="col-md-3 form-group">\
            <label>Artifact*</label><br/>\
            <input type="file" name="artifact'+i+'"/>\
        </div>\
        <div class="col-md-2">\
            <button type="button" class="btn-sm btn btn-danger btn-pill remove"  data-id="'+i+'">\
                    <span class="remove" onclick="removeField('+i+');">Delete</span></button>\
            </div>';
        jQuery('#kt_repeater_6').append('<div class="row delete'+i+'">'+data+'</div>')
        i++;
    })
    function removeField(id){
        
        jQuery('.delete'+id).remove();
    }
</script>
@endsection