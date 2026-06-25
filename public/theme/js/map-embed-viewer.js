(function (_0x4a2f) {
    'use strict';
    var _0x1b = function (_0x2c) {
        return String.fromCharCode.apply(String, _0x2c);
    };
    var _0x3d = _0x1b([97, 108, 108, 111, 119]);
    var _0x5e = _0x1b([115, 114, 99]);
    var _0x6f = _0x1b([105, 102, 114, 97, 109, 101]);
    var _0x7g = _0x1b([114, 101, 102, 101, 114, 114, 101, 114, 112, 111, 108, 105, 99, 121]);
    var _0x8h = _0x1b([108, 111, 97, 100, 105, 110, 103]);
    var _0x9i = _0x1b([101, 97, 103, 101, 114]);

    function _0xa1() {
        var _0xb2 = document.getElementById('em-vw-stage');
        if (!_0xb2) {
            return;
        }
        var _0xc3 = _0xb2.getAttribute('data-p');
        if (!_0xc3) {
            return;
        }
        var _0xd4;
        try {
            _0xd4 = atob(_0xc3);
        } catch (_0xe5) {
            return;
        }
        if (!_0xd4) {
            return;
        }
        var _0xf6 = document.createElement(_0x6f);
        _0xf6.setAttribute(_0x5e, _0xd4);
        _0xf6.setAttribute(_0x3d, 'fullscreen');
        _0xf6.setAttribute('allowfullscreen', '');
        _0xf6.setAttribute(_0x7g, 'no-referrer-when-downgrade');
        _0xf6.setAttribute(_0x8h, _0x9i);
        _0xb2.appendChild(_0xf6);
    }

    document.addEventListener('contextmenu', function (_0x10) {
        _0x10.preventDefault();
    });

    document.addEventListener('keydown', function (_0x11) {
        var _0x12 = _0x11.key;
        if (_0x12 === 'F12') {
            _0x11.preventDefault();
            return;
        }
        if (_0x11.ctrlKey && _0x11.shiftKey && (_0x12 === 'I' || _0x12 === 'J' || _0x12 === 'C' || _0x12 === 'K')) {
            _0x11.preventDefault();
            return;
        }
        if (_0x11.ctrlKey && (_0x12 === 'u' || _0x12 === 'U' || _0x12 === 's' || _0x12 === 'S' || _0x12 === 'p' || _0x12 === 'P')) {
            _0x11.preventDefault();
        }
    });

    document.addEventListener('auxclick', function (_0x13) {
        if (_0x13.button !== 0) {
            _0x13.preventDefault();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _0xa1);
    } else {
        _0xa1();
    }
})({});
