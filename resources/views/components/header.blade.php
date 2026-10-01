<nav>
    <a href="/">Home</a>
    <a href="/selected">Purchase List</a>
    <form method="POST" action="{{ url('trash-all') }}">
        @csrf
        <button type="submit">Trash All</button>
    </form>
</nav>
