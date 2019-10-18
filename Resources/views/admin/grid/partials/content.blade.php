<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ trans('media::media.file picker') }}</title>
    {!! Theme::style('vendor/bootstrap/dist/css/bootstrap.min.css') !!}
    {!! Theme::style('vendor/admin-lte/dist/css/AdminLTE.css') !!}
    {!! Theme::style('vendor/datatables.net-bs/css/dataTables.bootstrap.min.css') !!}
    {!! Theme::style('vendor/font-awesome/css/font-awesome.min.css') !!}
    <link href="{!! Module::asset('media:css/dropzone.css') !!}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{!! Module::asset('translation:vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css') !!}">
    <style>
        body {
            background: #ecf0f5;
            margin-top: 20px;
        }
        .dropzone {
            border: 1px dashed #CCC;
            min-height: 227px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
    <script>
        AuthorizationHeaderValue = 'Bearer {{ $currentUser->getFirstApiKey() }}';
    </script>
    @include('partials.asgard-globals')
</head>
<?php $locale = App::getLocale(); ?>
<body>
<div class="container">
    <div class="row">
        <form method="POST" class="dropzone">
            {!! Form::token() !!}
        </form>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ trans('media::media.choose file') }}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool jsShowUploadForm" data-toggle="tooltip" title="" data-original-title="Upload new">
                        <i class="fa fa-cloud-upload"></i>
                        Upload new
                    </button>
                </div>
            </div>
            <div class="box-body">
                <table class="data-table table table-bordered table-hover jsFileList data-table">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>{{ trans('core::core.table.thumbnail') }}</th>
                        <th>{{ trans('media::media.table.filename') }}</th>
                        <th>{{ trans('media::media.form.alt_attribute') }}</th>
                        <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($files): ?>
                    <?php foreach ($files as $file): ?>
                    <tr>
                        <td>{{ $file->id }}</td>
                        <td>
                            <?php if ($file->isImage()): ?>
                            <img src="{{ Imagy::getThumbnail($file->path, 'smallThumb') }}" alt=""/>
                            <?php else: ?>
                            <i class="fa {{ FileHelper::getFaIcon($file->media_type) }}" style="font-size: 20px;"></i>
                            <?php endif; ?>
                        </td>
                        <td>{{ $file->filename }}</td>
                        <?php $altAttribute = isset($file->translate($locale)->alt_attribute) ? $file->translate($locale)->alt_attribute : '' ?>
                        <td><a class="alt-attribute" data-pk="{{ $locale }}__-__{{ $file->id }}">{{ $altAttribute }}</a></td>
                        <td>
                            <div class="btn-group">
                                <?php if ($isWysiwyg === true): ?>
                                <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('media::media.insert') }} <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <?php foreach ($thumbnails as $thumbnail): ?>
                                    <li data-file-path="{{ Imagy::getThumbnail($file->path, $thumbnail->name()) }}"
                                        data-id="{{ $file->id }}" data-media-type="{{ $file->media_type }}"
                                        data-mimetype="{{ $file->mimetype }}" class="jsInsertImage">
                                        <a href="">{{ $thumbnail->name() }} ({{ $thumbnail->size() }})</a>
                                    </li>
                                    <?php endforeach; ?>
                                    <li class="divider"></li>
                                    <li data-file-path="{{ $file->path }}" data-id="{{ $file->id }}"
                                        data-media-type="{{ $file->media_type }}" data-mimetype="{{ $file->mimetype }}" class="jsInsertImage">
                                        <a href="">Original</a>
                                    </li>
                                </ul>
                                <?php else: ?>
                                <a href="" class="btn btn-primary jsInsertImage btn-flat" data-id="{{ $file->id }}"
                                   data-file-path="{{ Imagy::getThumbnail($file->path, 'mediumThumb') }}"
                                   data-media-type="{{ $file->media_type }}" data-mimetype="{{ $file->mimetype }}">
                                    {{ trans('media::media.insert') }}
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{!! Theme::script('vendor/jquery/jquery.min.js') !!}
{!! Theme::script('vendor/bootstrap/dist/js/bootstrap.min.js') !!}
{!! Theme::script('vendor/datatables.net/js/jquery.dataTables.min.js') !!}
{!! Theme::script('vendor/datatables.net-bs/js/dataTables.bootstrap.min.js') !!}
<script src="{!! Module::asset('media:js/dropzone.js') !!}"></script>
<?php $config = config('asgard.media.config'); ?>
<script>
    var maxFilesize = '<?php echo $config['max-file-size'] ?>',
        acceptedFiles = '<?php echo $config['allowed-types'] ?>';
</script>
<script src="{!! Module::asset('media:js/init-dropzone.js') !!}"></script>
<script src="{!! Module::asset('translation:vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js') !!}"></script>
<script>
    $( document ).ready(function() {
        $('.jsShowUploadForm').on('click',function (event) {
            event.preventDefault();
            $('.dropzone').fadeToggle();
        });
    });
</script>

<script type="text/javascript">
    $(function () {
        $('.data-table').dataTable({
            "paginate": true,
            "lengthChange": true,
            "filter": true,
            "sort": true,
            "info": true,
            "autoWidth": true,
            "order": [[ 0, "desc" ]],
            "language": {
                "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
            }
        });
    });
    $(function() {
        $('a.alt-attribute').editable({
            url: function(params) {
                var splitKey = params.pk.split("__-__");
                var locale = splitKey[0];
                var key = splitKey[1];
                var value = params.value;

                if (! locale || ! key) {
                    return false;
                }

                var data = {
                    id: key
                };
                data[locale] = {
                    alt_attribute: value,
                };

                $.ajax({
                    url: '/{{$locale}}/api/file/' + key,
                    headers: {
                        'Authorization': 'Bearer {{ $currentUser->getFirstApiKey() }}',
                    },
                    method: 'PUT',
                    dataType: 'JSON',
                    data: data,
                    success: function(res) {
                    }
                })
            },
            type: 'textarea',
            mode: 'inline',
            send: 'always', /* Always send, because we have no 'pk' which editable expects */
            inputclass: 'translation_input'
        });
    });
</script>
