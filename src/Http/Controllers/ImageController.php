<?php

namespace Alexd\Image\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Croppa;
use Image;
use Storage;


class ImageController extends Controller
{
    /**
     * Ajax upload an image
     *
     * @param Request $request
     * @return string $filename
     */
    public function ajaxImageUpload(Request $request)
    {
        $rules = [
            'image' => 'image|mimes:jpg,jpeg,png',
        ];

        $this->validate($request, $rules);

        $image = $request->file('image');
        $filename = uniqid() . '_' . $image->getClientOriginalName();
        $image->storeAs($request->input('upload_dir'), $filename, 'public');

        return response()->json($filename);
    }

    /**
     * Ajax multiupload images
     *
     * @param Request $request
     * @return array|bool $output
     */
    public function ajaxImagesUpload(Request $request)
    {
        $rules = [
            'images.*' => 'image|mimes:jpg,jpeg,png',
        ];

        $this->validate($request, $rules);

        if(($request->file('images')[0])) {
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '_' . $image->getClientOriginalName();
                $image->storeAs($request->input('upload_dir'), $filename, 'public');

                $output[] = $filename;
            }
            return response()->json($output);
        }

        return response()->json(false);
    }

    /**
     * Ajax delete an image
     *
     * @param Request $request
     */
    public function ajaxImageDelete(Request $request)
    {
        $filename = $request->input('filename');
        $upload_dir = $request->input('upload_dir');

        if (Storage::disk('public')->exists($upload_dir . '/' . $filename)) {
            self::delete($filename, $upload_dir);
        }
    }

    /**
     * Upload an image
     *
     * @param $request
     * @param object $field_name
     * @param string $upload_dir
     * @param string $old_image
     * @return string
     */
    public static function upload($request, $field_name, $upload_dir, $old_image = null)
    {
        // delete an old image
        if(isset($old_image) && !empty($old_image) && $old_image !== $request->input($field_name)) {
            self::delete($old_image, $upload_dir);
        }

        if($request->hasFile($field_name)) {
            $image = $request->file($field_name);
            $filename = uniqid() . '_' . $image->getClientOriginalName();
            $image->storeAs($upload_dir, $filename, 'public');

            //self::delete(basename($old_image), $upload_dir);

            return $filename;
        }

        if($request->input($field_name) !== null) {
            return $request->input($field_name);
        }

        return '';
    }

    public static function multiupload($request, $field_name, $upload_dir, $model_id, $model, $imageable_type = '')
    {
        // if model is empty, use default model
        if(empty($model)) {
            $model = \Alexd\Image\Models\Image::class;
        }

        if ($request->input($field_name) !== null) {
            foreach ($request->input($field_name) as $key => $image) {
                if(isset($image['delete'])) {
                    self::delete($image['delete'], $upload_dir);
                    $model::destroy($key);
                } else {
                    $model::updateOrCreate(['id' => $key,], [
                        'imageable_id' => $model_id,
                        'imageable_type' => $imageable_type,
                        'filename' => $image['filename'],
                        'alt' => $image['alt'],
                        'title' => $image['title'],
                        'weight' => $image['weight']
                    ]);
                }
            }
        }

        if ($request->file($field_name) !== null) {
            foreach ($request->file($field_name) as $image) {
                $filename = uniqid() . '_' . $image->getClientOriginalName();
                $image->storeAs($upload_dir, $filename, 'public');

                $model::create([
                    'imageable_id' => $model_id,
                    'imageable_type' => $imageable_type,
                    'filename' => $filename
                ]);
            }
        }
    }

    /**
     * Delete an image
     *
     * @param string $filename
     * @param string $upload_dir
     */
    public static function delete($filename, $upload_dir)
    {
        if(isset($filename) && !empty($filename)) {
            Croppa::delete('/storage/' . $upload_dir . '/' . $filename);

            if (Storage::disk('public')->exists($upload_dir . '/crops/' . $filename)) {
                Croppa::delete('/storage/' . $upload_dir . '/crops/' . $filename);
            }
        }
    }

    /**
     * Multi delete images
     * @param object $images
     * @param string $upload_dir
     */
    public static function multidelete($images, $upload_dir)
    {
        if($images->count() > 0) {
            foreach ($images as $image) {
                Croppa::delete('/storage/' . $upload_dir . '/' . $image['filename']);

                if (Storage::disk('public')->exists($upload_dir . '/crops/' . $image['filename'])) {
                    Croppa::delete('/storage/' . $upload_dir . '/crops/' . $image['filename']);
                }
            }
        }
    }

    public static function destroy($gallery_id, $imageable_type)
    {
        \Alexd\Image\Models\Image::where([
            'imageable_type' => $imageable_type,
            'imageable_id' => $gallery_id
        ])->delete();
    }

    /**
     * Return or make a copy of the original image for crop manipulations
     *
     * @param Request $request
     * @return string
     */
    public function ajaxCropCheck(Request $request)
    {
        $filename = $request->input('filename');
        $upload_dir = $request->input('upload_dir');

        if(!Storage::disk('public')->exists($upload_dir . '/crops/' . $filename)) {
            Storage::disk('public')->copy($upload_dir . '/' . $filename, $upload_dir . '/crops/' . $filename);
        }

        return response()->json($filename);
    }

    /**
     * Crop the original image
     *
     * @param Request $request
     * @return string
     */
    public function ajaxCropSave(Request $request)
    {
        $x = (int)$request->input('x');
        $y = (int)$request->input('y');
        $width = (int)$request->input('width');
        $height = (int)$request->input('height');
        $filename = $request->input('filename');
        $upload_dir = $request->input('upload_dir');

        $image = Image::make(public_path('storage/' . $upload_dir . '/crops/' . $filename));
        $image->crop($width, $height, $x, $y)->save('storage/' . $upload_dir . '/' . $filename);
        
        Croppa::reset('storage/' . $upload_dir . '/' . $filename);

        return response()->json('/storage/' . $upload_dir . '/' . $filename);
    }

}