@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Memo List</h1>
    <a href="{{ route('memos.create') }}" class="btn btn-primary mb-3">Create New Memo</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Memo ID</th>
                <th>Part No</th>
                <th>Part Name</th>
                <th>Date OT</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($memos as $memo)
            <tr>
                <td>{{ $memo->id }}</td>
                <td>{{ $memo->id_memo }}</td>
                <td>{{ $memo->part_no }}</td>
                <td>{{ $memo->part_name }}</td>
                <td>{{ $memo->date_ot }}</td>
                <td>{{ $memo->status }}</td>
                <td>
                    <a href="{{ route('memos.show', $memo->id) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('memos.edit', $memo->id) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('memos.destroy', $memo->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection