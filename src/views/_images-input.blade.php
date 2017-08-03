<div class='form-group{{ $errors->has($field_name) ? ' has-error' : '' }}'>
    {!! Form::label($field_name, $label) !!}
    <div class="{{ $field_name }}-container">

        {{-- if validation fails, show an uploaded image --}}
        @if(old($field_name) !== null)
            <div class="table-responsive">
                <table class="{{ $field_name }}-table table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('image_manager.form.image') }}</th>
                        <th>{{ trans('image_manager.form.image_alt') }}</th>
                        <th>{{ trans('image_manager.form.image_title') }}</th>
                        <th>{{ trans('image_manager.form.image_weight') }}</th>
                        <th>{{ trans('image_manager.form.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="ui-sortable">
                    @foreach(old($field_name) as $key => $image)
                        @if(!isset($image['delete']))
                            <tr>
                                <td><a class="handle"><span class="glyphicon glyphicon-move"></span></a></td>
                                <td>
                                    <a href="/storage/{{ $upload_dir }}/{{ $image['filename'] }}" target="_blank">
                                        <img src="{{ Croppa::url('/storage/' . $upload_dir . '/' . $image['filename'], $size[0], $size[1]) }}">
                                    </a>
                                </td>
                                <td><input placeholder="Alt" class="form-control" data-toggle="tooltip" title="" name="{{ $field_name }}[{{ $key }}][alt]" type="text" value="{{ $image['alt'] }}" data-original-title="{{ trans('image_manager.form.image_alt_help') }}"></td>
                                <td><input placeholder="Title" class="form-control" data-toggle="tooltip" title="" name="{{ $field_name }}[{{ $key }}][title]" type="text" value="{{ $image['title'] }}" data-original-title="{{ trans('image_manager.form.image_title_help') }}"></td>
                                <td><input class="form-control {{ $field_name }}-weight" style="max-width:70px" name="{{ $field_name }}[{{ $key }}][weight]" type="text" value="{{ $image['weight'] }}"></td>
                                <td>
                                    <input name="{{ $field_name }}[{{ $key }}][filename]" value="{{ $image['filename'] }}" type="hidden">
                                    <button name="{{ $image['filename'] }}" type="button" class="btn btn-primary crop-{{ $field_name }}" style="margin-right: 2px"><i class="fa fa-crop"></i></button>
                                    <button name="{{ $image['filename'] }}" class="btn btn-danger {{ $field_name }}-delete" type="submit"><i class="fa fa-remove"></i></button>
                                </td>
                            </tr>
                        @else
                            <input type="hidden" name="{{ $field_name }}[{{ $key }}][delete]" value="{{ $image['delete'] }}">
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            {!! Form::file($field_name.'[]', ['multiple' => true, 'class' => $field_name . ' input-file-bordered']) !!}
            {!! Form::submit(trans('image_manager.upload'), ['class' => 'btn btn-primary btn-sm ' .$field_name.'-upload']) !!}

        {{-- if a model has images, show them --}}
        @elseif (isset($model) && $model->$field_name->count() > 0)
            <div class="table-responsive">
                <table class="{{ $field_name }}-table table table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('image_manager.form.image') }}</th>
                        <th>{{ trans('image_manager.form.image_alt') }}</th>
                        <th>{{ trans('image_manager.form.image_title') }}</th>
                        <th>{{ trans('image_manager.form.image_weight') }}</th>
                        <th>{{ trans('image_manager.form.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="ui-sortable">
                    @foreach($model->$field_name as $image)
                        <tr>
                            <td><a class="handle"><span class="glyphicon glyphicon-move"></span></a></td>
                            <td>
                                <a href="/storage/{{ $upload_dir }}/{{ $image->filename }}" target="_blank">
                                    <img src="{{ Croppa::url('/storage/' . $upload_dir . '/' . $image->filename, $size[0], $size[1]) }}">
                                </a>
                            </td>
                            <td><input placeholder="Alt" class="form-control" data-toggle="tooltip" title="" name="{{ $field_name }}[{{ $image->id }}][alt]" type="text" value="{{ $image->alt }}" data-original-title="{{ trans('image_manager.form.image_alt_help') }}"></td>
                            <td><input placeholder="Title" class="form-control" data-toggle="tooltip" title="" name="{{ $field_name }}[{{ $image->id }}][title]" type="text" value="{{ $image->title }}" data-original-title="{{ trans('image_manager.form.image_title_help') }}"></td>
                            <td><input class="form-control {{ $field_name }}-weight" style="max-width:70px" name="{{ $field_name }}[{{ $image->id }}][weight]" type="text" value="{{ $image->weight }}"></td>
                            <td>
                                <input class="{{ $field_name }}-id" value="{{ $image->id }}" type="hidden">
                                <input name="{{ $field_name }}[{{ $image->id }}][filename]" value="{{ $image->filename }}" type="hidden">
                                <button name="{{ $image->filename }}" type="button" class="btn btn-primary crop-{{ $field_name }}" style="margin-right: 2px"><i class="fa fa-crop"></i></button>
                                <button name="{{ $image->filename }}" value="{{ $image->id }}" class="btn btn-danger fake-{{ $field_name }}-delete" type="submit"><i class="fa fa-remove"></i></button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {!! Form::file($field_name.'[]', ['multiple' => true, 'class' => $field_name . ' input-file-bordered']) !!}
            {!! Form::submit(trans('image_manager.upload'), ['class' => 'btn btn-primary btn-sm ' .$field_name.'-upload']) !!}
        @else
            {!! Form::file($field_name.'[]', ['multiple' => true, 'class' => $field_name . ' input-file-bordered']) !!}
            {!! Form::submit(trans('image_manager.upload'), ['class' => 'btn btn-primary btn-sm ' .$field_name.'-upload']) !!}
        @endif
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
    $(document).on('click', '.{{ $field_name }}-upload', function(e){
        var weight, new_id;
        var $imagesContainer = $('.{{ $field_name }}-container');
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append('upload_dir', '{{ $upload_dir }}');

        var count = $('.{{ $field_name }}')[0].files.length;
        for (var i = 0; i < count; i++) {
            formData.append("images[]", $('.{{ $field_name }}')[0].files[i]);
        }

        if(!$('.{{ $field_name }}-table').length) {
            weight = 0;
            new_id = 0;
        } else {
            weight = $('.{{ $field_name }}-table tr').length - 1;

            var array = [];

            $.each($('.{{ $field_name }}-id'), function (key, value) {
                array.push($(value).val());
            });

            new_id = Math.max.apply(null, array) + 1;
        }

        $.ajax({
            type: "POST",
            processData: false,
            contentType: false,
            url: '{{ route('images.upload') }}',
            data: formData,
            success: function (data) {
                if(data !== false) {
                    if (!$('.{{ $field_name }}-table').length) {
                        $(e.target).parent().before(
                                '<div class="table-responsive">' +
                                '<table class="{{ $field_name }}-table table table-striped">' +
                                '<thead>' +
                                '<tr>' +
                                '<th></th>' +
                                '<th>{{ trans('image_manager.form.image') }}</th>' +
                                '<th>{{ trans('image_manager.form.image_alt') }}</th>' +
                                '<th>{{ trans('image_manager.form.image_title') }}</th>' +
                                '<th>{{ trans('image_manager.form.image_weight') }}</th>' +
                                '<th>{{ trans('image_manager.form.actions') }}</th>' +
                                '</tr>' +
                                '</thead>' +
                                '<tbody class="ui-sortable">' +
                                '</tbody>' +
                                '</table>' +
                                '</div>'
                        );
                    }

                    var $images_table_tbody = $('.{{ $field_name }}-table tbody');
                    var crop;
                    for (var i = 0; i < data.length; i++) {
                        crop = croppa.url('/storage/' + '{{ $upload_dir }}/' + data[i], '{{ $size[0] }}', '{{ $size[1] }}');
                        $images_table_tbody.append(
                                '<tr>' +
                                '<td><a class="handle"><span class="glyphicon glyphicon-move"></span></a></td>' +
                                '<td><a href="/storage/' + '{{ $upload_dir }}/' + data[i] + '" target="_blank"><img src="' + crop + '"></a></td>' +
                                '<td><input placeholder="Alt" class="form-control" data-toggle="tooltip" title="" name="{{ $field_name }}[' + new_id + '][alt]" type="text" value="" data-original-title="{{ trans('image_manager.form.image_alt_help') }}"></td>' +
                                '<td><input placeholder="Title" class="form-control" data-toggle="tooltip" title="" name="{{ $field_name }}[' + new_id + '][title]" type="text" value="" data-original-title="{{ trans('image_manager.form.image_title_help') }}"></td>' +
                                '<td><input class="form-control {{ $field_name }}-weight" style="max-width:70px" name="{{ $field_name }}[' + new_id + '][weight]" type="text" value="' + weight + '"></td>' +
                                '<td>' +
                                '<input class="' + '{{ $field_name }}' + '-id" value="' + new_id + '" type="hidden">' +
                                '<input name="{{ $field_name }}[' + new_id + '][filename]" value="' + data[i] + '" type="hidden">' +
                                '<button name="' + data[i] + '" type="button" class="btn btn-primary crop-{{ $field_name }}" style="margin-right: 2px"><i class="fa fa-crop"></i></button>' +
                                '<button name="' + data[i] + '" class="btn btn-danger {{ $field_name }}-delete" type="submit"><i class="fa fa-remove"></i></button>' +
                                '</td>' +
                                '</tr>'
                        );
                        weight++;
                        new_id++;
                    }

                    $images_table_tbody.sortable();
                    $images_table_tbody.on("sortupdate", function (event, ui) {
                        $('.{{ $field_name }}-weight').each(function (e) {
                            $(this).val($('.{{ $field_name }}-weight').index(this));
                        });
                    });

                    $('.{{ $field_name }}').val('');
                    $imagesContainer.parent().removeClass('has-error');
                    $imagesContainer.find('.help-block').remove();
                }
            },
            error: function (data){
                var jsonResponse = JSON.parse(data.responseText);

                $.each(jsonResponse, function(index, value) {
                    if (!$imagesContainer.children(".help-block").length) {
                        $imagesContainer.parent().addClass('has-error');

                        $.each(value, function (index, value) {
                            $imagesContainer.append(
                                '<span class="help-block">' + value + '</span>'
                            );
                        })
                    }
                });

            }
        });

        e.preventDefault();
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
                    filename: $(this).prop('name'),
                    upload_dir: '{{ $upload_dir }}'
                },
                success: function (data) {
                    $(e.target).closest('tr').hide('fade', {}, 'fast', function() {
                        $(this).remove();
                        if($('.{{ $field_name }}-table tr').length == 1) {
                            $('.{{ $field_name }}-table').parent().remove();
                        }
                    });
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
            $('.{{ $field_name }}-container .table-responsive').after(
                '<input type="hidden" name="'+ '{{ $field_name }}['+ $(this).val() +'][delete]" value="'+ $(this).prop('name') +'">'
            );
            $(e.target).closest('tr').hide('fade', {}, 'fast', function() {
                $(this).remove();
                if($('.{{ $field_name }}-table tr').length == 1) {
                    $('.{{ $field_name }}-table').parent().remove();
                }
            });
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
    $(document).on('click', '.crop-{{ $field_name }}', function(e){
        var filename = $(this).prop('name');

        $.ajax({
            type: "POST",
            url: '{{ route('image.crop.check') }}',
            data: {
                _token: '{{ csrf_token() }}',
                filename: filename,
                upload_dir: '{{ $upload_dir }}'
            },
            success: function (data) {
                $('#{{ $field_name }}-crop-container').html('<img id="crop-{{ $field_name }}" src="/storage/'+ '{{ $upload_dir }}' + '/crops/' + data +'" style="max-width: 570px; max-height: 500px">');
                $('#save-{{ $field_name }}-crop').attr('name', data);

                cropper_<?= $field_name ?>();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
            }
        });

        $('#{{ $field_name }}-crop-modal').modal('show');
    });
</script>

<script>
    $('#{{ $field_name }}-crop-modal').on('hidden.bs.modal', function () {
        $('#crop-{{ $field_name }}').cropper('destroy');
        $('#{{ $field_name }}-crop-container').empty();
        $('#save-{{ $field_name }}-crop').removeAttr('name');
    });
</script>

<script>
    $(document).on('click', '#save-{{ $field_name }}-crop', function(e){
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
                filename: $('#save-{{ $field_name }}-crop').prop('name'),
                upload_dir: '{{ $upload_dir }}'
            },
            success: function (data) {
                var crop_path = croppa.url(data, '{{ $size[0] }}', '{{ $size[1] }}');
                var $thumb = $('.{{ $field_name }}-table').find('img[src*="'+crop_path+'"]');

                //update thumbnail
                $thumb.attr('src', crop_path + '?' + new Date().getTime());

                $('#{{ $field_name }}-crop-modal').modal('hide');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
            }
        });
    });
</script>

<script>
    function imagesSortable($element) {
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };
        $element.sortable({helper: fixHelper}).disableSelection();
        $element.on("sortupdate", function (event, ui) {
            $('.{{ $field_name }}-weight').each(function (e) {
                $(this).val($('.{{ $field_name }}-weight').index(this));
            });
        });
    }

    imagesSortable($('.{{ $field_name }}-table tbody'));
</script>