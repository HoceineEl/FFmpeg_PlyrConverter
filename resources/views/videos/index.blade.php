@extends('layout')

@section('content')
    <div class="row">
        @foreach ($videos as $video)
            <div class="col-md-4">
                <div class="card mb-4 ">
                    <div class="card-body">
                        @if ($video->resolutions()->first() && $video->resolutions->where('resolution', '720')->first())
                            <a href="{{ route('videos.show', $video->id) }}">
                                <div class="ratio ratio-16x9">
                                    <video id="video-{{ $video->id }}" class="ratio-content rounded-3" muted loop>
                                        <source
                                            src="{{ asset('storage/videos/' . $video->resolutions->where('resolution', '720')->first()->path) }}"
                                            type="video/webm">
                                        Your browser does not support the video tag.
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const videos = document.querySelectorAll('video');

            videos.forEach(video => {
                let isHovered = false;

                video.addEventListener('mouseover', () => {
                    isHovered = true;
                    video.play();
                });

                video.addEventListener('mouseout', () => {
                    isHovered = false;
                    video.pause();
                });

                video.addEventListener('loadedmetadata', () => {
                    const duration = video.duration;
                    const formattedDuration = formatDuration(duration);
                    const videoId = video.id.split('-')[1];
                    const durationElement = document.getElementById(`duration-${videoId}`);
                    durationElement.textContent = formattedDuration;

                    // Start playing the video if it was hovered before metadata loaded
                    if (isHovered) {
                        video.play();
                    }
                });
            });

            function formatDuration(duration) {
                const hours = Math.floor(duration / 3600);
                const minutes = Math.floor((duration % 3600) / 60);
                const seconds = Math.floor(duration % 60);
                return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        });
    </script>
@endsection
