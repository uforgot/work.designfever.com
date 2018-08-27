module.exports = function(){

    // Get the modal
    var _modal = document.getElementById('id_modal');
    var _modal_txt = document.getElementById('id_modal_txt');
    var _btn_close = document.getElementById('id_btn_close_modal');

    var _ID_TIMEOUT = 0;
    function _init(){
        // When the user clicks on <span> (x), close the modal
        _btn_close.onclick = function() {
            _closeModal();
        };

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == _modal) {
                _closeModal();
            }
        };
    }

    function _showModal(txt){
        clearTimeout(_ID_TIMEOUT);

        _modal_txt.textContent = txt;
        df.lab.Util.removeClass(_modal, window.df.workgroup.Preset.class_name.showIn);
        _modal.style.display = "block";

        _ID_TIMEOUT = setTimeout(function(){
            df.lab.Util.addClass(_modal, window.df.workgroup.Preset.class_name.showIn);
        }, 100);

        _modal.setAttribute("tabindex", "-1");
        _modal.focus();
        _modal.removeAttribute("tabindex");
    }

    function _closeModal(){
        clearTimeout(_ID_TIMEOUT);
        df.lab.Util.removeClass(_modal, window.df.workgroup.Preset.class_name.showIn);
        _modal.style.display = "none";
        _dispatchOnLoad();
    }

    function _dispatchOnLoad(){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL);
        document.dispatchEvent(event);
    }

    return {
        init: _init,
        showModal: _showModal,
        closeModal: _closeModal
    }
};