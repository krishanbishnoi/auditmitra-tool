@extends('layouts.master')

@section('title', '| Create Master')

@section('content')

<div class="row">
    <div class="col-lg-12">

        <div class="card">

            <div class="card-header">
                <strong>Create Master</strong>
            </div>

            <div class="card-body">

                <form action="{{ route('masters.store') }}" method="POST">

                    @csrf

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>

                                <input type="text"
                                       name="type"
                                       class="form-control"
                                       placeholder="Enter Type">

                                @error('type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>

                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       placeholder="Enter Name">

                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Possible Values</label>

                                <textarea name="possible_values"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Example: Mirai,CMM,Sunstone"></textarea>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Show Add Button</label>

                                <select name="show_add_button" class="form-control">
                                    <option value="N">No</option>
                                    <option value="Y">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Weight</label>

                                <input type="number"
                                       name="weight"
                                       class="form-control"
                                       value="0">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>

                                <select name="is_active" class="form-control">
                                    <option value="Y">Active</option>
                                    <option value="N">Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-primary btn-sm">
                            Submit
                        </button>

                        <a href="{{ route('masters.index') }}"
                           class="btn btn-danger btn-sm">
                            Back
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>
</div>

@endsection