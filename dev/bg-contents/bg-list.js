var BackgroundList = function(json_data){

    var _bg_contents_data = json_data.preset.bg_contents;

    var _dataTitleArr = []
    var _dataArr = []

    var _init = function(){
        _setElement();
    }

    var _setElement = function(){

        var wrap = document.createElement('ul');
        var obj = json_data.preset.bg_contents;

        for(var key in obj){
            _dataTitleArr.push(key);
            _dataArr.push(obj[key]);
            wrap.appendChild(_makeItem(obj[key]));
            // console.log(key, " / ", obj[key])
        }
        document.getElementById('list-wrapper').appendChild(wrap);

        /*
        var weather = _makeItem(_bg_contents_data.weather);
        var birthday = _makeItem(_bg_contents_data.birthday);
        var artwork = _makeItem(_bg_contents_data.artwork);
        var custom = _makeItem(_bg_contents_data.custom);

        wrap.appendChild(weather);
        wrap.appendChild(birthday);
        wrap.appendChild(artwork);
        wrap.appendChild(custom);*/


    };

    var _makeItem = function(data){

        var group = document.createDocumentFragment();

        for(var i=0 ; i<data.list.length ; i++){

            var list = data.list[i];

            var elem = document.createElement('li');
            elem.classList.add('item');

            var link = document.createElement('a');
            link.href = "../"+list.url;
            link.target = "_blank";

            var img = document.createElement('img');
            img.src = "../bg-contents/artwork/001_Gooey-df/images/thumbnail-1200.jpg";

            link.appendChild(img);
            elem.appendChild(link);
            group.appendChild(elem);
        }

        return group;
    };


    return {
        init: _init
    }


};