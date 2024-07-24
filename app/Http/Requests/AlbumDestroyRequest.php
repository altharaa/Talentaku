<?php

namespace App\Http\Requests;

use App\Models\Album;
use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AlbumDestroyRequest extends FormRequest
{
    protected $album;
    protected $grade;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $gradeId = $this->route('gradeId');
        $albumId = $this->route('albumId');

        $this->grade = Grade::findOrFail($gradeId);
        $this->album = Album::where('id', $albumId)->where('grade_id', $gradeId)->first();

        if (!$this->album) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Album not found for the specified grade.',
            ], 404));
        }

        return $this->grade->teacher_id == $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function getAlbum()
    {
        return $this->album;
    }
}
