<?php
namespace App\Http\Requests;

use App\Models\Announcement;
use App\Models\Grade;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnnouncementReplyStoreRequest extends FormRequest
{
    protected $announcement;

    public function authorize(): bool
    {
        $user = $this->user();
        $grade = Grade::findOrFail($this->route('gradeId'));

        if (($user->id == $grade->teacher_id) || ($grade->members->contains($user->id))) {
            return true;
        }

        throw new HttpException(403, 'You are not authorized to perform this action.');
    }

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
                throw new HttpException(404, 'Announcement not found.');
            }
        }
        return $this->announcement;
    }
}
