window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};

window.df.workgroup.Util = (function(){

    var now_date;
    function load_json(url, method, callback, $data){

        now_date = new Date();
        console.log("\n----- << START LOAD >> xhr.url : ", url, "\n");

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
            //console.log("xhr.readyState : ", xhr.readyState);
            //console.log("xhr.status : ", xhr.status);
        };

        // Callback function
        xhr.onloadend = function (response) {

            //console.log("xhr.onloadend : " , response);

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
                console.log("\n----- << COMP LOAD >> ----- xhr.onloadend (Success) duration : " , (((new Date()).getTime() - now_date.getTime())/1000) + "sec\n\n" );
                console.log("xhr.onloadend (Success) response : \n" , response);
                console.log("xhr.onloadend (Success) responseText(JSON) : \n" , JSON.parse(response.target.responseText));
                console.log("\n----- << COMP LOAD >> -----\n\n" );
                //console.log("xhr.onloadend (Success) xhr : " , xhr);
                //console.log("xhr.onloadend (Success) response.target.responseText : " , JSON.parse(response.target.responseText));
                setTimeout(function(){
                    callback(response);
                }, 10);
            }
        };
        // Send the collected data as JSON
        xhr.send(data);
    }

    return {
        load_json: load_json
    }

})();