@extends("layouts.main")

@section("mainData")
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
        <div class="border-bottom mb-3 pt-3 pb-2">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1 class="h2">{{ $event->name }}</h1>
            </div>
            <span class="h6">{{ date('F d, Y', strtotime($event->date)) }}</span>
        </div>

        <div class="mb-3 pt-3 pb-2">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h2 class="h4">Create new room</h2>
            </div>
        </div>

        <form class="needs-validation" novalidate action="{{ route('room.store', $event->id) }}" method="post">
            @csrf

            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label for="inputName">Name</label>
                    <!-- adding the class is-invalid to the input, shows the invalid feedback below -->
                    <input type="text" class="form-control{{ $errors->first('name')?' is-invalid':'' }}"
                           id="inputName" name="name"
                           placeholder="" value="{{ old('name')??'' }}">
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label for="selectChannel">Channel</label>
                    <select class="form-control{{ $errors->first('channel_id')?' is-invalid':'' }}" id="selectChannel" name="channel_id">
                        @foreach($event->channel as $channel)
                        <option value="{{ $channel->id }}"{{ (old('channel_id')==$channel->id)?' selected':'' }}>{{ $channel->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        {{ $errors->first('channel_id') }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label for="inputCapacity">Capacity</label>
                    <input type="number" class="form-control{{ $errors->first('capacity')?' is-invalid':'' }}" id="inputCapacity"
                           name="capacity" placeholder="" value="">
                    <div class="invalid-feedback">
                        {{ $errors->first('capacity') }}
                    </div>
                </div>
            </div>

            <hr class="mb-4">
            <button class="btn btn-primary" type="submit">Save room</button>
            <a href="{{ route('event.show', $event->id) }}" class="btn btn-link">Cancel</a>
        </form>

    </main>
@endsection
