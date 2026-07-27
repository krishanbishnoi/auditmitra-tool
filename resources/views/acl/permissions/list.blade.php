@extends('layouts.master')

@section('title', '| Roles')

@section('content')

	<div class="card">
		<div class="card-header">
		<strong>
			Permissions List</strong>

			<a href="{{ route('user.index') }}" class="btn btn-default pull-right">Users</a>
			<a href="{{ route('roles.index') }}" class="btn btn-default pull-right">Roles</a></h1>
		</div>
		<div class="card-body card-block table-responsive">
			<table class="table table-bordered table-striped">

				<thead>
				<tr>
					<th>Permissions</th>
					<th>Operation</th>
				</tr>
				</thead>
				<tbody>
				@foreach ($permissions as $permission)
					<tr>
						<td>{{ $permission->name }}</td>
						<td>
							<a href="{{url('permissions/'.Crypt::encrypt($permission->id).'/edit')}}" class="btn btn-info pull-left" style="margin-right: 3px;">Edit</a>

							{{--{!! Form::open(['method' => 'DELETE', 'route' => ['permissions.destroy', $permission->id] ]) !!}
							{!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
							{!! Form::close() !!}--}}

						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<a href="{{ URL::to('permissions/create') }}" class="btn btn-success">Add Permission</a>
		</div>



	</div>

@endsection