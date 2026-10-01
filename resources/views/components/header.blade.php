<nav>
    <a href="/">Home</a>
    <a href="/selected">Purchase List</a>
    <form method="POST" action="{{ url('trash-all') }}">
        @csrf
        <button type="submit">Trash All</button>
    </form>
    <form method="POST" action="{{ url('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</nav>
