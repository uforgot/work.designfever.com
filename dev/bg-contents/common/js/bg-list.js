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
    };

    var _makeItem = function(data){

        var group = document.createDocumentFragment();

        for(var i=0 ; i<data.list.length ; i++){

            var list = data.list[i];

            var elem = document.createElement('li');
            elem.classList.add('item');

            var link = document.createElement('a');
            // link.href = "../"+list.url;
            link.href = list.url;
            link.target = "_blank";

            var title = document.createElement('p');
            title.classList.add('item-title');
            title.innerHTML = list.seq;


            var img = document.createElement('img');
            // img.src = list.bg_thumb;
            img.src = list.bg_thumb_s;

            link.appendChild(img);
            elem.appendChild(link);
            elem.appendChild(title);
            group.appendChild(elem);
        }

        return group;
    };


    return {
        init: _init
    }


};