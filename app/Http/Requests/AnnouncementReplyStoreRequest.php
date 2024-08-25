<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AnnouncementReplyStoreRequest extends FormRequest
{
    protected $announcement;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $grade = Grade::findOrFail($this->route('gradeId'));

        if (($user->id === $grade->teacher_id) || ($grade->members->contains($user->id))) {
            return true;
        }

        throw new \HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'You are not authorized to perform this action.',
        ], 403));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'replies' => 'required|string',
        ];
    }

    public function getAnnouncement()
    {
        if (!$this->announcement) {
            $this->announcement = Announcement::where('id', $this->route('announcementId'))
                ->where('grade_id', $this->route('gradeId'))
                ->first();
            if (!$this->announcement)
            {
                throw new HttpResponseException(response()->json([
                    'status' => 'error',
                    'message' => 'Announcement not found.',
                ], 404));
            }
        }
        return $this->announcement;
    }
}
