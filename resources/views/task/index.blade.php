@extends('layouts.layout')
@section('css')
    <style>
        #notes .note {
            border: 1px solid #cecece;
            padding: 20px 32px;
            border-radius: 4px;
            margin-bottom: 30px
        }

        form label span {
            color: #ff0000;
        }
    </style>
@endsection

@section('content')
    <x-app-layout>
        <div class="container mt-3">
            <div class="row">
                <div class="col-md-12 text-right">
                    <a href="{{ route('show-task') }}" class="btn btn-primary bg-dark">Show Task</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <form id="addTask" method="POST" action="{{ route('add-task') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="subject">Subject<span>*</span></label>
                            <input type="text" name="subject" id="subject" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="start_date">Start Date<span>*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="due_date">Due Date<span>*</span></label>
                            <input type="date" name="due_date" id="due_date" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">Status<span>*</span></label>
                            <select name="status" id="status" class="form-control">
                                <option value="">-- Select Status --</option>
                                <option value="New">New</option>
                                <option value="Incomplete">Incomplete</option>
                                <option value="Complete">Complete</option>
                            </select>
                        </div>

                        <div class="form-group mb-5">
                            <label for="priority">Priority<span>*</span></label>
                            <select name="priority" id="priority" class="form-control">
                                <option value="">-- Select Priority --</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h2 class="mb-3">Notes:</h2>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary bg-dark" id="add_note">Add Note</button>
                            </div>
                        </div>

                        <div id="notes">
                            <div class="note">
                                <div class="form-group mb-3">
                                    <label for="notes[0][subject]">Note Subject:<span>*</span></label>
                                    <input type="text" class="form-control" name="notes[0][subject]" id="note_subject_0">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="notes[0][note]">Note:<span>*</span></label>
                                    <textarea class="form-control" name="notes[0][note]" id="note_0"></textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="notes[0][note]">Attachment:</label>
                                    <input type="file" name="notes[0][attachments][]" multiple>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary bg-dark">Create Task</button>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#add_note').on('click', function() {
                var noteCount = $('.note').length;
                var newNote = `
                <div class="note">

                    <div class="form-group mb-3">
                        <label for="notes[${noteCount}][subject]">Note Subject:</label>
                        <input type="text" class="form-control" name="notes[${noteCount}][subject]" class="note_subject">
                    </div>

                    <div class="form-group mb-3">
                        <label for="notes[${noteCount}][note]">Note:</label>
                        <textarea class="form-control" name="notes[${noteCount}][note]" class="note_content"></textarea>
                    </div>

                    <div class="form-group mb-3 row">
                        <div class="col-md-8">
                            <label for="notes[${noteCount}][note]">Attachment:</label>
                            <input type="file" name="notes[${noteCount}][attachments][]" class="note_attachments" multiple>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="remove-note"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </div>
                    </div>

                </div>
            `;
                $('#notes').append(newNote);
            });

            $('#notes').on('click', '.remove-note', function() {
                $(this).parent().parent().parent().remove();
            });

            // Validation
            $('#addTask').validate({
                rules: {
                    subject: {
                        required: true
                    },
                    start_date: {
                        required: true
                    },
                    due_date: {
                        required: true
                    },
                    status: {
                        required: true
                    },
                    priority: {
                        required: true
                    },
                    'notes[0][subject]': {
                        required: true
                    },
                    'notes[0][note]': {
                        required: true
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                }
            });
        });
    </script>
@endsection
