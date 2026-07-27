@extends('layouts.master')

@section('title', '| CMS Pages')

@section('sh-detail')
List of CMS Pages
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>CMS Pages</strong>
                <a href="{{ route('cms.create') }}" class="btn btn-primary btn-sm float-right">
                    <i class="fa fa-plus"></i> Add New Page
                </a>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Title</th>
                           
                            <th>Status</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $key => $page)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $page->name }}</td>
                                <td>{{ $page->title }}</td>
                               
                                <td>
                                    <span class="badge badge-{{ $page->status ? 'success' : 'danger' }}">
                                        {{ $page->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @if($page->file_path)
                                        <a href="{{ asset($page->file_path) }}" target="_blank">View</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cms.edit', $page->id) }}" class="btn btn-sm btn-info">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('cms.destroy', $page->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this page?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No pages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $pages->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
