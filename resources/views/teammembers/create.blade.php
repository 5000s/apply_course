@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary fw-bold mb-4">Add Member to Team: {{ $team->name }}</h1>

        <form action="{{ route('teammembers.store', $team->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="user_id" class="form-label">Member ID or Name (Search)</label>
                <input type="text"
                       name="user_id"
                       id="user_id"
                       class="form-control"
                       list="members"
                       oninput="searchMember(this)"
                       required>
                <datalist id="members"></datalist>
            </div>

            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <input type="text" name="position" id="position" class="form-control">
            </div>


            <div class="mb-3">
                <label for="join_at" class="form-label">Join At</label>
                <input type="date" name="join_at" id="join_at" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>

            <button type="submit" class="btn btn-success">Add</button>
            <a href="{{ route('teammembers.index', $team->id) }}" class="btn btn-secondary">Back</a>
        </form>
    </div>

    <script>
        function searchMember(input) {
            let query = input.value;
            if (query.length < 1) return;

            fetch('/api/members/search?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    let datalist = document.getElementById('members');
                    datalist.innerHTML = '';
                    data.forEach(member => {
                        let option = document.createElement('option');
                        option.value = member.id;
                        option.text = member.name + ' ' + member.surname;
                        datalist.appendChild(option);
                    });
                });
        }
    </script>
@endsection
