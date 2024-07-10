<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    private function uploadNewMedia($newMedia, $comment)
    {
        $mediaData = [];
        $maxFiles = 5;

        if (!is_array($newMedia)) {
            return $mediaData;
        }

        if (count($newMedia) > $maxFiles) {
            throw new \Exception("Maximum of {$maxFiles} files allowed");
        }

        foreach ($newMedia as $mediaFile) {
            if ($mediaFile->getSize() > 20 * 1024 * 1024) {
                throw new \Exception('File size exceeds 20MB limit');
            }

            $fileName = Str::uuid() . '.' . $mediaFile->getClientOriginalExtension();
            $path = $mediaFile->storeAs('comment', $fileName, 'public');

            if (!$path) {
                throw new \Exception('Failed to upload file');
            }

            $commentMedia = $comment->media()->create([
                'file_path' => Storage::url($path),
                'file_name' => $fileName,
                'original_name' => $mediaFile->getClientOriginalName(),
                'file_size' => $mediaFile->getSize(),
                'file_type' => $mediaFile->getMimeType(),
            ]);

            $mediaData[] = [
                'id' => $commentMedia->id,
                'file_name' => $fileName,
                'original_name' => $mediaFile->getClientOriginalName(),
                'file_path' => $commentMedia->file_path,
                'file_size' => $mediaFile->getSize(),
                'file_type' => $mediaFile->getMimeType(),
            ];
        }

        return $mediaData;
    }

    private function deleteMedia($mediaToDelete, $comment)
    {
        $deletedMedia = [];
        if (is_array($mediaToDelete) && !empty($mediaToDelete)) {
            foreach ($mediaToDelete as $mediaId) {
                $media = $comment->media()->find($mediaId);
                if ($media) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $media->file_path));
                    $media->delete();
                    $deletedMedia[] = $mediaId;
                }
            }
        }
        return $deletedMedia;
    }

    public function store(Request $request, $gradeId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (($user->id !== $grade->teacher_id) && (!$grade->members->contains($user->id))) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create comments for this grade.',
            ], 403);
        }

        $request->validate([
            'comments' => 'required|string',
            'media' => 'nullable|array',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi,pdf,docx,ppt|max:20480',
        ]);

        DB::beginTransaction();
        try {
            $comment = new Comment();
            $comment->user_id = $user->id;
            $comment->grade_id = $grade->id;
            $comment->comments = $request->comments;
            $comment->save();

            $mediaData = $this->uploadNewMedia($request->file('media'), $comment);

            $comment->load('user', 'grade');

            $formattedComment = [
                'id' => $comment->id,
                'user' => [
                    'id' => $comment->user_id,
                    'name' => optional($comment->user)->name,
                    'photo' => optional($comment->user)->photo
                ],
                'grade' => [
                    'id' => $comment->grade_id,
                    'name' => optional($comment->grade)->name,
                ],
                'comments' => explode(PHP_EOL, $comment->comments),
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'media' => $mediaData,
            ];

            DB::commit();

            return response()->json([
                'message' => 'Comment created successfully',
                'comment' => $formattedComment,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $gradeId, $commentId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (($user->id !== $grade->teacher_id) && (!$grade->members->contains($user->id))) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create comments for this grade.',
            ], 403);
        }

        $request->validate([
            'comments' => 'required',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi,pdf,ppt,docx|max:20480',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'exists:comment_media,id'
        ]);

        DB::beginTransaction();

        try {

            $comment = Comment::find($commentId);
            $comment->comments = $request->comments;
            $comment->save();

            $deletedMedia = [];
    
            $deletedMedia = $this->deleteMedia($request->delete_media, $comment);
            $mediaData = $this->uploadNewMedia($request->file('media'), $comment);

            DB::commit();

            $formattedComment = [
                'id' => $comment->id,
                'user' => [
                    'id' => $comment->user_id,
                    'name' => $comment->user->name,
                    'photo' => $comment->user->photo
                ],
                'grade_id' => [
                    'id' => $comment->grade_id,
                    'name' => $comment->grade->name,
                ],
                'comments' => explode(PHP_EOL,$comment->comments),
                'media' => $mediaData,
                'delete_media' => $deletedMedia,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
            
            ];

            return response()->json([
                'message' => 'Comment updated successfully',
                'comment' => $formattedComment,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy( Request $request,$gradeId, $commentId)
    {
        $user = $request->user();
        $grade = Grade::findOrFail($gradeId);

        if (($user->id !== $grade->teacher_id) && (!$grade->members->contains($user->id))) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to create comments for this grade.',
            ], 403);
        }
        
        $comment = Comment::find($commentId);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
