import videojs from 'video.js';
import 'video.js/dist/video-js.css';
import 'videojs-resolution-switcher';

document.addEventListener('DOMContentLoaded', () => {
    const player = videojs('video', {
        plugins: {
            videoJsResolutionSwitcher: {
                default: 'high',
                dynamicLabel: true
            }
        }
    });

    // Add video sources
    player.src([
        {
            src: 'http://example.com/video/144p_645bbd193a93c.mp4',
            type: 'video/mp4',
            label: '144'
        },
        {
            src: 'http://example.com/video/360p_645bbd193a93c.mp4',
            type: 'video/mp4',
            label: '360'
        },
        {
            src: 'http://example.com/video/720p_645bbd193a93c.mp4',
            type: 'video/mp4',
            label: '720'
        }
    ]);
});
