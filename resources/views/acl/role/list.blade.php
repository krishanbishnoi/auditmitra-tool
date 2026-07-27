@extends('layouts.master')

@section('title', '| Roles')

@section('content')

	<div class="card">
		<div class="card-header">
		<strong>
			Roles</strong>

			<a href="{{ route('user.index') }}" class="btn btn-default pull-right">Users</a>
			<a href="{{ route('permissions.index') }}" class="btn btn-default pull-right">Permissions</a></h1>
		</div>
		<div class="card-body card-block table-responsive">
			<table class="table table-bordered table-striped">
				<thead>
				<tr>
					<th>Role</th>
					<th>Permissions</th>
					<th>Operation</th>
				</tr>
				</thead>

				<tbody>
				@foreach ($roles as $role)
					<tr>

						<td>{{ $role->name }}</td>

						<td>{{ str_replace(array('[',']','"'),'', $role->permissions()->pluck('name')) }}</td>{{-- Retrieve array of permissions associated to a role and convert to string --}}
						<td>
							<a href="{{url('roles/'.Crypt::encrypt($role->id).'/edit')}}" class="btn btn-info pull-left" style="margin-right: 3px;">Edit</a>

							{{--{!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id] ]) !!}
							{!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
							{!! Form::close() !!}--}}

						</td>
					</tr>
				@endforeach
				</tbody>

			</table>
			<a href="{{ URL::to('roles/create') }}" class="btn btn-success">Add Role</a>
		</div>



	</div>

@endsection