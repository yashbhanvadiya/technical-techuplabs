@extends('layouts.layout')
@section('content')
    <x-app-layout>
        <div class="container mt-3">
            <div class="row">
                <div class="col-md-12 text-right">
                    <a href="{{ route('task') }}" class="btn btn-primary bg-dark">Add Task</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Task Subject</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Note Subject</th>
                                <th>Note</th>
                                <th>Attachments</th>
                                <th>Start Date</th>
                                <th>Due Date</th>
                                <th colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration . '.' }}
                                    </td>
                                    <td>{{ $task->subject }}</td>
                                    <td>{{ $task->description }}</td>
                                    <td>{{ $task->status }}</td>
                                    <td>{{ $task->priority }}</td>
                                    <td>
                                        @if ($task->notes->isNotEmpty())
                                            @foreach ($task->notes as $index => $note)
                                                {{ $note->subject }}<br>
                                            @endforeach
                                        @else
                                            No notes found
                                        @endif
                                    </td>
                                    <td>
                                        @if ($task->notes->isNotEmpty())
                                            @foreach ($task->notes as $index => $note)
                                                {{ $note->note }}<br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if ($task->notes->isNotEmpty())
                                            @foreach ($task->notes as $note)
                                                @if (!empty($note->attachment))
                                                    <ul>
                                                        @foreach (json_decode($note->attachment) as $attachment)
                                                            <li>{{ $attachment }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    No attachments
                                                @endif
                                            @endforeach
                                        @else
                                            No notes found
                                        @endif
                                    </td>
                                    <td>{{ $task->start_date }}</td>
                                    <td>{{ $task->due_date }}</td>
                                    <td>
                                        <a href="{{ route('edit-task', encrypt($task->id)) }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                    </td>
                                    <td>
                                        <form action="{{ route('delete-task', encrypt($task->id)) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $tasks->links() !!}
                </div>
            </div>
        </div>
    </x-app-layout>
@endsection
