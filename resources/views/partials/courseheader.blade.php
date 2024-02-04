<div class="row justify-content-center pt-3">
    <div class="col-12 text-center">
        @php $location = $course->location()->first(); @endphp
        <h3>{{$location->name}}</h3>
        <h3>{{$course->category}}</h3>
        <h3>{{$course->courseDateTxt}}</h3>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-12 p-0 text-center">
        <img style="width: 100%; max-width: 500px" src="{{asset("images/".$location->image)}}"></img>
    </div>
</div>
