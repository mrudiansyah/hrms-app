@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Memo Details</h1>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Memo #{{ $memo->id }}</h5>
            <p class="card-text">
                <strong>Memo ID:</strong> {{ $memo->id_memo }}<br>
                <strong>Part No:</strong> {{ $memo->part_no }}<br>
                <strong>Part Name:</strong> {{ $memo->part_name }}<br>
                <strong>Lines:</strong> {{ $memo->lines }}<br>
                <strong>JPH/GSPH:</strong> {{ $memo->jph_gsph }}<br>
                <strong>Process:</strong> {{ $memo->process }}<br>
                <strong>Date OT:</strong> {{ $memo->date_ot }}<br>
                <strong>Start OT:</strong> {{ $memo->start_ot }}<br>
                <strong>Finish OT:</strong> {{ $memo->finish_ot }}<br>
                <strong>Plan Quantity:</strong> {{ $memo->plan_qty }}<br>
                <strong>Reason OT:</strong> {{ $memo->reason_ot }}<br>
                <strong>Remark:</strong> {{ $memo->remark }}<br>
                <strong>Status:</strong> {{ $memo->status == 1 ? 'Active' : 'Inactive' }}
            </p>
            
            <a href="{{ route('memos.edit', $memo->id) }}" class="btn btn-primary">Edit</a>
            <form action="{{ route('memos.destroy', $memo->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
           <a href="{{ route('memos.index', ['id_memo' => $memo->id_memo]) }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection