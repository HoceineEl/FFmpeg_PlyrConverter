@extends('layout')

@section('content')
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
            integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card p-5">
        <h1>Video Upload Form</h1>
        <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="video" class="form-label">Video</label>
                <input type="file" class="form-control-file" id="video" name="video" required>
                <div class="progress mt-2" style="display: none;">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    {{-- <script>
        // Track file upload progress
        $(document).ready(function() {
            $('#upload-form').on('submit', function(event) {
                event.preventDefault();

                var formData = new FormData(this);
                var progressBar = $('.progress');
                var progressBarValue = progressBar.find('.progress-bar');

                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(e) {
                            if (e.lengthComputable) {
                                var progress = Math.round((e.loaded / e.total) * 100);
                                progressBarValue.css('width', progress + '%').attr(
                                    'aria-valuenow', progress);
                            }
                        });
                        return xhr;
                    },
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        progressBar.show();
                    },
                    success: function(response) {
                        progressBar.hide();
                        $('#upload-form')[0].reset();
                        // Handle success response or redirect as needed
                    },
                    error: function(xhr, status, error) {
                        progressBar.hide();
                        // Handle error response or display error message
                    }
                });
            });
        });
    </script> --}}
@endsection
