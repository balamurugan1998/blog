<style>
    span.select2-selection.select2-selection--multiple {
        width: 71em;
    }
</style>
<form id="post_create_form" class="validation">
    @csrf
    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Post Title*</label>
        <div class="col-md-10">
           <input type="text" class="form-control" id="title" name="title">
        </div>
    </div>

    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Post Content*</label>
        <div class="col-md-10">
            <textarea class="form-control" name="content" id="content" cols="30" rows="10" required></textarea>
        </div>
    </div>

    <div class="form-group row">
        <label for="example-text-input" class="col-md-2 col-form-label">Category*</label>
        <div class="col-md-10">
            <select class="select2 form-control select2-multiple" multiple="multiple" data-placeholder="Select a Category" name="category[]" id="category_select" required>
                @foreach ($category as $cat)
                    <option value="{{$cat->id}}">{{$cat->category}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="post_create">Create New</button>
    </div>
</form>

<script>
    var form = $("#post_create_form");

    $("#category_select").select2({
        placeholder: "Select a Category",
        allowClear: true
    });

    $("#post_create").click(function () {
        if (!form.valid()) { // Not Valid
            return false;
        } else {
            var data = form.serialize();

            $.ajax({
                type: 'POST',
                url: "{{route('posts.store')}}",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#post_create').html('....Please wait');
                },
                success: function(response) {
                    toastr.success(response.message);
                    $("#commonModal").modal('hide');
                    datatable();
                },
                complete: function(response) {
                    $('#post_create').html('Create New');
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
                        data: { 'form_name' : "post" },
                        type: "GET"
                    }
                }
            },
            messages: {
                title: {
                    remote: "Sorry, that title already taken!"
                }
            }
        });
    });
</script>