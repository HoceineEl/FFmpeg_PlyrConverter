@extends('layout')

@section('content')
    <div class="row">
        @foreach ($videos as $video)
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        @if ($video->path)
                            <a href="{{ route('videos.show', $video->id) }}">
                                <div class="ratio ratio-16x9">
                                    <video id="video-{{ $video->id }}" autoplay muted loop preload="auto">
                                        <source src="{{ asset('storage/videos/' . $video->path) }}#t=0,10" type="video/mp4">
                                    </video>
                                </div>
                            </a>
                        @else
                            <h1>
                                <i class="fas fa-video-camera fa-2x"></i>
                                <span class="d-none d-md-block">
                                    No video found
                                </span>
                            </h1>
                        @endif
                        <div class="card-footer">
                            <h5 class="card-title">{{ $video->title }}</h5>
                            <p class="card-text" id="duration-{{ $video->id }}"></p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var videos = document.querySelectorAll('video');

            videos.forEach(function(video) {
                var videoId = video.id.split('-')[1];
                var videoSrc = video.querySelector('source').src;

                if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = videoSrc;
                } else if (Hls.isSupported()) {
                    var hls = new Hls();
                    hls.loadSource(videoSrc);
                    hls.attachMedia(video);
                }
            });
        });
    </script>
@endsection
