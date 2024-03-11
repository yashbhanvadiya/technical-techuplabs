<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Validator;

class TaskController extends Controller
{
    public function index(Request $request) {
        try{
            return view('task.index');
        } catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function addTask(Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|max:255',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'status' => 'required|in:New,Incomplete,Complete',
                'priority' => 'required|in:High,Medium,Low',
                'notes.*.subject' => 'required|string|max:255',
                'notes.*.note' => 'required|string'
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

            foreach ($request->notes as $noteData) {
                $note = new Note();
                $note->subject = $noteData['subject'];
                $note->note = $noteData['note'];
                $note->task_id = $task->id;
                
                if (!empty($noteData['attachments'])) {
                    $originalNames = [];
                    foreach ($noteData['attachments'] as $attachment) {
                        $originalNames[] = $attachment->getClientOriginalName();
                    }
                    $note->attachment = json_encode($originalNames);
                }
                $note->save();
            }

            return redirect()->back()->with('success', 'Task created successfully');

        } catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function showTask(Request $request) {
        try{
            $tasks = $this->task::with('notes')->orderBy('priority', 'asc')->paginate(10);
            return view('task.show', compact('tasks'));

        } catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function deleteTask($id) {
        try{
            $id = decrypt($id);
            $task = $this->task::findOrFail($id);
            $task->delete();
            return redirect()->back()->with('success', 'Task deleted successfully');

        } catch(\Exception $e){
            return redirect()->back()->with('error', 'Task Delete is not successfully');
        }
    }

    public function editTask($id)
    {
        try{
            $id = decrypt($id);
            $task = $this->task::findOrFail($id);
            $notes = $task->notes;
            return view('task.edit', compact('task', 'notes'));

        } catch(\Exception $e){
            return redirect()->back()->with('error', 'Error edit task');
        }
    }

    public function updateTask(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|max:255',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'status' => 'required|in:New,Incomplete,Complete',
                'priority' => 'required|in:High,Medium,Low',
                'notes.*.subject' => 'required|string|max:255',
                'notes.*.note' => 'required|string'
            ]);
    
            if($validator->fails()) {
                return $this->sendError($validator->errors()->first(), [], 422);
            }
    
            $task = $this->task::findOrFail($id);
            $task->subject = $request->subject;
            $task->description = $request->description;
            $task->start_date = $request->start_date;
            $task->due_date = $request->due_date;
            $task->status = $request->status;
            $task->priority = $request->priority;
            $task->save();

            if ($request->notes) {
                $existingNoteIds = $task->notes->pluck('id')->toArray();
    
                foreach ($request->notes as $noteData) {
                    $noteId = $noteData['id'];
                    $note = null;

                    if ($noteId) {
                        $note = Note::findOrFail($noteId);
                    } else {
                        $note = new Note();
                        $note->task_id = $task->id;
                    }

                    $note->subject = $noteData['subject'];
                    $note->note = $noteData['note'];
    
                    if (!empty($noteData['attachments'])) {
                        $attachmentPaths = [];
                        foreach ($noteData['attachments'] as $attachment) {
                            $attachmentPaths[] = $attachment->getClientOriginalName();
                        }
                        $note->attachment = json_encode($attachmentPaths);
                    }
                    $note->save();
    
                    if ($noteId) {
                        $key = array_search($noteId, $existingNoteIds);
                        if ($key !== false) {
                            unset($existingNoteIds[$key]);
                        }
                    }
                }
    
                if (!empty($existingNoteIds)) {
                    Note::whereIn('id', $existingNoteIds)->delete();
                }
            }
            return redirect()->back()->with('success', 'Task updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating task');
        }
    }
}
