# Vendor package for uploading images in Laravel 5.3

## Installation

1. Require the package via Composer in your `composer.json`
```
{
    "require": {
        "alexd/image": "*"
    }
}
```

2. Run Composer update
```
$ composer update
```

3. Add the service providers to your `app/config/app.php` file
```
Bkwld\Croppa\ServiceProvider::class,
Alexd\Image\ImageServiceProvider::class,
```

4. Add the aliases to `app/config/app.php` file
```
'Croppa' => Bkwld\Croppa\Facade::class,
'ImageManager' => Alexd\Image\Facades\Image::class
```

5. Run artisan commands
```
php artisan vendor:publish --provider="Alexd\Image\ImageServiceProvider"
php artisan storage:link
```

6. if you already had `config/croppa.php` change setting values on following:

```
'src_dir' => public_path('storage'),
'crops_dir' => public_path('storage'),
'path' => 'storage/(.*)$',
'signing_key' => false,
'upscale' => true,
```

## Usage

1. On the create form add:

For single image:
```
@include('ImageManager::_scripts')
@include('ImageManager::_image-input', [
    'label' => 'Image',
    'field_name' => 'image',
    'upload_dir' => 'gallery',
    'size' => [100, 100]
])

```

For multiple images:
```
@include('ImageManager::_scripts')
@include('ImageManager::_images-input', [
    'label' => 'Images',
    'field_name' => 'images',
    'upload_dir' => 'gallery',
    'size' => [100, 100]
])
```

2. On the edit form add:

For single image:
```
@include('ImageManager::_scripts')
@include('ImageManager::_image-input', [
    'label' => 'Image',
    'field_name' => 'image',
    'upload_dir' => 'gallery',
    'size' => [100, 100],
    'model' => $model
])
```

For multiple images:
```
@include('ImageManager::_scripts')
@include('ImageManager::_images-input', [
            'label' => 'Images',
            'field_name' => 'images',
            'upload_dir' => 'gallery',
            'size' => [100, 100],
            'model' => $model
        ])
```
Don't forget to add `multipart/form-data` to your forms

3. On the store method you can use:

For single image:
```
$data = $request->all();

$data['image'] = ImageManager::upload($request, 'image', 'gallery');
```

For multiple images:
```
$data = $request->all();

ImageManager::multiupload($request, 'images', 'gallery', $model->id, Gallery::class);
```

4. On the update method you can use:

For single image:
```
$data = $request->all();

$data['image'] = ImageManager::upload($request, 'image', 'gallery', $model->image);
```

For multiple images:
```
$data = $request->all();

ImageManager::multiupload($request, 'images', 'gallery', $model->id, Gallery::class);
```

5. To delete image you should pass filename and upload dir

For single image:
```
ImageManager::delete($model->image, 'gallery');
```

For multiple images:
```
ImageManager::multidelete($model->images, 'gallery');
```