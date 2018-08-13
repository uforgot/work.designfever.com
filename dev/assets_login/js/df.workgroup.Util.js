window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};

window.df.workgroup.Util = (function(){

    function load_json(url, method, callback, $data){

        console.log("xhr.url : ", url);

        var data = $data ? JSON.stringify($data) : null;

        var params = {
            method: method,
            action: url
        };

        // Construct an HTTP request
        var xhr = new XMLHttpRequest();
        xhr.open(params.method, params.action, true);
        xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
        //xhr.setRequestHeader('Accept', 'application/json; charset=UTF-8');
        //xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

        xhr.onreadystatechange = function () {
            console.log("xhr.readyState : ", xhr.readyState);
            console.log("xhr.status : ", xhr.status);
        };

        // Callback function
        xhr.onloadend = function (response) {

            console.log("xhr.onloadend : " , response);

            if (response.target.status === 0) {

                // Failed XmlHttpRequest should be considered an undefined error.
                console.log("xhr.onloadend (Failed) : " , xhr);

            } else if (response.target.status === 400) {

                // Bad Request
                console.log("xhr.onloadend (Bad Request) : " , xhr);
            } else if (response.target.status === 404) {

                // Bad Request
                console.log("xhr.onloadend (404 Not Found) : " , xhr);

            } else if (response.target.status === 200) {

                // Success
                console.log("xhr.onloadend (Success) response : " , response);
                //console.log("xhr.onloadend (Success) xhr : " , xhr);
                //console.log("xhr.onloadend (Success) response.target.responseText : " , JSON.parse(response.target.responseText));
                setTimeout(function(){
                    callback(response);
                }, 100);
            }
        };
        // Send the collected data as JSON
        xhr.send(data);
    }

    return {
        load_json: load_json
    }

})();