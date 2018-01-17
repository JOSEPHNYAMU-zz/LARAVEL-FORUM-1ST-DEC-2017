@component('profiles.activities.activity')
    @slot('heading')
        {{--{!! $activity->subject->favourite !!}--}}
        <a href = "{!! $activity->subject->favourited->path() !!}">
            {!! $profileUser->name !!} favourited a reply
        </a>
    @endslot

    @slot('body')
        {{$activity->subject->favourited->body}}
    @endslot
@endcomponent