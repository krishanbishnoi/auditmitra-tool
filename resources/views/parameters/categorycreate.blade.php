@extends('layouts.master')

@section('content')
<div class="container">
    <h2>{{ isset($editParam) ? 'Edit Category' : 'Add Category' }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

  <form method="POST" action="{{ isset($editParam) ? route('category.update', $editParam->id) : route('category.store') }}">
    @csrf
    @if(isset($editParam))
        @method('PUT')
    @endif

    <div class="row">

        <!-- Category Name -->
        <div class="form-group col-md-6">
            <label for="category_name">Category Name</label>
            <input type="text" 
                   name="category" 
                   id="category_name"
                   class="form-control" 
                   required 
                   value="{{ old('category', $editParam->category ?? '') }}">

            @error('category')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Category Weight -->
        <div class="form-group col-md-6">
            <label for="category_weight">Category Weight</label>
            <input type="number" 
                   name="weight" 
                   id="category_weight"
                   class="form-control" 
                   step="0.01"
                   required 
                   value="{{ old('weight', $editParam->weight ?? '') }}">

            @error('weight')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

    </div>

    <button class="btn btn-primary mt-3" type="submit">
        {{ isset($editParam) ? 'Update' : 'Save' }}
    </button>
</form>

    <h4 class="mt-4">Existing category</h4>

    <table class="table table-bordered mt-2">
        <thead>
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Weight</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($category as $index => $param)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $param->category }}</td>
                    <td>{{ $param->weight }}</td>
                    <td>{{ $param->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('category.edit', $param->id) }}" class="btn btn-sm btn-info">Edit</a>
                        {{-- <form method="POST" action="{{ route('category.destroy', $param->id) }}" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                        <a href="{{ route('sub-category.create') }}" class="btn btn-sm btn-success">Add Sub-Parameter</a> --}}

                    </td>
                    <td> - </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
