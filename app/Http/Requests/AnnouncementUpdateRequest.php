<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use App\Models\Grade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AnnouncementUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $gradeId = $this->route('gradeId');

        try {
            $this->grade = Grade::findOrFail($gradeId);
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Grade not found.',
            ], 404));
        }

        $roles = $user->roles()->pluck('name')->toArray();

        return (in_array('Guru SD', $roles) || in_array('Guru KB', $roles))
            && $user->id == $this->grade->teacher_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'announcements' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi,pdf,ppt,docx|max:20480',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'exists:announcement_media,id'
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
