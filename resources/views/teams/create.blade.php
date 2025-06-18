@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary fw-bold my-4">Create Team</h1>
        <form action="{{ route('teams.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Team Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label for="leader_id" class="form-label">Leader Member ID (Search by ID or Name)</label>
                <input type="text" class="form-control" name="leader_id" id="leader_id" list="members" oninput="searchMember(this)">
                <datalist id="members"></datalist>
            </div>

            <button type="submit" class="btn btn-success">Create</button>
            <a href="{{ route('teams.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        function searchMember(input) {
            let query = input.value;
            if (query.length < 1) return;

            fetch('/api/members/search?q=' + query)
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
