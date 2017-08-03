<div class='form-group{{ $errors->has($field_name) ? ' has-error' : '' }}'>
    {!! Form::label($field_name, $label) !!}
    <div class="{{ $field_name }}-container">

        {{-- if validation fails, show an uploaded image --}}
        @if(old($field_name) !== null)

            <a href="/storage/{{ $upload_dir }}/{{ old($field_name) }}" target="_blank">
                <img src="/storage/{{ $upload_dir }}/{{ Croppa::url(old($field_name), $size[0], $size[1]) }}" class="img-responsive" style="margin:0 3px 10px 0">
            </a>

            {!! Form::button('<i class="fa fa-crop"></i>', [
                'data-target' => '#'.$field_name.'-crop-modal',
                'data-toggle' => 'modal',
                'class' => 'btn btn-primary'
            ]) !!}

            {!! Form::button('<i class="fa fa-remove"></i>', [
                'class' => 'btn btn-danger '.$field_name.'-delete'
            ]) !!}

            {!! Form::hidden('', old($field_name), ['name' => $field_name]) !!}

        {{-- if an image was fake-deleted and validation fails, show a file input --}}
        @elseif(old('fake_delete_'.$field_name) !== null)

            {!! Form::file($field_name, ['class' => $field_name . ' input-file-bordered']) !!}
            {!! Form::submit(trans('image_manager.upload'), ['class' => 'btn btn-primary btn-sm ' .$field_name.'-upload']) !!}

        {{-- if a model has an image, show it --}}
        @elseif(isset($model->$field_name) && !empty($model->$field_name))

            <a href="/storage/{{ $upload_dir }}/{{ $model->$field_name }}" target="_blank">
                <img src="/storage/{{ $upload_dir }}/{{ Croppa::url($model->$field_name, $size[0], $size[1]) }}" class="img-responsive" style="margin: 0 3px 10px 0">
            </a>

            {!! Form::button('<i class="fa fa-crop"></i>', [
                'data-target' => '#'.$field_name.'-crop-modal',
                'data-toggle' => 'modal',
                'class' => 'btn btn-primary'
            ]) !!}

            {!! Form::button('<i class="fa fa-remove"></i>', [
                'class' => 'btn btn-danger fake-'.$field_name.'-delete'
            ]) !!}

            {!! Form::hidden($field_name, $model->$field_name) !!}

        @else
            {!! Form::file($field_name, ['class' => $field_name . ' input-file-bordered']) !!}
            {!! Form::submit(trans('image_manager.upload'), ['class' => 'btn btn-primary btn-sm '.$field_name.'-upload']) !!}
        @endif

        {!! $errors->first($field_name, '<span class="help-block">:message</span>') !!}

    </div>
</div>

<!-- Modal -->
<div class="modal" id="{{ $field_name }}-crop-modal" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalLabel">{{ trans('image_manager.crop') }}</h4>
            </div>
            <div class="modal-body">
                <div id="{{ $field_name }}-crop-container"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-{{ $field_name }}-crop">{{ trans('image_manager.save') }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('image_manager.close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('click', '.{{ $field_name }}-upload', function(e){
            var field_name = '{{ $field_name }}';
            var upload_dir = '{{ $upload_dir }}';
            var width = '{{ $size[0] }}';
            var height = '{{ $size[1] }}';
            var $imageContainer = $('.{{ $field_name }}-container');
            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            formData.append('image', $('.{{ $field_name }}')[0].files[0]);
            formData.append('field_name', field_name);
            formData.append('upload_dir', upload_dir);

            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                url: '{{ route('image.upload') }}',
                data: formData,
                success: function (data) {
                    var original_path = '/storage/' + upload_dir + '/' + data;
                    var crop_path = croppa.url(original_path, width, height);

                    $imageContainer.html(
                            '<a href="' + original_path + '" target="_blank">' +
                                '<img src="' + crop_path + '" class="img-responsive" style="margin: 0 3px 10px 0">' +
                            '</a>' +
                            '<button type="button" class="btn btn-primary" data-target="#{{ $field_name }}-crop-modal" data-toggle="modal" style="margin-right: 2px">' +
                                '<i class="fa fa-crop"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-danger {{ $field_name }}-delete">' +
                                '<i class="fa fa-remove"></i>' +
                            '</button>' +
                            '<input name="' + field_name + '" type="hidden" value="' + data + '">'
                    );

                    $imageContainer.parent().removeClass('has-error');
                },
                error: function (data) {
                    var jsonResponse = JSON.parse(data.responseText);

                    if (!$imageContainer.children(".help-block").length) {
                        $imageContainer.parent().addClass('has-error');

                        $.each(jsonResponse['image'], function( index, value ) {
                            $imageContainer.append('<span class="help-block">' + value + '</span>');
                        });
                    }
                }
            });

            e.preventDefault();
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.{{ $field_name }}-delete', function(e){
            $.ajax({
                type: "DELETE",
                url: '{{ route('image.delete') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    filename: $('input[name="{{ $field_name }}"]').val(),
                    upload_dir: '{{ $upload_dir }}'
                },
                success: function (data) {
                    $('.{{ $field_name }}-container').html(
                            '<input name="'+ '{{ $field_name }}' +'" type="file" class="input-file-bordered '+ '{{ $field_name }}' +'" style="margin-right: 3px;">' +
                            '<input class="btn btn-primary btn-sm '+ '{{ $field_name }}' +'-upload" type="submit" value="{{ trans('image_manager.upload') }}">'
                    );
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr)
                }
            });

            e.preventDefault();
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.fake-'+ '{{ $field_name }}' +'-delete', function(e){
            $('.{{ $field_name }}-container').html(
                    '<input type="hidden" name="fake_delete_{{ $field_name }}">' +
                    '<input name="'+ '{{ $field_name }}' +'" type="file" class="{{ $field_name }} input-file-bordered" style="margin-right: 3px">' +
                    '<input class="btn btn-primary btn-sm {{ $field_name }}-upload" type="submit" value="{{ trans('image_manager.upload') }}">'
            );

            e.preventDefault();
        });
    });
</script>

<script>
    function cropper_<?= $field_name ?>() {
        $('#crop-{{ $field_name }}').cropper({
            viewMode: 1,
            autoCropArea: 1,
            zoomable: false,
            minCropBoxWidth: '{{ $size[0] }}',
            minCropBoxHeight: '{{ $size[1] }}',
            aspectRatio: '{{ $size[0] }}' / '{{ $size[1] }}'
        });
    }
</script>

<script>
    $(document).on('click', '#save-{{ $field_name }}-crop', function(){
        var $image = $('#crop-{{ $field_name }}').cropper('getData');

        $.ajax({
            type: "POST",
            url: '{{ route('image.crop.save') }}',
            data: {
                _token: '{{ csrf_token() }}',
                x: $image.x,
                y: $image.y,
                width: $image.width,
                height: $image.height,
                filename: $('input[name="{{ $field_name }}"]').val(),
                upload_dir: '{{ $upload_dir }}'
            },
            success: function (data) {
                var crop_path = croppa.url(data, '{{ $size[0] }}', '{{ $size[1] }}');

                //update thumbnail
                $('.{{ $field_name }}-container').find('img').attr('src', crop_path + '?' + new Date().getTime());

                $('#{{ $field_name }}-crop-modal').modal('hide');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#{{ $field_name }}-crop-modal').on('show.bs.modal', function (e) {
            var filename;
            var $image = $('input[name="{{ $field_name }}"]');

            <?php if(old($field_name) !== null && !empty(old($field_name))) { ?>
                filename = '{{ old($field_name) }}';
            <?php } elseif(isset($model->$field_name) && !empty($model->$field_name)) { ?>
                filename = '<?php echo $model->$field_name ?>';
            <?php } ?>

            if(typeof $image.val() !== 'undefined') {
                filename = $image.val();
            }

            $.ajax({
                type: "POST",
                url: '{{ route('image.crop.check') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    filename: filename,
                    upload_dir: '{{ $upload_dir }}'
                },
                success: function (data) {
                    $('#{{ $field_name }}-crop-container').html('<img id="crop-{{ $field_name }}" src="/storage/{{ $upload_dir }}/crops/'+ data +'" style="max-width: 570px; max-height: 500px">');

                    cropper_<?= $field_name ?>();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                }
            });

        }).on('hidden.bs.modal', function () {
            $('#crop-{{ $field_name }}').cropper('destroy');
            $('#{{ $field_name }}-crop-container').empty();
            $('#save-{{ $field_name }}-crop').removeAttr('name');
        });
    });
</script>