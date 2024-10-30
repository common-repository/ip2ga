jQuery(document).ready(function($) {
    var requestQueue = [];
    var isProcessing = false;

    // Function to process the request queue
    function processQueue() {
        if (requestQueue.length > 0 && !isProcessing) {
            isProcessing = true;
            var request = requestQueue.shift();

            $.ajax(request).always(function() {
                isProcessing = false;
                processQueue();
            });
        }
    }

    // Function to add a request to the queue
    function enqueueRequest(settings) {
        requestQueue.push(settings);
        processQueue();
    }

    // Function to convert serialized form data to an object
    function serializeArrayToObject(serializedArray) {
        var result = {};
        serializedArray.forEach(function(item) {
            result[item.name] = item.value;
        });
        return result;
    }

    // Event handler for popups
    $('.popup').on('show.bs.modal', function() {
        var eventData = {
            action: 'ga_ip2c_event',
            type: 'popup', // Event type
            category: 'Popup',
            label: $(this).attr('id') || 'Popup',
            page: window.location.pathname,
            title: document.title
        };

        enqueueRequest({
            url: ga_ip2c_ajax.ajax_url,
            method: 'POST',
            data: $.extend(eventData, { security: ga_ip2c_ajax.nonce })
        });
    });

    // Event handler for image clicks
    $('img').on('click', function() {
        var eventData = {
            action: 'ga_ip2c_event',
            type: 'image', // Event type
            category: 'Image',
            label: $(this).attr('alt') || 'Image',
            page: window.location.pathname,
            title: document.title
        };

        enqueueRequest({
            url: ga_ip2c_ajax.ajax_url,
            method: 'POST',
            data: $.extend(eventData, { security: ga_ip2c_ajax.nonce })
        });
    });

    // Event handler for button clicks
    $('button').on('click', function() {
        var eventData = {
            action: 'ga_ip2c_event',
            type: 'button_click',
            category: 'Button Click',
            label: $(this).text(),
            page: window.location.pathname,
            title: document.title
        };

        enqueueRequest({
            url: ga_ip2c_ajax.ajax_url,
            method: 'POST',
            data: $.extend(eventData, { security: ga_ip2c_ajax.nonce })
        });
    });

    // Event handler for form submissions
    $('form').on('submit', function() {
        var $form = $(this);
        var formDataArray = $form.serializeArray();
        var additionalData = serializeArrayToObject(formDataArray);
        var eventData = {
            action: 'ga_ip2c_event',
            type: 'form_submission',
            category: 'Form Submission',
            label: $form.attr('id') || 'Unnamed Form',
            page: window.location.pathname,
            title: document.title,
            additional: additionalData
        };

        enqueueRequest({
            url: ga_ip2c_ajax.ajax_url,
            method: 'POST',
            data: $.extend(eventData, { security: ga_ip2c_ajax.nonce }),
        });
    });

    // Event handler for general link clicks
    $('a').on('click', function(event) {
        var href = $(this).attr('href');
        if (href && href.startsWith('#')) {
            // Track the click on the anchor link
            var eventData = {
                action: 'ga_ip2c_event',
                type: 'anchor_click',
                category: 'Anchor Link Click',
                label: $(this).text() || href,
                page: window.location.pathname,
                title: document.title,
                additional: {
                    anchor: href
                }
            };
           
            enqueueRequest({
                url: ga_ip2c_ajax.ajax_url,
                method: 'POST',
                data: $.extend(eventData, { security: ga_ip2c_ajax.nonce })
            });


        }
        else if (href && href.match(/\.(pdf|docx|xlsx|zip|rar|jpg|png)$/i)) {

            // Track the click on file downloads
            var eventData = {
                action: 'ga_ip2c_event',
                type: 'download',
                category: 'Download',
                label: href,
                page: window.location.pathname,
                title: document.title
            };

            enqueueRequest({
                url: ga_ip2c_ajax.ajax_url,
                method: 'POST',
                data: $.extend(eventData, { security: ga_ip2c_ajax.nonce }),
            });
        } 
        else {

            
            // Track the click on general links
            var eventData = {
                action: 'ga_ip2c_event',
                type: 'link_click',
                category: 'Link Click',
                label: $(this).text() || href,
                page: window.location.pathname,
                title: document.title,
                additional: {
                    url: href
                }
            };

            enqueueRequest({
                url: ga_ip2c_ajax.ajax_url,
                method: 'POST',
                data: $.extend(eventData, { security: ga_ip2c_ajax.nonce }),
            });
        }           
    });

    // Event handler for tracking scroll depth
    $(window).on('scroll', function() {
        var scrollDepth = Math.round($(window).scrollTop() / ($(document).height() - $(window).height()) * 100);
        if (scrollDepth % 25 === 0) { // Track every 25% scroll depth
            var eventData = {
                action: 'ga_ip2c_event',
                type: 'scroll_depth',
                category: 'Scroll',
                label: scrollDepth + '%',
                page: window.location.pathname,
                title: document.title,
                additional: { depth: scrollDepth }
            };

            enqueueRequest({
                url: ga_ip2c_ajax.ajax_url,
                method: 'POST',
                data: $.extend(eventData, { security: ga_ip2c_ajax.nonce })
            });
        }
    });

    // Track video interactions (YouTube API example)
    function onPlayerStateChange(event) {
        var eventData;
        if (event.data == YT.PlayerState.PLAYING) {
            eventData = {
                action: 'ga_ip2c_event',
                type: 'video_play', // Event type
                category: 'Video',
                label: event.target.getVideoData().title,
                page: window.location.pathname,
                title: document.title
            };
        } else if (event.data == YT.PlayerState.PAUSED) {
            eventData = {
                action: 'ga_ip2c_event',
                type: 'video_pause', // Event type
                category: 'Video',
                label: event.target.getVideoData().title,
                page: window.location.pathname,
                title: document.title
            };
        } else if (event.data == YT.PlayerState.ENDED) {
            eventData = {
                action: 'ga_ip2c_event',
                type: 'video_end', // Event type
                category: 'Video',
                label: event.target.getVideoData().title,
                page: window.location.pathname,
                title: document.title
            };
        }
        enqueueRequest({
            url: ga_ip2c_ajax.ajax_url,
            method: 'POST',
            data: $.extend(eventData, { security: ga_ip2c_ajax.nonce })
        });
    }

    // Initialize YouTube IFrame Player API
    function onYouTubeIframeAPIReady() {
        $('iframe[src*="youtube.com"]').each(function() {
            var player = new YT.Player(this, {
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        });
    }

    // Load YouTube IFrame Player API asynchronously
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);




    // Other event handlers can be added here
});