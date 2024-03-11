<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use Exception;
use Validator;

class TaskController extends Controller
{
    public function index(Request $request) 
    {
        try{
            $getTasks = $this->task::with('notes')->get();
            return $this->sendResponse($getTasks, 'Tasks get successfully');
        }
        catch(Exception $e){
            return $this->sendError('something went wrong', 500);
        }
    }

    public function addTask(Request $request) 
    {
        try{
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|max:255',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'status' => 'required|in:New,Incomplete,Complete',
                'priority' => 'required|in:High,Medium,Low',
            ]);
    
            if($validator->fails()) {
                return $this->sendError($validator->errors()->first(), [], 422);
            }

            $task = $this->task;
            $task->subject = $request->subject;
            $task->description = $request->description;
            $task->start_date = $request->start_date;
            $task->due_date = $request->due_date;
            $task->status = $request->status;
            $task->priority = $request->priority;
            $task->save();
            
            $notesData = json_decode($request->notes);
            foreach ($notesData as $noteData) {
                $note = new Note();
                $note->subject = $noteData->subject;
                $note->note = $noteData->note;
                $note->task_id = $task->id;
                
                if (!empty($noteData->attachments)) {
                    $originalNames = [];
                    foreach ($noteData->attachments as $attachment) {
                        $originalNames[] = $attachment->getClientOriginalName();
                    }
                    $note->attachment = json_encode($originalNames);
                }

                $note->save();
            }
            return $this->sendResponse($task, 'Task added successfully');

        } catch(\Exception $e){
            return $this->sendError('something went wrong', 500);
        }
    }

    public function deleteTask($id) {
        try{
            $task = $this->task::findOrFail($id);
            $task->delete();
            return $this->sendResponse([],'Task delete successfully');

        } catch(\Exception $e){
            return $this->sendError('Task not found', 500);
        }
    }
}
