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
					<strong class="card-title"> Red Alert</strong>
				</div>
				<div class="card-body">
                    {!! Form::model($audit,
                        array(
                        'method' => 'PATCH',
                          'url' =>'red-alert/'.Crypt::encrypt($audit->id),
                          'class' => 'kt-form',
                          'data-toggle'=>"validator")
                        ) !!}
                        @csrf
                            
                            <div class="row">
                            @foreach ($audit->redAlert as $item)  
                                <div class="col-md-6 form-group">
                                    <label>{{$item->subParameter->sub_parameter}}</label>
                                    <!-- <input type="file" class="form-control" style="margin-bottom:5px" name="artifactAnswer{{$item->id}}"/> -->
                                    <textarea name="answer{{$item->id}}" Required class="form-control" placeholder="Enter Answer"></textarea>
                                </div>
                                <!-- @if($item->artifact!=null)
                                <div class="col-md-6 form-group">
                                    <label><a href="{{url('download-action-artifact/'.Crypt::encrypt($item->id))}}" target="_blank">Artifact</a></label>
                                    <input type="file" class="form-control" style="margin-bottom:5px" name="artifactAnswer{{$item->id}}"/>
                                    <textarea name="answer{{$item->id}}" Required class="form-control" placeholder="Enter Answer"></textarea>
                                </div>
                                @endif -->
                            @endforeach
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

<script>

</script>
@endsection