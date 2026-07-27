@csrf

<div class="row">
            
            <div class="col col-md-4">
                <div class="form-group">
                    <label for="module_name">Module Name</label>
                    <input type="text" class="form-control" name="module_name" id="module_name" value="{{ old('module_name', $modulePermission->module_name ?? '') }}" required>                    
                </div>
            </div>
</div>
<div class="card-footer">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-dot-circle-o"></i> {{ $buttonText }}
                </button>
                <button type="reset" class="btn btn-danger btn-sm">
                    <i class="fa fa-ban"></i> Reset
                </button>
</div>

