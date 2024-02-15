<style>
    span.select2-selection.select2-selection--multiple {
        width: 71em !important;
    }
</style>
<form id="post_update_form">
    @csrf
    <input type="hidden" id="id" name="id" value="{{$post->id}}">

    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Post Title*</label>
        <div class="col-md-10">
           <input type="text" class="form-control" id="title" name="title" value="{{$post->title}}">
        </div>
    </div>

    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Post Content*</label>
        <div class="col-md-10">
            <textarea class="form-control" name="content" id="content" cols="30" rows="10" required>{{$post->content}}</textarea>
        </div>
    </div>

    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Category*</label>
        <div class="col-md-10">
            @php
                $cat_explode = explode(',',$post->category_id);
            @endphp
            <select class="select2 form-control select2-multiple" multiple="multiple" data-placeholder="Select a Category" name="category[]" id="category" required>
                @foreach ($category as $cat)
                    <option @if(in_array($cat->id,$cat_explode)) selected @endif value="{{$cat->id}}">{{$cat->category}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="post_update">
            Update
        </button>
    </div>
</form>

<script>
    var form = $("#post_update_form");

    $("#category").select2({
        placeholder: "Select a Category",
        allowClear: true
    });

    $("#post_update").click(function () {
        if (!form.valid()) { // Not Valid
            return false;
        } else {
            var data    = form.serialize();
            var id      = $("#id").val();
            var url_set = "{{ route('posts.update', ':id') }}";
            url_set     = url_set.replace(':id', id);
            $.ajax({
                type: 'PUT',
                url: url_set,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#post_update').html('....Please wait');
                },
                success: function(response) {
                    toastr.success(response.message);
                    $("#commonModal").modal('hide');
                    datatable();
                },
                complete: function(response) {
                    $('#post_update').html('Update');
                },
                error: function(response) {
                    toastr.error("Something Wrong!");
                },
            });
        }
    });

    $(function ()
    {
        form.validate({
            rules: {
                title: {
                    required: true,
                    remote: {
                        url: '{{ route("checkduplicate") }}',
                        data: { 'form_name' : "post", "id" : $("#id").val() },
                        type: "GET"
                    }
                }
            },
            messages: {
                title: {
                    remote: "Sorry, that title already exists!"
                }
            }
        });
    });

</script>
        