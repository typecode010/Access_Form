@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 id="page-title" class="h3 mb-1">{{ $pageTitle }}</h2>
                <p class="mb-0 text-muted">Survey: <strong>{{ $survey->title }}</strong></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('creator.surveys.edit', $survey) }}" class="btn btn-outline-secondary">Back to Survey</a>
                <a href="{{ route('creator.surveys.questions.create', $survey) }}" class="btn btn-primary">Add Question</a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        @php
            $orderedIds = $questions->pluck('id')->values()->all();
        @endphp

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <caption class="visually-hidden">Questions for {{ $survey->title }}</caption>
                <thead class="table-light">
                    <tr>
                        <th scope="col">Position</th>
                        <th scope="col">Type</th>
                        <th scope="col">Prompt</th>
                        <th scope="col">Required</th>
                        <th scope="col">Options</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($questions as $index => $question)
                        @php
                            $moveUpIds = $orderedIds;
                            if ($index > 0) {
                                $temp = $moveUpIds[$index - 1];
                                $moveUpIds[$index - 1] = $moveUpIds[$index];
                                $moveUpIds[$index] = $temp;
                            }

                            $moveDownIds = $orderedIds;
                            if ($index < count($orderedIds) - 1) {
                                $temp = $moveDownIds[$index + 1];
                                $moveDownIds[$index + 1] = $moveDownIds[$index];
                                $moveDownIds[$index] = $temp;
                            }
                        @endphp
                        <tr>
                            <td>{{ $question->position }}</td>
                            <td>{{ $questionTypes[$question->type] ?? $question->type }}</td>
                            <td>{{ $question->prompt }}</td>
                            <td>{{ $question->is_required ? 'Yes' : 'No' }}</td>
                            <td>{{ $question->requiresOptions() ? $question->options->count() : 'N/A' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <a href="{{ route('creator.surveys.questions.edit', [$survey, $question]) }}" class="btn btn-sm btn-outline-primary">Edit</a>

                                    <form method="POST" action="{{ route('creator.surveys.questions.destroy', [$survey, $question]) }}" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('creator.surveys.questions.reorder', $survey) }}">
                                        @csrf
                                        @foreach ($moveUpIds as $orderedId)
                                            <input type="hidden" name="ordered_ids[]" value="{{ $orderedId }}">
                                        @endforeach
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" @disabled($index === 0)>Move Up</button>
                                    </form>

                                    <form method="POST" action="{{ route('creator.surveys.questions.reorder', $survey) }}">
                                        @csrf
                                        @foreach ($moveDownIds as $orderedId)
                                            <input type="hidden" name="ordered_ids[]" value="{{ $orderedId }}">
                                        @endforeach
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" @disabled($index === count($orderedIds) - 1)>Move Down</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No questions yet. Add your first question to start the builder.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
