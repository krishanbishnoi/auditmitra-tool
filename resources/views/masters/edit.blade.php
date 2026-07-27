@extends('layouts.master')

@section('title', '| Edit Master')

@section('content')

<div class="row">
    <div class="col-lg-12">

        <div class="card">

            <div class="card-header">
                <strong>Edit Master</strong>
            </div>

            <div class="card-body">

                <form action="{{ route('masters.update', $master->id) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>

                                <input type="text"
                                       name="type"
                                       class="form-control"
                                       value="{{ $master->type }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>

                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ $master->name }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Possible Values</label>

                                <textarea name="possible_values"
                                          class="form-control"
                                          rows="4">{{ $master->possible_values }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Show Add Button</label>

                                <select name="show_add_button" class="form-control">
                                    <option value="N" {{ $master->show_add_button == 'N' ? 'selected' : '' }}>No</option>

                                    <option value="Y" {{ $master->show_add_button == 'Y' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Weight</label>

                                <input type="number"
                                       name="weight"
                                       class="form-control"
                                       value="{{ $master->weight }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>

                                <select name="is_active" class="form-control">
                                    <option value="Y" {{ $master->is_active == 'Y' ? 'selected' : '' }}>Active</option>

                                    <option value="N" {{ $master->is_active == 'N' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-primary btn-sm">
                            Update
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