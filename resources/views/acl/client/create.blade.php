@extends('layouts.master')



@section('title', '| Users')



@section('sh-detail')

    Create New

@endsection



@section('content')

    <div class="card">

        <div class="card-header">

            <strong>Create Client</strong>

            <!-- <a class="btn btn-primary btn-sm float-right" style="margin-right: 5px" href="{{ route('userUpload') }}">Import Users (Create bulk user)</a> -->

        </div>

        <div class="card-body card-block">

            {!! Form::open([
                'route' => 'client.store',
                'class' => 'form-horizontal',
                'role' => 'form',
                'data-toggle' => 'validator',
                'files' => true, // <-- this is required for file upload
            ]) !!}



            <div class="row form-group">

                <div class="col col-md-3"><label for="text-input" class=" form-control-label">Name</label></div>

                <div class="col-12 col-md-9"><input type="text" id="text-input" name="name" placeholder="User Name"
                        class="form-control" value="{{ old('name') }}">

                </div>

            </div>

            <div class="row form-group">

                <div class="col col-md-3"><label for="email-input" class=" form-control-label">Email</label></div>

                <div class="col-12 col-md-9"><input type="email" id="email-input" value="{{ old('email') }}"
                        name="email" placeholder="Enter Email" class="form-control">

                </div>

            </div>



            <div class="row form-group">

                <div class="col col-md-3"><label for="email-input" class=" form-control-label">Mobile</label></div>

                <div class="col-12 col-md-9"><input type="text" id="email-input" name="mobile"
                        placeholder="Enter Mobile" class="form-control" value="{{ old('mobile') }}">

                </div>


            </div>
            <div class="row form-group">
                <div class="col col-md-3">
                    <label for="logo" class="form-control-label">Client Logo</label>
                </div>
                <div class="col-12 col-md-9">
                    <input type="file" name="logo" id="logo" class="form-control-file">
                </div>
            </div>
            <div class="row form-group">
                <div class="col col-md-3"><label class="form-control-label">Color Code</label></div>
                <div class="col-12 col-md-9 d-flex align-items-center">
                    <input type="color" name="color_code" id="color_code" class="form-control" style="max-width: 100px;"
                        value="{{ old('color_code', '#000000') }}">
                    <div id="colorPreview"
                        style="width: 40px; height: 40px; border: 1px solid #ccc; margin-left: 15px; background-color: {{ old('color_code', '#000000') }}">
                    </div>
                    <span id="colorCodeText"
                        style="margin-left: 10px; font-weight: bold;">{{ old('color_code', '#000000') }}</span>
                </div>
            </div>

            <div class="row form-group">
                <div class="col col-md-3">
                    <label class="form-control-label">Levels</label>
                </div>
                <div class="col-12 col-md-9">
                    <div id="levels-container">
                        <!-- Dynamic level fields will appear here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-success mt-2" id="add-level-btn">Add Level</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date" class="form-label">Cycle Start Date</label>
                        <select name="start_date" id="start_date" class="form-control" required>
                            <option value="">-- Cycle Start Date --</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date" class="form-label">Cycle End Date</label>
                        <select name="end_date" id="end_date" class="form-control" required>
                            <option value="">-- Cycle End Date --</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>


            <div class="row form-group">

                <div class="col col-md-3"><label for="check-input" class=" form-control-label">Password Type</label></div>

                <div class="col-12 col-md-9">

                    <input type="radio" id="check-input" name="auto" value="automatic"><label
                        for="check-input">Automatic</label>

                    <input type="radio" id="check-input2" name="auto" value="manual" checked><label
                        for="check-input2">Manual</label>

                </div>

            </div>

            <div id="passwordDiv">

                <div class="row form-group">

                    <div class="col col-md-3"><label for="email-input" class=" form-control-label">Password</label></div>

                    <div class="col-12 col-md-9"><input type="password" id="email-input" name="password"
                            placeholder="Enter password" class="form-control">

                    </div>

                </div>

                <div class="row form-group">

                    <div class="col col-md-3"><label for="email-input" class=" form-control-label">Confirm
                            Password</label>
                    </div>

                    <div class="col-12 col-md-9"><input type="password" id="email-input" name="password_confirmation"
                            placeholder="Confirm Password" class="form-control">

                    </div>

                </div>

            </div>

            <div class="row form-group">
                <div class="col col-md-3">
                    <label class="form-control-label">Audit Type</label>
                </div>

                <div class="col-12 col-md-9">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_legal" id="is_legal" value="1"
                            {{ old('is_legal') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_legal">
                            Legal
                        </label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_compliance" id="is_compliance"
                            value="1" {{ old('is_compliance') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_compliance">
                            Compliance
                        </label>
                    </div>
                </div>
            </div>

            <div class="row form-group">

                <div class="col col-md-3"><label for="multiple-select" class=" form-control-label">Multiple

                        select</label></div>

                <div class="col col-md-9">
                    {!! Form::select('role[]', $roles, null, ['id' => 'multiple-select', 'class' => 'form-control']) !!}
                </div>

            </div>

            <div class="card-footer">

                <button type="submit" class="btn btn-primary btn-sm">

                    <i class="fa fa-dot-circle-o"></i> Create

                </button>

                <button type="reset" class="btn btn-danger btn-sm">

                    <i class="fa fa-ban"></i> Reset

                </button>

            </div>

            </form>

        </div>



    </div>





@endsection

@section('js')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

    <script>
        jQuery(function() {

            jQuery(".sizes").select2();

        });

        jQuery('#check-input2').on('click', function() {

            jQuery('#passwordDiv').show();

        })

        jQuery('#check-input').on('click', function() {

            jQuery('#passwordDiv').hide();

        })
        document.getElementById('color_code').addEventListener('input', function() {
            const color = this.value;
            document.getElementById('colorPreview').style.backgroundColor = color;
            document.getElementById('colorCodeText').textContent = color;
        });
    </script>

    <script>
        let maxLevels = 10;
        let levelCount = 0;

        function createLevelField(index) {
            return `
        <div class="input-group mb-2 level-item" data-index="${index}">
            <input type="text" name="levels[]" class="form-control" placeholder="Enter Level ${index + 1}">
            <div class="input-group-append">
                <button class="btn btn-danger remove-level-btn" type="button">Remove</button>
            </div>
        </div>
    `;
        }

        document.getElementById('add-level-btn').addEventListener('click', function() {
            if (levelCount < maxLevels) {
                const container = document.getElementById('levels-container');
                container.insertAdjacentHTML('beforeend', createLevelField(levelCount));
                levelCount++;
            } else {
                alert("Maximum 10 levels can be added.");
            }
        });

        document.getElementById('levels-container').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-level-btn')) {
                const item = e.target.closest('.level-item');
                item.remove();
                levelCount--;
                updateLevelPlaceholders();
            }
        });

        function updateLevelPlaceholders() {
            const levelItems = document.querySelectorAll('#levels-container .level-item');
            levelItems.forEach((item, idx) => {
                item.querySelector('input').placeholder = `Enter Level ${idx + 1}`;
            });
        }
    </script>


@endsection
