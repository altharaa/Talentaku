<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    protected $announcement;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $grade = Grade::findOrFail($this->route('gradeId'));

        if (($user->id == $grade->teacher_id) || ($grade->members->contains($user->id))) {
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
            //
        ];
    }

    public function getAnnouncement()
    {
        $announcement = Announcement::find($this->route('announcementId'));

        if (!$announcement) {
            return response()->json([
                'status' => 'error',
                'message' => 'Announcement not found.',
            ], 404);
        }

        $user = auth()->user();

        if ($announcement->user_id != $user->id && $announcement->grade->teacher_id != $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to access this announcement.',
            ], 403);
        }

        return $announcement;
    }
}
