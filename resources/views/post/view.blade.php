<form>
    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Post Title</label>
        <div class="col-md-10">
           {{$post->title}}
        </div>
    </div>

    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Post Content</label>
        <div class="col-md-10">
            {{$post->content}}
        </div>
    </div>

    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Category</label>
        <div class="col-md-10">
            @php
                $cat_explode = explode(',',$post->category);
            @endphp
            
            @forelse ($cat_explode as $cat)
                <span class="badge bg-success">{{$cat}}</span>
            @empty
                <span class="badge bg-success">-</span>
            @endforelse
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
    </div>
</form>