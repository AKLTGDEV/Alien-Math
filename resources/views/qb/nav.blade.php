<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ $QB_NAV_PAGE == 'INDEX' ? 'active' : '' }}" href="{{ route('qbank_index') }}">
            Questions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $QB_NAV_PAGE == 'TOPICS' ? 'active' : '' }}" href="{{ route('qbank_index_topics') }}">
            Topics
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $QB_NAV_PAGE == 'SUBTOPICS' ? 'active' : '' }}" href="{{ route('qbank_index_subtopics') }}">
            Sub-Topics
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $QB_NAV_PAGE == 'TESTS' ? 'active' : '' }}" href="{{ route('qbank_index_tests') }}">
            Tests
        </a>
    </li>
</ul>