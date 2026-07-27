@extends('layouts.action')

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
					<strong class="card-title"> Summary</strong>
				</div>
				<div class="card-body">
                    <div class="row">
                        <div class="col-md-3  form-group">
                            <label>Sheet Name</label>
                            <span class="form-control">{{$action->audit->qmsheet->name ?? ''}}</span>
                        </div>
                        <div class="col-md-3  form-group">
                            <label>Auditor Name</label>
                            <span class="form-control">{{$action->audit->qa_qtl_detail->name ?? ''}}</span>
                        </div>
                        <div class="col-md-3  form-group">
                            <label>Auditor Email</label>
                            <span class="form-control">{{$action->audit->qa_qtl_detail->email ?? ''}}</span>
                        </div>
                        <div class="col-md-3  form-group">
                            <label>Collection Manager Name</label>
                            <span class="form-control">{{$collection->user->name ?? ''}}</span>
                        </div>
                        <div class="col-md-3  form-group">
                            <label>Collection Manager Email</label>
                            <span class="form-control">{{$collection->user->email ?? ''}}</span>
                        </div>
                        <div class="col-md-3  form-group">
                            <label>{{ucfirst($action->audit->qmsheet->type)}} name</label>
                            <span class="form-control">{{$name ?? ''}}</span>
                        </div>
                        <div class="col-md-3  form-group">
                            <label>Product</label>
                            <span class="form-control">{{$collection->product->name ?? ''}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<strong class="card-title"> Action Plan</strong>
				</div>
				<div class="card-body">
                    {!! Form::model($action,
                        array(
                        'method' => 'PATCH',
                          'url' =>'action/'.Crypt::encrypt($action->id),
                          'class' => 'kt-form',
                          'data-toggle'=>"validator")
                        ) !!}
                        @csrf
                            
                            @foreach ($action->sub as $item)  
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{$item->question}}</label>
                                    <input type="file" class="form-control" style="margin-bottom:5px" name="artifactAnswer{{$item->id}}"/>
                                    <textarea name="answer{{$item->id}}" Required class="form-control" placeholder="Enter Answer"></textarea>
                                </div>
                                @if($item->artifact!=null)
                                <div class="col-md-6 form-group">
                                    <label><a href="{{url('download-action-artifact/'.Crypt::encrypt($item->id))}}" target="_blank">Artifact</a></label>
                                    <input type="file" class="form-control" style="margin-bottom:5px" name="artifactAnswer{{$item->id}}"/>
                                    <textarea name="answer{{$item->id}}" Required class="form-control" placeholder="Enter Answer"></textarea>
                                </div>
                                @endif
                            </div>
                            @endforeach
                                    
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

<script>

</script>
@endsection