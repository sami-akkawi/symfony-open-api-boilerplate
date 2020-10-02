window.onload = function() {
    var currentUrl = window.location.origin;
    var apiBasePath = currentUrl + '/v1';

    window.ui = SwaggerUIBundle({
        url: apiBasePath + "/specifications.json",
        imageUrl: currentUrl + '/images/symfony-openapi-boilerplate.jpg',
        imageAlt: 'Symfony OpenApi Boilerplate',
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],
        plugins: [
            SwaggerUIBundle.plugins.DownloadUrl
        ],
        layout: "StandaloneLayout"
    })
}