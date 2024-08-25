<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use App\Models\AnnouncementReply;
use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AnnouncementReplyUpdateRequest extends FormRequest
{
    protected $announcementReply;
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

    public function getAnnouncementReply()
    {
        if (!$this->announcementReply) {
            $this->announcementReply = AnnouncementReply::where('id', $this->route('replyId'))
                ->where('announce_id', $this->route('announcementId'))
                ->first();
            if (!$this->announcementReply)
            {
                throw new HttpResponseException(response()->json([
                    'status' => 'error',
                    'message' => 'Announcement Reply not found.',
                ], 404));
            }
            if ($this->announcementReply->user_id !== $this->user()->id) {
                throw new  HttpResponseException(response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to update this reply.',
                ], 403));
            }
        }
        return $this->announcementReply;
    }
}
