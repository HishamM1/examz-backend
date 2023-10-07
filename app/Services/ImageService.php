<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Str;

class ImageService
{
    public function upload($image)
    {
        $image_name = $this->imageNameGenerator($image);
        $image->move(public_path('images'), $image_name);

        return asset('images/' . $image_name);
    }
    public function update($image, $model, $modelId)
    {
        $image_name = $this->imageNameGenerator($image);

        $image->move(public_path('images'), $image_name);

        $models = [
            'user' => [
                'class' => User::class,
                'attribute' => 'profile_picture',
            ],
            'question' => [
                'class' => Question::class,
                'attribute' => 'image',
            ],
            'announcement' => [
                'class' => Announcement::class,
                'attribute' => 'media',
            ],
        ];

        if (!array_key_exists($model, $models)) {
            abort(404);
        }

        $modelClass = $models[$model]['class'];
        $attribute = $models[$model]['attribute'];
    
        $modelInstance = $modelClass::findOrFail($modelId);
        $this->delete($modelInstance->$attribute);
        $modelInstance->update([
            $attribute => asset('images/' . $image_name),
        ]);

        return asset('images/' . $image_name);
    }

    public function delete($media)
    {
        if ($media && substr($media, 29) != 'default.png' && file_exists(public_path(substr($media, 29)))) {
            unlink(public_path(substr($media, 29)));
        }
    }

    protected function imageNameGenerator($image)
    {
        $user = auth()->user();

        return $user->id . '-' . Str::random(5) . '-' . time() . '.' . $image->extension();
    }
}
